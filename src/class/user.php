<?php
require_once FUNC_DIR.'/hash_equals.php';

/**
 * 虎绿林WAP6 用户操作
 */
class user extends userinfo
{
    //错误ID开始
    const ERROR_USER_NOT_ACTIVE = 80403;
    //错误ID结束
    
    protected static $sid; //sid到uid对应关系的缓存
    protected static $safety; //用户安全信息缓存

    protected $update = array(); //记录需要更新的信息，以便在__destruct时一同写到数据库（如果update不为空数组，则user对象禁止更换用户）
    public $err = NULL; //start()方法捕获的错误
    protected $at = NULL; //注册的at消息元信息数组
    protected $atUid = NULL; //at消息收件人uid数组

    /*加密用户的密码*/
    protected static function mkpass($pass)
    {
        return md5(USER_PASS_KEY . md5($pass) . USER_PASS_KEY);
    }

    /*产生sid*/
    protected static function mksid($uid, $name, $pass)
    {
        return str_shuffle(url::b64ec(md5(str::random_bytes(128), true), true)) . url::b64ec(pack('V', $uid));
    }

    /*生成info数据的字符串*/
    protected static function makeinfo($uid)
    {
        return serialize(self::$info[$uid]);
    }

    /*生成safety数据的字符串*/
    protected static function makesafety($uid)
    {
        return serialize(self::$safety[$uid]);
    }


    /*解析用户的safety数据*/
    protected static function parsesafety($uid, $info)
    {
        $info = unserialize($info);
        if ($info === NULL) $info = array();
        self::$safety[$uid] = $info;
    }

    /**
     * 快速初始化用户登录，并设置$user变量到模板引擎
     */
    public function start($tpl = null, $page = null, $sid = null)
    {
        if ($page === null) {
            global $PAGE;
            $page = $PAGE;
        }
        if (!is_object($page)) throw new userexception('参数$page类型不正确。$page必须是一个Page类的对象。', 2500);
        if ($sid === null) $sid = $page->sid;
        $e = TRUE;
        try {
            $this->loginbysid($sid);
        } catch (userexception $e) {
            $this->err = $e;
        }
        if ($tpl === null) $tpl = $page->tpl();
        $tpl->assign('USER', $this);
        $tpl->assign('user', $this);
        return $e;
    }


    /**
     * 设置身份验证Cookie（sid）
     */
    public function setCookie()
    {
        if (!self::$data[$this->uid]['islogin']) throw new userexception('用户未登录，不能设置身份验证Cookie。', 2503);
        return page::setCookie('sid', self::$data[$this->uid]['sid'], $_SERVER['REQUEST_TIME'] + DEFAULT_LOGIN_TIMEOUT);
    }

    /**
     * 写用户的info数据
     * 参数：
     *   $index  用.分隔的索引
     *   $data  要写入的数据
     * 返回：
     *   成功返回TRUE，失败返回FALSE，未更改返回NULL
     * 注意：
     *   本方法不会立即把配置写入数据库，只有调用save()方法或者对象被销毁时才会把数据写入数据库。
     *   一般情况下，你不需要关注数据什么时候被写入数据库，user类会在所有更改完成后自动把所有更改一次性写入数据库。
     *   但如果有需要，你还是可以调用save()方法立即把所有更改同步到数据库。如：
     * $user->setinfo('a',1);
     * $user->setinfo('b',2);
     * $user->save();
     */
    public function setinfo($index, $data)
    {
        if (!self::$data[$this->uid]['islogin']) throw new userexception('用户未成功登录，不能写info数据。', 3503);
        $set =& self::$info[$this->uid];
        if ($set === NULL) {
            $this->getinfo();
            $set =& self::$info[$this->uid];
        }

        if ($index !== null) {
            $index = explode('.', $index);
            foreach ($index as $key) {
                $set =& $set[$key];
            }
        }
        if ($set === $data) return NULL;
        $set = $data;
        $this->update['info'] = true;
        return TRUE;
    }

    /*取得用户的安全数据*/
    public function getSafety($index = null)
    {
        $set = self::$safety[$this->uid];
        if ($set === NULL) {
            static $rs;
            if (!$rs) {
                $db = self::conn(true);
                $rs = $db->prepare('SELECT `safety` FROM `' . DB_A . 'user` WHERE `uid`=?');
            }
            if (!$rs || !$rs->execute(array($this->uid))) return FALSE;
            $data = $rs->fetch(db::ass);
            self::parseSafety($this->uid, $data['safety']);
            $set = self::$safety[$this->uid];
        }
        if ($index === null) return $set;
        $index = explode('.', $index);
        foreach ($index as $key) {
            $set = $set[$key];
        }
        return $set;
    }

    /**
     * 写用户的安全数据
	 * 
	 * 只传递一个参数时删除对应的index
     */
    public function setSafety($index, $data = null)
    {
        $set =& self::$safety[$this->uid];
        if ($set === NULL) {
            $this->getSafety();
            $set =& self::$safety[$this->uid];
        }

        if ($index !== null) {
            $index = explode('.', $index);
            foreach ($index as $key) {
                $set =& $set[$key];
            }
        }
        if ($set === $data) return NULL;
        $set = $data;
        $this->update['safety'] = true;
        return TRUE;
    }

    /*
    * 判断是否允许改变用户
    * 若$this->update非空，则不允许
    * 允许返回TRUE，不允许则抛出一个异常
    */
    protected function canChange()
    {
        if (empty($this->update)) return TRUE;
        else throw new userexception('当前用户的更新尚未保存，不能切换用户。', 1503);
    }

    /**
     * 跳转到登录页并结束程序
     */
    public function gotoLogin($checkLogin = false)
    {
        global $PAGE;

        if ($checkLogin && $this->islogin) {
            return TRUE;
        }
        header('Location: user.login.' . $PAGE->bid . '?u=' . urlencode($PAGE->geturl()));
        exit;
    }

    /**
     * 用户登录
     */
    public function login($name, $pass, $isuid = false, $getinfo = true, $getsafety = false)
    {
        $this->canchange();
        $this->uid = NULL;
        if ($isuid) {
            self::checkUid($name);
            $uid = $name;
        } else {
            self::checkName($name);
            $uid = self::$name[$name];
        }
        if ($uid !== NULL) {
            if (self::$data[$uid]['islogin'] === TRUE) {
                $this->uid = $uid;
                return TRUE;
            } elseif (self::$data[$uid] === FALSE) {
                throw new userexception(($isuid ? '用户ID' : '用户名') . " \"$name\" 不存在。", 1404);
            }
        }
        $pass = self::mkpass($pass);
        $sql = 'SELECT `active`,`uid`,`name`,`pass`,`mail`,`sid`,`sidtime`,`regtime`,`acctime`';
        if ($getinfo) {
            $sql .= ',`info`';
        }
        if ($getsafety) {
            $sql .= ',`safety`';
        }
        $sql .= ' FROM `' . DB_A . 'user` WHERE ';
        if ($isuid) {
            $sql .= '`uid`=?';
        } else {
            $sql .= '`name`=?';
        }
        $db = self::conn(true);
        $rs = $db->prepare($sql);
        if (!$rs || !$rs->execute(array($name))) throw new PDOException('数据库查询错误，SQL' . ($rs ? '预处理' : '执行') . '失败。', $rs ? 21 : 22);
        $data = $rs->fetch(db::ass);
        if (!isset($data['uid'])) {
            self::$data[$uid] = FALSE;
            throw new userexception(($isuid ? '用户ID' : '用户名') . " \"$name\" 不存在。", 1404);
        }
        if (!hash_equals($data['pass'], $pass)) {
            throw new userexception('密码错误。', 1403);
        }
        $this->uid = $data['uid'];
        if ($getinfo) {
            self::parseinfo($this->uid, $data['info']);
            unset($data['info']);
        }
        if ($getsafety) {
            self::parsesafety($this->uid, $data['safety']);
            unset($data['safety']);
        }

        if ($data['sidtime'] + DEFAULT_LOGIN_TIMEOUT < $_SERVER['REQUEST_TIME']) {
            $data['sid'] = self::mksid($data['uid'], $data['name'], $data['pass']);
            $data['sidtime'] = $_SERVER['REQUEST_TIME'];
            $this->update['sidtime'] = true;
            $this->update['sid'] = true;
        }
        $data['islogin'] = true;
        $data['acctime'] = $_SERVER['REQUEST_TIME'];
        $this->update['acctime'] = true;
        self::$data[$this->uid] = $data;
        self::$name[$data['name']] = $this->uid;
        self::$sid[$data['sid']] = $this->uid;

        if (!$data['active']) {
            $data['islogin'] = false;
            self::$data[$uid] = $data;
            throw new userexception("用户未激活", self::ERROR_USER_NOT_ACTIVE);
        }

        return TRUE;
    }

	/**
	 * 模拟用户登录，用于实现管理员操作
	 */
	public function virtualLogin() {
		self::$data[$this->uid]['islogin'] = true;
	}

    /**
     * 通过邮箱登录
     */
    public function loginByMail($mail, $pass, $getinfo = true, $getsafety = false)
    {
        $sql = 'SELECT `uid` FROM `' . DB_A . 'user` WHERE `mail`=?';
        $db = self::conn();
        $rs = $db->prepare($sql);

        if (!$rs || !$rs->execute([$mail])) {
            throw new PDOException('数据库查询错误，SQL' . ($rs ? '预处理' : '执行') . '失败。', $rs ? 21 : 22);
        }

        $uid = $rs->fetch(db::num);

        if (empty($uid)) {
            throw new UserException('该邮箱未绑定任何用户。', 5404);
        }

        return $this->login($uid[0], $pass, true, $getinfo, $getsafety);
    }

    /**
     * 通过手机登录
     */
    public function loginByPhone($phone, $pass, $getinfo = true, $getsafety = false)
    {
        $phone = str::regularPhoneNumber($phone);

        $sql = 'SELECT `uid` FROM `' . DB_A . 'user` WHERE `regphone`=?';
        $db = self::conn();
        $rs = $db->prepare($sql);

        if (!$rs || !$rs->execute([$phone])) {
            throw new PDOException('数据库查询错误，SQL' . ($rs ? '预处理' : '执行') . '失败。', $rs ? 21 : 22);
        }

        $uid = $rs->fetch(db::num);

        if (empty($uid)) {
            throw new UserException('该手机号码未绑定任何用户。', 6404);
        }

        return $this->login($uid[0], $pass, true, $getinfo, $getsafety);
    }


    /**
     * 通过sid登录
     * 参数：
     *   $sid  用户的sid
     * 返回：
     *   成功返回TRUE，失败抛出异常
     */
    public function loginBySid($sid, $getinfo = true)
    {
        $this->canchange();
        $this->uid = NULL;
        if (empty($sid)) throw new userexception('sid为空。', 50);
        if (strlen($sid) < 20) throw new userexception('sid过短。', 51);
        if (strlen($sid) > 64) throw new userexception('sid过长。', 52);
        if (!preg_match('/^[a-zA-Z0-9_\\-]+$/s', $sid)) throw new exception('sid格式不正确。', 53);
        $uid = self::$sid[$sid];
        if ($uid !== NULL) {
            $this->uid = $uid;
            if ($uid !== FALSE) return TRUE;
            else throw new userexception('sid不存在。', 54);

        }
        static $rs, $x_getinfo;
        if (!$rs || $getinfo != $x_getinfo) {
            $db = self::conn(true);
            $rs = $db->prepare('SELECT `active`,`uid`,`name`,`mail`,`sid`,`sidtime`,`regtime`,`acctime`' . ($getinfo ? ',`info`' : '') . ' FROM `' . DB_A . 'user` WHERE `sid`=?');

            $x_getinfo = $getinfo;
        }
        if (!$rs || !$rs->execute(array($sid))) throw new PDOException('数据库查询失败，SQL' . ($rs ? '执行' : '预处理') . '失败。', $rs ? 21 : 22);
        $data = $rs->fetch(db::ass);

        if (!isset($data['uid'])) {
            self::$sid[$sid] = FALSE;
            throw new userexception('sid不存在。', 54);
        }
        $this->uid = $data['uid'];
        if ($getinfo) {
            self::parseinfo($this->uid, $data['info']);
            unset($data['info']);
        }
        self::$name[$data['name']] = $this->uid;

        self::$sid[$data['sid']] = $this->uid;
        if ($data['sidtime'] + DEFAULT_LOGIN_TIMEOUT > $_SERVER['REQUEST_TIME']) {
            $data['acctime'] = $_SERVER['REQUEST_TIME'];
            $this->update['acctime'] = true;

            if (!$data['active']) {
                $data['islogin'] = false;
                self::$data[$this->uid] = $data;
                throw new userexception("用户未激活", self::ERROR_USER_NOT_ACTIVE);
            } else {
                $data['islogin'] = true;
                self::$data[$this->uid] = $data;
                return TRUE;
            }

        } else {
            $data['islogin'] = FALSE;
            self::$data[$this->uid] = $data;
            throw new userexception('sid已过期。', 55);
        }
    }

    /**
     * 新用户注册
     * 参数：
     * $name  用户名
     * $pass  密码
     */
    public function reg($name, $pass, $mail)
    {
        $this->canchange();
        $this->uid = NULL;

        self::checkname($name);
        if ($this->name($name)) throw new userexception("用户名 \"$name\" 已存在，请更换一个。", 12);
        self::checkmail($mail);
        if ($this->mail($mail)) throw new userexception("邮箱 \"$mail\" 已存在，请更换一个。", 12);
        $pass = self::mkpass($pass);
        $time = $_SERVER['REQUEST_TIME'];
        $db = self::conn(true);
        $rs = $db->query('SELECT max(`uid`) FROM `' . DB_A . 'user`');
        if (!$rs) $id = 1;
        else {
            $rs = $rs->fetch(db::num);
            $id = $rs[0] + 1;
        }
        $sid = self::mksid($id, $name, $pass);
        
        //若短信验证打开，则默认不激活
        $active = SECCODE_SMS_ENABLE ? 0 : 1;
        
//实现读写分离：获得一个可以写入的数据库连接
        $db = self::conn();
        $rs = $db->prepare('INSERT INTO `' . DB_A . 'user`(`name`,`pass`,`sid`,`mail`,`regtime`,`sidtime`,`acctime`,`active`) values(?,?,?,?,?,?,?,?)');
        if (!$rs || !$rs->execute(array($name, $pass, $sid, $mail, $time, $time, $time, $active))) throw new PDOException('数据库写入错误，SQL' . ($rs ? '执行' : '预处理') . '失败。', $rs ? 21 : 22);
        $uid = $db->lastinsertid();
        $this->uid = $uid;
        self::$data[$uid] = array('uid' => $uid, 'name' => $name, 'mail' => $mail, 'pass' => $pass, 'sid' => $sid, 'regtime' => $time, 'sidtime' => $time, 'acctime' => $time, 'islogin' => true);
        self::$name[$name] = $uid;
        self::$sid[$sid] = $uid;
        return true;
    }

    /**
     * 取得指定用户名的信息
     * 若$this->update非空，则禁止操作
     */
    public function name($name, $getinfo = false)
    {
        $this->canchange();

        return parent::name($name, $getinfo);
    }

    /**
     * 取得指定邮箱的用户信息
     * 若$this->update非空，则禁止操作
     */
    public function mail($mail, $getinfo = false)
    {
        $this->canchange();

        return parent::mail($mail, $getinfo);
    }

    /**
     * 取得指定uid的信息，并存储在属性内。
     * 若$this->update非空，则禁止操作
     */
    public function uid($uid, $getinfo = false)
    {
        $this->canchange();

        return parent::uid($uid, $getinfo);
    }

    /**
     * 把用户信息的更改保存到数据库
     */
    public function save()
    {
        $up =& $this->update;
        $uid = $this->uid;
        if (empty($up)) return NULL;
        $opt = array();
        $data = array();
        if ($up['name']) {
            $opt[] = '`name`=?';
            $data[] = self::$data[$uid]['name'];
            unset($up['name']);
        }
        if ($up['pass']) {
            $opt[] = '`pass`=?';
            $data[] = self::$data[$uid]['pass'];
            unset($up['pass']);
        }
        if ($up['sid']) {
            $opt[] = '`sid`=?';
            $data[] = self::$data[$uid]['sid'];
            unset($up['sid']);
        }
        if ($up['sidtime']) {
            $opt[] = '`sidtime`=?';
            $data[] = self::$data[$uid]['sidtime'];
            unset($up['sidtime']);
        }
        if ($up['acctime']) {
            $opt[] = '`acctime`=?';
            $data[] = self::$data[$uid]['acctime'];
            unset($up['acctime']);
        }
        if ($up['info']) {
            $opt[] = '`info`=?';
            $data[] = self::makeinfo($uid);
            unset($up['info']);
        }
        if ($up['safety']) {
            $opt[] = '`safety`=?';
            $data[] = self::makesafety($uid);
            unset($up['safety']);
        }
        if (empty($opt)) return FALSE;
        $sql = 'UPDATE `' . DB_A . 'user` SET ' . implode(',', $opt) . ' WHERE `uid`=?';
        $data[] = $uid;
        $db = self::conn();
        $rs = $db->prepare($sql);

        if (!$rs || !$rs->execute($data)) throw new PDOException('数据库写入错误，SQL' . ($rs ? '预处理' : '执行') . '失败。', $rs ? 21 : 22);
        return TRUE;
    }


    public function __destruct()
    {
        $this->save();
        if (!empty($this->update)) throw new userexception('更新未全部写入数据库。未写入项：' . implode(',', $this->update), 100);
        //发at消息
        $this->sendAt();
    }

    /*
    *退出登录
    */
    function logout()
    {
        global $PAGE;
        page::setcookie('sid', false, -1);
        header('Location: ' . $_SERVER['PHP_SELF'] . '/user.login.' . $PAGE->bid);
    }

    public function regAt($pos, $url, $msg)
    {
        if (!$this->islogin) {
            throw new userException('用户未登录，不能注册at消息！', 403);
        }

		$ubb = new ubbparser;
        $msg = $ubb->parse($msg);
        $this->at = array('pos' => $pos, 'url' => $url, 'msg' => $msg);
    }

    public function at($tag)
    {
        static $atUid = array();

        if ($this->hasPermission(self::PERMISSION_BLOCK_ATINFO)) {
            throw new UserException("您被举报通过@功能骚扰其他用户，已被禁止使用@。若要正常发言，请删除所有的@标记。", 403);
        }

        $tag = str_replace('＃', '#', trim($tag));
        $uinfo = new userinfo;
        if ($tag[0] == '#') {
            $uinfo->uid(substr($tag, 1));
        } else {
            $uinfo->name($tag);
        }

        if ($uinfo->uid < 1) {
            return false;
        }

        $uid = $uinfo->uid;

        // 检查是否被屏蔽
        $userRelationshipService = new UserRelationshipService($this);
        if($userRelationshipService->isBlock($uid, $this->uid)) {
            throw new UserException('用户['. $uinfo->name .']已屏蔽了您的At消息', 403);
        }


        if ($atUid[$uid] || !$this->islogin) {
            return $uid;
        }

        $this->atUid[] = $uid;
        $atUid[$uid] = true;

        if ($this->at !== NULL) {
            $this->sendAt();
        }

        return $uid;
    }

    public function sendAt()
    {
        static $maxSize = 5;
        static $nowSize = 0;

        if ($this->at === NULL) {
            return false;
        }

        $ubb = new ubbParser();
        $content = $ubb->createAtMsg($this, $this->at['pos'], $this->at['url'], $this->at['msg']);

        /*$content = <<<UBB
        {$this->name} 在 《链接：{$this->at['url']}，{$this->at['pos']}》 at你：
        [div=border:1px solid #ff0000]
        {$this->at['msg']}
        [/div]
        UBB;*/

        $msg = new msg;
        foreach ($this->atUid as $i => $uid) {
            $nowSize ++;
            if ($nowSize > $maxSize) {
                return false;
            }

            $msg->send_msg($this->uid, '1', $uid, $content);
            unset($this->atUid[$i]);
        }

        return true;
    }

    public function getRegPhone()
    {
        $db = self::conn(true);
        $rs = $db->prepare('SELECT `regphone` FROM `' . DB_A . 'user` WHERE uid=?');

        if (!$rs || !$rs->execute([$this->uid])) {
            return false;
        }

        $phone = $rs->fetch(db::num);

        return $phone[0];
    }

    public function bindPhoneRequest($phoneNumber)
    {
        $uinfo = new UserInfo();

        $regPhone = $this->getRegPhone();

        if ($regPhone) {
            throw new UserException('该帐号已绑定手机。', 7400);
        }

        if ($uinfo->regPhone($phoneNumber)) {
            throw new UserException('该手机号已绑定其他用户。', 7403);
        }

        $secCode = new SecCode($this);
        $ok = $secCode->sendToPhone($phoneNumber);

        if (false === $ok) {
            throw new UserException('短信验证码发送失败，请稍后再试。', 7500);
        }

        $this->setSafety('user.regPhone', $phoneNumber);

        return TRUE;
    }

    public function bindPhoneVerify($code)
    {
        $regPhone = $this->getSafety('user.regPhone');
        $secCode = new SecCode($this);
        $ok = $secCode->checkFromPhone($regPhone, $code);

        if (false === $ok) {
            throw new UserException('验证码输入错误', 7404);
        }

        $db = self::conn(false);
        $rs = $db->prepare('UPDATE `' . DB_A . 'user` SET `regphone`=?,`active`=1 WHERE `uid`=?');

        $this->setSafety('user.regPhone');

        $ok = $rs->execute([$regPhone, $this->uid]);

        unset(self::$sid[$this->sid]);

        return $ok;
    }

    public function resetPasswordRequest()
    {
        $regPhone = $this->getRegPhone();

        $secCode = new SecCode($this);
        $ok = $secCode->sendToPhone($regPhone);

        if (false === $ok) {
            throw new UserException('短信验证码发送失败，请稍后再试。', 7500);
        }

        return TRUE;
    }

    public function resetPasswordVerify($code, $newPassword)
    {
        $regPhone = $this->getRegPhone();
        $secCode = new SecCode($this);
        $ok = $secCode->checkFromPhone($regPhone, $code);

        if (false === $ok) {
            throw new UserException('验证码输入错误', 7404);
        }

        $hashedPassword = self::mkpass($newPassword);

        $db = self::conn(false);
        $sql = 'UPDATE ' . DB_A . 'user SET pass=?,sidtime=0 WHERE uid=?';
        $rs = $db->prepare($sql);

        if (!$rs) {
            throw new userexception('数据库预处理失败。', 10500);
        }

        $ok = $rs->execute([$hashedPassword, $this->uid]);

        if (!$ok) {
            throw new userexception('数据库写入失败。', 10500);
        }

        return true;
    }

    public function changeName($newName)
    {
        if (!$this->islogin) {
            throw new userexception('用户未登录，不能改名。', 9403);
        }

        //检查用户名合法性，不合法则抛出异常
        $this->checkName($newName);

        $uinfo = new UserInfo();
        $exists = $uinfo->name($newName);

        if ($exists) {
            throw new userexception('该用户名已被他人使用。', 9410);
        }

        $sql = 'UPDATE ' . DB_A . 'user SET name=? WHERE uid=?';
        $db = self::conn(false);

        $rs = $db->prepare($sql);

        if (!$rs) {
            throw new userexception('数据库预处理失败。', 9500);
        }

        $ok = $rs->execute([$newName, $this->uid]);

        if (!$ok) {
            throw new userexception('数据库写入失败。', 9500);
        }

        return true;
    }

    public function changePassword($oldPassword, $newPassword)
    {
        if (!$this->islogin) {
            throw new userexception('用户未登录，不能修改密码。', 9403);
        }

        $ok = self::checkPassword($this->uid, $oldPassword);

        if (!$ok) {
            throw new UserException('原密码错误。', 10403);
        }

        $hashedPassword = self::mkpass($newPassword);

        $db = self::conn(false);
        $sql = 'UPDATE ' . DB_A . 'user SET pass=?,sidtime=0 WHERE uid=?';
        $rs = $db->prepare($sql);

        if (!$rs) {
            throw new userexception('数据库预处理失败。', 10500);
        }

        $ok = $rs->execute([$hashedPassword, $this->uid]);

        if (!$ok) {
            throw new userexception('数据库写入失败。', 10500);
        }

        return true;
    }

    public static function checkPassword($uid, $password)
    {
        $db = self::conn(false);
        $sql = 'SELECT pass FROM ' . DB_A . 'user WHERE uid=?';
        $rs = $db->prepare($sql);

        if (!$rs) {
            throw new userexception('数据库预处理失败。', 10500);
        }

        $ok = $rs->execute([$uid]);

        if (!$ok) {
            throw new userexception('数据库写入失败。', 10500);
        }

        $result = $rs->fetch(db::ass);

        $hashedPwd = $result['pass'];

        return self::mkpass($password) === $hashedPwd;
    }

    /*class end*/
}
