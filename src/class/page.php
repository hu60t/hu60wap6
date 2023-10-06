<?php

/**
 * 页面操作类
 */
class page implements ArrayAccess
{
	//标志位，禁止发送 Cookie （用于跨站访问的 Json Page）
	protected static $noCookie = false;

    //已注册的bid
    protected static $bid = array();

    //模板引擎对象
    protected $tpl = null;

    //已注册的自定义模板id
    protected static $tplid = array();

    //页面信息保存
    protected $page = array(
        'tpl' => DEFAULT_PAGE_TPL,
    );

    //Smarty的模板路径缓存
    protected $tplCache = array();

	/* 设置是否发送 Cookie */
	public function setNoCookie($noCookie) {
		self::$noCookie = $noCookie;

		if ($noCookie) {
			// 删除所有 Cookie
			$_COOKIE = [];
			// 删除 sid
			$this->page['sid'] = NULL;
		}
	}

    /*取得已注册的bid信息*/
    public static function getRegbid()
    {
        return self::$bid;
    }

    /*注册bid*/
    public static function regBid($bid, $func = null)
    {
        if (isset(self::$bid[$bid])) return true;
        if ($func === null) {
            $path = FUNC_DIR . "/hu60.page.$bid.php";
            $func = "hu60_page_$bid";

            if (is_file($path)) require_once $path;
        }
        if (!function_exists($func)) return false;
        self::$bid[$bid] = $func;
        return true;
    }

    /*取得已注册的自定义模板*/
    public static function getRegTpl()
    {
        return self::$tplid;
    }

    /*注册自定义模板*/
    public static function regTpl($tplid)
    {
        return self::$tplid[$tplid] = true;
    }

    /*模板是否已注册*/
    public static function isRegTpl($tplid)
    {
        if ($tplid == '') return true;
        elseif (isset(self::$tplid[$tplid])) return true;
        else return false;
    }

    /*取得当前页面的路径*/
    public function getUrl($page = array())
    {
        $page += $this->page;
        return "$page[cid].$page[pid].$page[extid]$page[bid]$page[path_info]$page[query_string]";
    }

    /*判断是否正在使用https*/
    public static function isHttps() {
        if ($_SERVER['SERVER_PORT'] == 443) {
            return true;
        }

        if (isset($_SERVER['HTTPS']) && !$_SERVER['HTTPS'] && 'off'!=strtolower($_SERVER['HTTPS'])) {
            return true;
        }

        return false;
    }

    /*取得当前URL的协议和主机名部分*/
    public function getUrlPrefix() {
        static $prefix = NULL;

        // 可能多次调用，进行缓存
        if (NULL !== $prefix) {
            return $prefix;
        }

        if ($this->isHttps()) {
            $protocol = 'https';
            $defaultPort = 443;
        }
        else {
            $protocol = 'http';
            $defaultPort = 80;
        }

        $prefix = "$protocol://$_SERVER[HTTP_HOST]";

        if ($_SERVER['SERVER_PORT'] != $defaultPort) {
            $prefix .= ":$_SERVER[SERVER_PORT]";
        }

        return $prefix;
    }

    /*取得文件或目录的访问路径（目录的总是包含结尾的/）*/
    public static function getFileUrl($path, $noCache = false, $keepSymlink = false)
    {
        if ($keepSymlink) {
            $realPath = realpath(dirname($path)).'/'.basename($path);
            if (!file_exists($realPath)) {
                throw new PageException("文件 '$path' 不存在！");
            }
        } else {
            $realPath = realpath($path);
            if (false === $realPath) {
                throw new PageException("文件 '$path' 不存在！");
            }
        }

        $webRoot = realpath($_SERVER['DOCUMENT_ROOT']);

        if (false === $webRoot) {
            throw new PageException("无法获取 \$_SERVER['DOCUMENT_ROOT'] 的绝对路径！");
        }

        if (0 !== strpos($realPath, $webRoot)) {
            throw new PageException("文件 '$path' 位于Web根目录外！");
        }

        $url = substr($realPath, strlen($webRoot));
        $url = strtr($url, '\\', '/');

        if (is_dir($realPath)) {
            $url .= '/';
        }

        if ($noCache) {
            $url .= '?r=' . filemtime($path);
        }

        return $url;
    }

	/*
	* 获取基本访问路径的前缀
	*/
	public function getBaseURL() {
		$url = $this->getFileUrl(ROOT_DIR.SITE_ROUTER_PATH).'/';
		if ($this->page['sidInUrl']) {
			$url .= $this->page['sid'].'/';
		}
		return $url;
	}

    /*取得tpl的访问路径
    *
    * 格式：bid/cid/pid.ext
    */
    public function getTplUrl($path, $noCache = false)
    {
        $path = TPL_DIR . '/' . $this->page['tpl'] . '/' . $path;
        $url = $this->getFileUrl($path);

        if ($noCache) {
            $url .= '?r=' . filemtime($path);
        }

        return $url;
    }

    /*取得并自动修改页面的mime和bid*/
    public function getMime()
    {
        if (isset($this->page['mime'])) return $this->page['mime'];
        foreach (self::$bid as $bid => $func) {
            if (call_user_func_array($func, array(&$this->page['mime']))) {
                $this->page['bid'] = $bid;
                return $this->page['mime'];
            }
        }
        $this->page['bid'] = DEFAULT_PAGE_BID;
        $this->page['mime'] = DEFAULT_PAGE_MIME;
        return $this->page['mime'];
    }

    /*返回要载入的页面的路径(请自行include)*/
    public function load($cid = NULL, $pid = NULL, $bid = NULL)
    {
        if ($cid === NULL) $cid = $this->page['cid'];
        if ($cid == '') $cid = DEFAULT_PAGE_CID;
        if ($pid === NULL) $pid = $this->page['pid'];
        if ($pid == '') $pid = DEFAULT_PAGE_PID;
        if ($bid === NULL) $bid = $this->page['bid'];
        if ($bid == '') $bid = DEFAULT_PAGE_BID;
        $path = PAGE_DIR . "/$cid/$pid.$bid.php";
        if (!is_file($path))
            $path = PAGE_DIR . "/$cid/$pid.php";
        if (!is_file($path))
            throw new pageexception("页面 '$cid.$pid.$bid' 不存在", 1404);
        $this->getMime();
        return $path;
    }

    /*页面gzip压缩*/
    public function gz_start($gzip = PAGE_GZIP)
    {
        if ($gzip && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false && function_exists('gzencode') && strpos(@ini_get('disable_functions'), 'gzencode') === false)
            $this->page['gzip'] = $gzip;
        else

            $this->page['gzip'] = false;
        require_once FUNC_DIR . '/page_gzip.php';
        ob_start('page_gzip');
    }

    /*关闭页面gzip压缩*/
    public function gz_stop()
    {
        $this->page['gzip'] = false;
        ob_end_clean();
    }

    /*初始化并取得模板引擎对象*/
    public function tpl($new = false)
    {
        if (!$new && $this->tpl != null)
            return $this->tpl;
        $tpl = new smarty;
        $tpl->setTemplateDir(PAGE_DIR);
        $tpl->setCompileDir(TEMP_DIR . '/tplc');
        $tpl->setConfigDir(CONFIG_DIR);
        $tpl->setCacheDir(TEMP_DIR . '/pagecache');
        $tpl->autoload_filters = array('pre' => array('hu60ext'));
        $tpl->setCompileId($this->page['bid'] . '.' . $this->page['tpl'] . '.' . $this->page['cid']);
        if (SMARTY_COMPILE == 1) $tpl->compile_check = false;
        elseif (SMARTY_COMPILE == 2) $tpl->force_compile = true;
        #$tpl->assign(array('PAGE'=>$this,'CID'=>$this->page['cid'],'PID'=>$this->page['pid'],'BID'=>$this->page['bid'],'page'=>$this,'cid'=>$this->page['cid'],'pid'=>$this->page['pid'],'bid'=>$this->page['bid']));
        $tpl->assign('PAGE', $this);
        $tpl->assign('page', $this);
        $tpl->assignByRef('CID', $this->page['cid']);
        $tpl->assignByRef('PID', $this->page['pid']);
        $tpl->assignByRef('BID', $this->page['bid']);
        $tpl->assignByRef('cid', $this->page['cid']);
        $tpl->assignByRef('pid', $this->page['pid']);
        $tpl->assignByRef('bid', $this->page['bid']);

        $this->tpl = $tpl;
        return $tpl;
    }

    public function start($nogzip = false, $notpl = false)
    {
        if (!$nogzip) $this->gz_start();
        if (!$notpl) return $this->tpl();
    }

    /*切割PATH_INFO来获得path信息*/
    public function cutPath($pathinfo = NULL)
    {
        if ($pathinfo === NULL) $pathinfo = $_SERVER['PATH_INFO'];
        $info = explode('/', substr($pathinfo, 1));
        if (strpos($info[0], '.') === false) {
            $this->page['sid'] = $info[0];
            array_splice($info, 0, 1);
			$this->page['sidInUrl'] = true;
        } elseif (isset($_COOKIE[COOKIE_A . 'sid'])) {
            $this->page['sid'] = $_COOKIE[COOKIE_A . 'sid'];
        } elseif (isset($_GET['_sid'])) {
            $this->page['sid'] = $_GET['_sid'];
        }
        $info2 = explode('.', (string)$info[0]);
        $info[0] = '';
        $this->page['cid'] = str::word($info2[0], true);
        $this->page['pid'] = str::word($info2[1], true);
        $cnt = count($info2) - 1;
        if ($cnt < 2) $cnt = 2;
        $this->page['bid'] = str::word($info2[$cnt], true);
        array_splice($info2, 0, 2);
        unset($info2[$cnt - 2]);
        $this->page['ext'] = $info2;
        $this->page['extid'] = implode('.', $info2);
        if ($this->page['extid'] != '') $this->page['extid'] .= '.';
        $this->page['path_info'] = implode('/', $info);
        $this->page['path_info_arr'] = $info;
        if ($this->page['cid'] == '')
            $this->page['cid'] = DEFAULT_PAGE_CID;
        if ($this->page['pid'] == '')
            $this->page['pid'] = DEFAULT_PAGE_PID;
        if ($this->page['bid'] == '')
            $this->page['bid'] = DEFAULT_PAGE_BID;
        if ($_SERVER['QUERY_STRING'] != '') $this->page['query_string'] = "?$_SERVER[QUERY_STRING]";
        else $this->page['query_string'] = '';
    }

    /**
     * 选择自定义模板
     *
     * 如果留空(空字符串或NULL)，则尝试从Cookie（$_COOKIE[COOKIE_A.'tpl']）读取模板，
     * 如果仍然为空，则失败
     */
    public function selectTpl($tplid = NULL)
    {
        $this->tplCache = array();
        $tplid = str::word($tplid, true);
        if ($tplid == '') {
            $tplid = str::word($_COOKIE[COOKIE_A . 'tpl'], true);
        }
        if ($tplid != '' && self::isRegTpl($tplid)) {
            $this->page['tpl'] = $tplid;
            if ($this->tpl) {
                $this->tpl->setCompileId($this->page['bid'] . '.' . $this->page['tpl'] . '.' . $this->page['cid']);
            }
            return true;
        } else {
            return false;
        }
    }

    /*取得Smarty模板路径*/
    public function tplPath($name, $ext = '.tpl')
    {
        global $PAGE;

        if (isset($this->tplCache[$ext][$name])) return $this->tplCache[$ext][$name];
        $info = explode('.', $name);
        if (count($info) == 1) {
            $cid = $PAGE['cid'];
            $pid = str::word($name, true);
            $bid = $PAGE['bid'];
        } elseif (count($info) == 2) {
            $cid = str::word($info[0], true);
            $pid = str::word($info[1], true);
            $bid = $PAGE['bid'];
        } else {
            $cid = str::word($info[0], true);
            $pid = str::word($info[1], true);
            $bid = str::word($info[2], true);
        }
        if ($cid == '') $cid = $this->page['cid'];
        if ($pid == '') $pid = $this->page['pid'];
        if ($bid == '') $bid = $this->page['bid'];
        $tpl = $this->page['tpl'];
        $path = TPL_DIR . "/$tpl/$bid/$cid/$pid$ext";
        if (!is_file($path)) {
            $path = TPL_DIR . "/$tpl/" . DEFAULT_PAGE_BID . "/$cid/$pid$ext";
            $this->page['bid'] = DEFAULT_PAGE_BID;
            if ($this->tpl) {
                /*$this->tpl->assign('bid', DEFAULT_PAGE_BID);
                $this->tpl->assign('BID', DEFAULT_PAGE_BID);*/
                $this->tpl->setCompileId($this->page['bid'] . '.' . $this->page['tpl'] . '.' . $this->page['cid']);
            }
        }
        if (!is_file($path)) {
            if ($ext == '.tpl') {
                $type = '模板';
                $code = 2404;
            } elseif ($ext == '.conf') {
                $type = '配置';
                $code = 3404;
            } else {
                $type = 'Smarty';
                $code = 4404;
            }
            throw new pageexception("{$type}文件 \"$name\" 不存在", $code);
        }
        $this->tplCache[$ext][$name] = $path;
        return $path;
    }

    public static function getCookie($name, $default = null)
    {
		if (self::$noCookie) {
            $getName = 'x-'.str_replace('_', '-', $name);
            if (isset($_GET[$getName])) {
                return $_GET[$getName];
            }

            $headerName = 'HTTP_X_'.strtoupper(str_replace('-', '_', $name));
            if (isset($_SERVER[$headerName])) {
                return $_SERVER[$headerName];
            }

			return $default;
		}
		else {
			$name = COOKIE_A . $name;
			return empty($_COOKIE[$name]) ? $default : $_COOKIE[$name];
		}
    }

    public static function setCookie($name, $value, $time = 0)
    {
		if (self::$noCookie) {
            $name = 'x-'.str_replace('_', '-', $name);
            header("$name: $value");
			return true;
		}
		else {
			$name = COOKIE_A . $name;

			if ($time > 0 && $time < $_SERVER['REQUEST_TIME'] || $time < 0) {
				$time += $_SERVER['REQUEST_TIME'];
			}

			return setCookie($name, $value, $time, COOKIE_PATH, COOKIE_DOMAIN, $_SERVER['HTTPS'] == 'on', true);
		}
    }

    /**
     * 获取页面大小
     * 
     * 页面大小可以从$_GET['pageSize']参数中读取。
     * 如果没有该参数，则使用默认值。
     * 如果参数超过$min或$max范围，则取$min或$max。
     */
    public static function pageSize($min, $default, $max) {
        if (isset($_GET['page_size']) && !isset($_GET['pageSize'])) {
            $_GET['pageSize'] = $_GET['page_size'];
        }
        if (!isset($_GET['pageSize'])) {
            return $default;
        }
        $pageSize = (int)$_GET['pageSize'];
        if ($pageSize < $min) {
            $pageSize = $min;
        } elseif ($pageSize > $max) {
            $pageSize = $max;
        }
        return $pageSize;
    }

    /**
     * 检测是不是跨站POST提交
     */
    public static function isCsrfPost() {
        if (empty($_POST) || !empty($_GET['_origin'])) {
            return false;
        }
        if (isset($_SERVER['HTTP_SEC_FETCH_SITE'])) {
            return $_SERVER['HTTP_SEC_FETCH_SITE'] !== 'same-origin'
                && $_SERVER['HTTP_SEC_FETCH_SITE'] !== 'none';
        }
        if (!isset($_SERVER['HTTP_REFERER'])) {
            return false;
        }
        $host = $_SERVER['HTTP_HOST'];
        $ref = parse_url($_SERVER['HTTP_REFERER']);
        return ($ref['host'] !== $host && "$ref[host]:$ref[port]" !== $host) || $ref['path'] == '/';
    }

    public function __isset($name)
    {
        return isset($this->page[$name]);
    }

    public function __get($name)
    {
        return $this->page[$name];
    }

    public function __set($name, $value)
    {
        throw new pageexception('不能从类外部修改PAGE的属性', 1503);
    }

    public function __unset($name)
    {
        throw new pageexception('不能从类外部删除PAGE的属性', 1503);
    }

    /*下面是ArrayAccess接口*/
    public function offsetExists($name) : bool
    {
        return isset($this->page[$name]);
    }

    public function offsetGet($name) : mixed
    {
        return $this->page[$name];
    }

    public function offsetSet($name, $value) : void
    {
        throw new pageexception('不能从类外部修改PAGE的属性', 1503);
    }

    public function offsetUnset($name) : void
    {
        throw new pageexception('不能从类外部删除PAGE的属性', 1503);
    }
    /*class end*/
}
