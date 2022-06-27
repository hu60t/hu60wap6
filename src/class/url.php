<?php

class url
{
#url解析类

    // base64URL编码，把+/替换为-_
    static function b64e($data)
    {
	    return strtr(base64_encode($data), array('+'=>'-', '/'=>'_'));
    }

    //去除末尾的填充（Base64 Encode Clean）
    static function b64ec($data)
    {
        return str_replace('=', '', self::b64e($data));
    }

    static function b64d($code)
    {
	    return base64_decode(strtr((string)$code, array('-'=>'+', '_'=>'/')));
    }

    static function decodeUrl64InLink($url, &$multiEncode = null) {
        // 链接可能是这样的：
        // https://hu60.cn/q.php/link.img.html?url64=aHR0c省略mpwZw..#20
        if (preg_match('/\burl64=([^&#]+)(#.*)?\b/s', $url, $arr)) {
                // 对多重编码进行解码
                $url = trim(code::b64d($arr[1]).$arr[2]);
                $url = self::decodeUrl64InLink($url);
                $multiEncode = true;
        }
        return trim($url);
    }

    static function realpath($url, $iurl = '') {
        /*取得URL的最简绝对路径
        $url 目标URL
        如果$url是一个相对路径，则必须指定参考路径$iurl
        具有良好的容错性。即使相对路径非法，也能尽可能地得到正确的结果*/
        $url = trim($url);
        $iurl = trim($iurl);
        if (preg_match("!^[a-zA-Z_][a-zA-Z0-9_]*://[a-zA-Z0-9_\\.\\-]*\$!", $iurl))
            $iurl .= '/';
        if ($iurl != '' && !preg_match('!^[a-zA-Z0-9_]*:!', $url)) {
            $iurl = str_replace('\\', '/', $iurl);
            $iurl = preg_replace('!\?.*$!', '', $iurl);
            /*假设所有没以/结尾的路径都是指向文件，因此注释掉本段
            if(substr($iurl,-1,1)!='/')
             $iurl.='/url';
            else*/
            $iurl .= 'url';
            $iurl = dirname($iurl);
            if (substr($url, 0, 1) == '/')
                $iurl = preg_replace("!^([a-zA-Z0-9_]*:/*.[^/]*)/?.*$!", '\\1', $iurl);
            $url = $iurl . '/' . $url;
        }
        if (preg_match("!^([a-zA-Z0-9_]*:/*)(.[^\?]*)(\?.*)?$!", $url, $ur)) {
            $path = str_replace('\\', '/', $ur[2]);
            while ($i = strpos($path, '/../')) {
                $patha = dirname(substr($path, 0, $i));
                if ($patha == '.' or $patha == '')
                    break;
                $path = $patha . substr($path, $i + 3);
            }
            $path = preg_replace(array('!/\./!', '!/{2,}!'), array('/', '/'), $path);
            $url = $ur[1] . $path . $ur[3];
        }
        return $url;
    }

    static function getQueryArray($query, $isurl = false) {
        #取得查询字符串数组
        if ($isurl) {
            $query = parse_url($query);
            $query = $query['query'];
        }
        $query = explode('&', $query);
        $arr = array();
        foreach ($query as $field) {
            if ($i = strpos($field, '='))

                $arr[urldecode(substr($field, 0, $i))] = urldecode(substr($field, $i + 1));
            else
                $arr[] = $field;
        }
        return $arr;
    }

//取得查询字符串URL
    static function getQueryString($query, $space = false)
    {
        $co = count($query);
        $jc = 0;
        $url = '';
        foreach ($query as $key => $value) {
            $jc++;
            $url .= urlencode($key) . '=' . urlencode($value);
            if ($jc < $co)

                $url .= '&';
        }
        if ($space)
            $url = str_replace('+', '%20', $url);
        return $url;
    }
	
	//获得URL安全跳转链接
	static function getJumpLink($url) {
		global $PAGE;
        
        if (JsonPage::isJsonPage()) {
            return $url;
        }

		return SITE_ROUTER_PATH . '/link.url.' . $PAGE->bid . '?url64=' . code::b64e($url);
    }

	//获得图片安全跳转链接
	static function getJumpImg($url) {
        global $PAGE;
        
        if (JsonPage::isJsonPage() || preg_match('#^data:image/#is', $url)) {
            return $url;
        }

		return SITE_ROUTER_PATH . '/link.img.' . $PAGE->bid . '?url64=' . code::b64e($url);
	}
    
    // 获得扩展名
    static function extName($url) {
        $name = basename(parse_url($url, PHP_URL_PATH));
        if (FALSE !== ($pos = strrpos($name, '.'))) {
            $name = substr($name, $pos + 1);
        }
        return $name;
    }
//class url end
}
