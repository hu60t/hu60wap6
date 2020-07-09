<?php

/**
 * 虎绿林WAP6 读取用户信息
 */
class userinfo implements ArrayAccess
{
    //权限列表开始

    /*** 帖子编辑权限 */
    const PERMISSION_EDIT_TOPIC = 1;

    /*** 禁止使用div和span标签 */
    const PERMISSION_UBB_DISABLE_STYLE = 2;

    /*** 用户被禁言 */
    const PERMISSION_BLOCK_POST = 4;

    /*** 用户被禁止@他人 */
    const PERMISSION_BLOCK_ATINFO = 8;

    /*** 设置禁言的权限 */
    const PERMISSION_SET_BLOCK_POST = 16;

    /*** 帖子加精权限 */
    const PERMISSION_SET_ESSENCE_TOPIC = 32;

	/*** 用户发言需要审核 */
	const PERMISSION_POST_NEED_REVIEW = 64;

	/*** 审核用户发言 */
	const PERMISSION_REVIEW_POST = 128;

    //权限列表结束

    protected static $data; //用户数据缓存
    protected static $name; //用户名到uid对应关系的缓存
    protected static $mail; //邮箱到uid对应关系的缓存
    protected static $info; //用户配置数据缓存
    protected $uid; //当前用户

    /**
     * 获取一个新实例
    */
    public static function getInstance() {
        return new userinfo();
    }

    /**
     * 连接数据库
     * 参数：
     * $read_only  如果为true，打开一个只读的数据库连接，只允许查询；否则打开一个可读可写的连接。实现分布式应用的读写分离。
     */
    protected static function conn($read_only = false)
    {
        return db::conn('user', $read_only);
    }


    /**
     * 检查邮箱是否有效
     */
    public static function checkMail($mail)
    {
        if ($mail == '') {
            throw new userexception('邮箱不能为空。', 20);
        }
        if (!preg_match('/^([a-z0-9_\-\.]+)@(([a-z0-9]+[_\-]?)\.)+[a-z]{2,5}$/is', $mail)) {
            throw new userexception('邮箱格式错误，请正确填写。', 21);
        }
        return TRUE;
    }

    /*
     * 检查用户名是否有效
     * 用户名只允许汉字、字母、数字、下划线(_)和减号(-)。
     */
    public static function checkName($name)
    {
        if ($name == '') throw new userexception('用户名不能为空。', 10);
        if (strlen(mb_convert_encoding($name, 'gbk', 'utf-8')) > 16) throw new userexception("用户名 \"$name\" 过长。用户名最长只允许16个英文字母或8个汉字（16字节）。", 13);
        if (!str::匹配汉字($name, 'A-Za-z0-9_\\-')) throw new userexception("用户名 \"$name\" 无效。只允许汉字、字母、数字、下划线(_)和减号(-)。", 11);
        return TRUE;
    }

    /*
     * 检查uid是否有效
     */
    public static function checkUid($x_uid)
    {
        $x_uid = (string)$x_uid;
        $uid = (int)$x_uid;
        if ($x_uid !== (string)$uid) throw new userexception("用户ID \"$x_uid\" 无效。用户ID必须是一个正整数。", 14);
        if ($uid < 1) throw new userexception("用户ID \"$x_uid\" 错误。用户ID不能小于1。", 15);
        return TRUE;
    }

    /*取得用户的头像*/
    public function avatar() {
        $url = $this->getinfo('avatar.url');
        
		if (empty($url)) {
			$url = page::getFileUrl(AVATAR_DIR."/default.jpg");
		}
		
		if (QINIU_USE_HTTPS) {
			$url = preg_replace('#^http://'.QINIU_STORAGE_HOST.'/#i', 'https://'.QINIU_STORAGE_HOST.'/', $url);
		}

        return $url;
    }

    /*取得用户的info数据*/
    public function getinfo($index = null)
    {
        $set = self::$info[$this->uid];
        if ($set === NULL) {
            static $rs;
            if (!$rs) {
                $db = self::conn(true);
                $rs = $db->prepare('SELECT `info` FROM `' . DB_A . 'user` WHERE `uid`=?');
            }
            if (!$rs || !$rs->execute(array($this->uid))) return FALSE;
            $data = $rs->fetch(db::ass);
            self::parseinfo($this->uid, $data['info']);
            $set = self::$info[$this->uid];
        }
        if ($index === null) return $set;
        $index = explode('.', $index);
        foreach ($index as $key) {
            $set = $set[$key];
        }
        return $set;
    }

    /*解析用户的info数据*/
    protected static function parseinfo($uid, $info)
    {
        $info = unserialize($info);
        if ($info === NULL) $info = array();

        self::$info[$uid] = $info;
    }

    /**
     * 取得指定用户名的信息，并存储在属性内。之后你可以通过$obj->uid等属性访问用户信息。
     * 参数：
     * $name 用户名
     * $getinfo=FALSE 是否同时取得info信息
     *    （为了优化数据库查询，减少不必要的查询。只有在你需要它时设置它为true）
     *     若$getinfo为假，则当访问info信息时将自动重新获取。
     * 返回值：成功返回TRUE，失败（用户名不存在）返回FALSE
     */
    public function name($name, $getinfo = false)
    {
        $this->uid = NULL;

        try {
            self::checkname($name);
        } catch (userexception $ERR) {
            return FALSE;
        }
        $uid = self::$name[$name];
        if ($uid !== NULL) {
            $this->uid = $uid;
            return $uid !== FALSE ? TRUE : FALSE;

        }
        static $rs, $x_getinfo;
        if (!$rs || $getinfo != $x_getinfo) {
            $db = self::conn(true);
            $rs = $db->prepare('SELECT `uid`,`name`,`mail`,`regtime`,`acctime`' . ($getinfo ? ',`info`' : '') . ' FROM `' . DB_A . 'user` WHERE `name`=?');

            $x_getinfo = $getinfo;
        }
        if (!$rs || !$rs->execute(array($name))) return FALSE;
        $data = $rs->fetch(db::ass);
        if (!isset($data['uid'])) {
            self::$name[$name] = FALSE;
            return FALSE;

        }
        $this->uid = $data['uid'];

        if ($getinfo) {
            self::parseinfo($this->uid, $data['info']);
            unset($data['info']);
        }
        self::$data[$this->uid] = $data;

        self::$name[$data['name']] = $this->uid;
        self::$mail[$data['mail']] = $this->uid;
        return TRUE;
    }

    /**
     * 取得指定uid的信息，并存储在属性内。之后你可以通过$obj->name等属性访问用户信息。
     * 参数：
     * $uid 用户ID
     * $getinfo=FALSE 是否同时取得info信息
     *    （为了优化数据库查询，减少不必要的查询。只有在你需要它时设置它为trun）
     *     若$getinfo为假，则当访问info信息时将自动重新获取。
     * 返回值：成功返回TRUE，失败（uid不存在）返回FALSE
     */
    public function uid($uid, $getinfo = false)
    {
        $this->uid = NULL;

        try {
            self::checkuid($uid);
        } catch (userexception $ERR) {
            return FALSE;
        }
        $data = self::$data[$uid];
        if ($data !== NULL) {
            $this->uid = $uid;
            return $data !== FALSE ? TRUE : FALSE;

        }
        static $rs, $x_getinfo;
        if (!$rs || $getinfo != $x_getinfo) {
            $db = self::conn(true);
            $rs = $db->prepare('SELECT `uid`,`name`,`mail`,`regtime`,`acctime`' . ($getinfo ? ',`info`' : '') . ' FROM `' . DB_A . 'user` WHERE `uid`=?');
            $x_getinfo = $getinfo;
        }
        if (!$rs || !$rs->execute(array($uid))) return FALSE;
        $data = $rs->fetch(db::ass);
        if (!isset($data['uid'])) {
            self::$data[$uid] = FALSE;
            return FALSE;

        }
        $this->uid = $data['uid'];
        if ($getinfo) {
            self::parseinfo($this->uid, $data['info']);
            unset($data['info']);
        }
        self::$data[$this->uid] = $data;

        self::$name[$data['name']] = $this->uid;
        self::$mail[$data['mail']] = $this->uid;
        return TRUE;
    }

    /**
     * 取得指定邮箱的用户信息，并存储在属性内。之后你可以通过$obj->uid等属性访问用户信息。
     * 参数：
     * $mail 邮箱
     * $getinfo=FALSE 是否同时取得info信息
     *    （为了优化数据库查询，减少不必要的查询。只有在你需要它时设置它为true）
     *     若$getinfo为假，则当访问info信息时将自动重新获取。
     * 返回值：成功返回TRUE，失败（用户名不存在）返回FALSE
     */
    public function mail($mail, $getinfo = false)
    {
        $this->uid = NULL;

        try {
            self::checkmail($mail);
        } catch (userexception $ERR) {
            return FALSE;
        }
        $uid = self::$mail[$mail];
        if ($uid !== NULL) {
            $this->uid = $uid;
            return $uid !== FALSE ? TRUE : FALSE;
        }
        static $rs, $x_getinfo;
        if (!$rs || $getinfo != $x_getinfo) {
            $db = self::conn(true);
            $rs = $db->prepare('SELECT `uid`,`name`,`mail`,`regtime`,`acctime`' . ($getinfo ? ',`info`' : '') . ' FROM `' . DB_A . 'user` WHERE `mail`=?');
            $x_getinfo = $getinfo;
        }
        if (!$rs || !$rs->execute(array($mail))) return FALSE;
        $data = $rs->fetch(db::ass);
        if (!isset($data['uid'])) {
            self::$mail[$mail] = FALSE;
            return FALSE;
        }
        $this->uid = $data['uid'];

        if ($getinfo) {
            self::parseinfo($this->uid, $data['info']);
            unset($data['info']);
        }
        self::$data[$this->uid] = $data;
        self::$name[$data['name']] = $this->uid;
        self::$mail[$data['mail']] = $this->uid;
        return TRUE;
    }

    protected static function getUidByRegPhone($phone)
    {
        $db = self::conn(true);
        $rs = $db->prepare('SELECT `uid` FROM `' . DB_A . 'user` WHERE regphone=?');

        if (!$rs || !$rs->execute([$phone])) {
            return false;
        }

        $uid = $rs->fetch(db::num);

        return $uid[0];
    }

    public function regphone($phone, $getinfo = false)
    {
        $uid = self::getUidByRegPhone($phone);

        if (!$uid) {
            return FALSE;
        }

        return $this->uid($uid, $getinfo);
    }

    public function __isset($name)
    {
        return isset(self::$data[$this->uid][$name]);
    }

    public function __get($name)
    {
        return self::$data[$this->uid][$name];
    }

    public function __set($name, $value)
    {
        throw new userexception('不能从类外部修改用户信息', 503);
    }

    public function __unset($name)
    {
        throw new userexception('不能从类外部删除用户信息', 503);
    }

    /*下面是ArrayAccess接口*/
    public function offsetExists($name)
    {
        return isset(self::$data[$this->uid][$name]);
    }

    public function offsetGet($name)
    {
        return self::$data[$this->uid][$name];
    }

    public function offsetSet($name, $value)
    {
        throw new userexception('不能从类外部修改用户信息', 503);
    }

    public function offsetUnset($name)
    {
        throw new userexception('不能从类外部删除用户信息', 503);
    }

    public function hasPermission($permission) {
        if (NULL === self::$data['permission'][$this->uid]) {
            $db = self::conn(true);
            $sql = 'SELECT `permission` FROM `'.DB_A.'user` WHERE uid = ?';
            $rs = $db->prepare($sql);

            if (!$rs || !$rs->execute([$this->uid])) {
                throw new UserException('数据库异常，无法读取权限信息！', 10500);
            }

            $data = $rs->fetch(db::num);
            self::$data['permission'][$this->uid] = $data[0];
        }

        return (bool) ($permission & self::$data['permission'][$this->uid]);
    }

	public function addPermission($permission) {
		unset(self::$data['permission'][$this->uid]);

		$db = self::conn();
		$sql = 'UPDATE `'.DB_A.'user` SET `permission` = `permission` | ? WHERE uid = ?';
		$rs = $db->prepare($sql);

		if (!$rs || !$rs->execute([(int)$permission, $this->uid])) {
			throw new UserException('数据库异常，无法设置权限信息！', 11500);
		}
	}

	
	public function removePermission($permission) {
		unset(self::$data['permission'][$this->uid]);		

		$db = self::conn();
		$sql = 'UPDATE `'.DB_A.'user` SET `permission` = `permission` & ~ ? WHERE uid = ?';
		$rs = $db->prepare($sql);

		if (!$rs || !$rs->execute([(int)$permission, $this->uid])) {
			throw new UserException('数据库异常，无法设置权限信息！', 11500);
		}
	}

	
	public function setUbbOpt($ubb) {
		$ubb->setOpt('style.disable', $this->hasPermission(UserInfo::PERMISSION_UBB_DISABLE_STYLE));
		$ubb->setOpt('all.blockPost', $this->hasPermission(UserInfo::PERMISSION_BLOCK_POST));
	}

	public function getData($key = null, $prefixMatching = false, $onlyValueLength = false, &$version = null) {
        $valueField = $onlyValueLength ? 'LENGTH(`value`) as `value`' : '`value`';
		$sql = 'SELECT `key`,'.$valueField.',version FROM `'.DB_A.'userdata` WHERE `uid`=?';
        $data = [ $this->uid ];
        
		if ($key !== null) {
            if ($prefixMatching) {
    			$sql .= ' AND `key` LIKE ?';
                $data[] = $key.'%';
            } else {
    			$sql .= ' AND `key`=?';
                $data[] = $key;
            }
		}

		$db = self::conn(true);
		$rs = $db->prepare($sql);
		if (!$rs || !$rs->execute($data)) {
		    throw new UserException('数据库异常，无法查询用户数据！', 11500);
		}

		$resultArr = $rs->fetchAll(db::ass);
        $result = [];
        $version = [];
		foreach ($resultArr as $v) {
			$result[$v['key']] = $v['value'];
			$version[$v['key']] = $v['version'];
		}

		if ($key !== null && !$prefixMatching) {
			if (isset($result[$key])) {
                if (isset($version[$key])) {
                    $version = $version[$key];
                } else {
                    $version = null;
                }
				return $result[$key];
			} else {
                $version = null;
				return null;
			}
		} else {
			return $result;
		}
	}
	
	public function setData($key = null, $value = null, $prefixMatching = false, &$version = null) {
        $db = self::conn();
        $db->beginTransaction();

	    if ($value === null) {
			$sql = 'DELETE FROM `'.DB_A.'userdata` WHERE `uid`=?';
			$data = [ $this->uid ];
			if ($key !== null) {
                if ($prefixMatching) {
                    $sql .= ' AND `key` LIKE ?';
                    $data[] = $key.'%';
                } else {
                    $sql .= ' AND `key`=?';
                    $data[] = $key;
                }
            }
        } elseif ($prefixMatching) {
            $sql = 'UPDATE `'.DB_A.'userdata` SET `value`=?, `version`=`version`+1 WHERE `uid`=? AND `key` LIKE ?';
            $data = [ $value, $this->uid, $key.'%' ];
        } else {
            $sql = 'INSERT INTO `'.DB_A.'userdata`(`uid`,`key`,`value`) VALUES(?,?,?)
                ON DUPLICATE KEY UPDATE `value`=VALUES(`value`), `version`=`version`+1';
            $data = [ $this->uid, $key, $value ];
        }

        if ($version !== null) {
            $oldVersion = [];
            $this->getData($key, $prefixMatching, true, $oldVersion);
            if (!is_array($oldVersion)) {
                $oldVersion = [ $oldVersion ];
            }
            foreach ($oldVersion as $v) {
                if ($v != $version) {
                    $db->rollback();
                    return false;
                }
            }
        }

		$rs = $db->prepare($sql);
		if (!$rs || !$rs->execute($data)) {
            $db->rollback();
		    throw new UserException('数据库异常，无法更新用户数据！', 11500);
        }

        $this->getData($key, $prefixMatching, true, $version);
        if ($key !== null && !$prefixMatching && isset($version[$key])) {
            $version = $version[$key];
        }

        $db->commit();
		return true;
	}


    /*class end*/
}
