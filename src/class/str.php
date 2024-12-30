<?php
require_once CLASS_DIR.'/random_compat/lib/random.php';

/*str类,字符串处理类*/
class str
{

    protected static $星期 = array('天', '一', '二', '三', '四', '五', '六', '日');

    /**
     * 取得数字（0-6或1-7）对应的星期汉字
     * 如果是0-6（用date('w')取得），0会得到“天”
     * 如果是1-7（用date('N')取得，7会得到“日”）
     * 所以可以自由选择使用“星期天”或“星期日”
     */
    static function 星期($num)
    {
        return self::$星期[$num];
    }

    static function 时间差($t)
    {
        if ($t < 60) return $t . '秒';
        $t = round($t / 60);
        if ($t < 60) return $t . '分钟';
        $t = round($t / 60);
        if ($t < 24) return $t . '小时';
        $t = round($t / 24);
        return $t . '天';
    }

    static function 匹配汉字($str, $extra = '')
    {
        $preg = '/^[\p{Han}' . $extra . ']+$/u';
        return preg_match($preg, $str);
    }

    static function npos($str, $substr, $times, $code = 'utf-8')
    {
        if ($times < 1)
            return false;
        $len = mb_strlen($substr, $code);
        for ($off = -$len; $times > 0; $times--) {
            $off += $len;
            $off = mb_strpos($str, $substr, $off, $code);
        }
        return $off;
    }

    static function word($f, $tolower = false)
    {
        $f = preg_replace('![^a-zA-Z0-9_\\-]!', '', (string)$f);
        if ($tolower)
            $f = strtolower($f);
        return $f;
    }

    /**
     * 对Unicode字符串进行规范化
     * https://zhuanlan.zhihu.com/p/363841066
     */
    static function normalize($str) {
        return Normalizer::normalize((string)$str, Normalizer::FORM_KC);
    }

    static function cut($str, $off, $len, $add = '', $code = 'utf-8', $notEmpty = false)
    {
        $slen = mb_strlen($str, $code);
        if ($off < 0) $off = $slen - $off;
        $str = mb_substr($str, $off, $len, $code);
        if ($off > 0) $str = $add . $str;
        if ($off + $len < $slen) $str .= $add;
        if ($notEmpty && empty(trim($str))) {
            return $add;
        }
        return $str;
    }

    // 同时兼容 C:\xxx 风格和 /mnt/xxx 风格的 basename
    static function basename($path) {
        return basename(str_replace('\\', '/', $path));
    }

    // 同时兼容 C:\xxx 风格和 /mnt/xxx 风格的 dirname
    static function dirname($path) {
        return dirname(str_replace('\\', '/', $path));
    }

    /*
     * 规范化手机号码
     *
     * 去除字符串中的非手机号码部分
     * 去除中国国际区号（0086或+86）
     * 其他地区的国际区号等保持不变
     */
    public static function regularPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9\+]/s', '', $phone);

        if ($phone[0] == '+') {
            $phone = preg_replace('/^\+86/s', '', $phone);
        } else {
            $phone = preg_replace('/^0086/s', '', $phone);
        }

        //若 $phone === '' 或 $phone === false
        if (!$phone) {
            throw new Exception('手机号码格式不正确');
        }

        return $phone;
    }

    /**
     * 取得可读的文件大小
     *
     * @param $fileSize 文件大小（整数，单位：字节）
     * @param $decimal 结果保留的小数位数（默认两位）
     * @param $separator 数值和单位之间的分隔符（默认空格）
     */
    public static function filesize($fileSize, $decimal = 2, $separator = ' ') {
        $units = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB', 'NB', 'DB');
        $unit = array_shift($units);

        while ($fileSize > 999 && !empty($units)) {
            $fileSize /= 1024;
            $unit = array_shift($units);
        }

        $fileSize = round($fileSize, $decimal);

        return "{$fileSize}{$separator}{$unit}";
    }

    public static function 过滤滥用($text) {
        #通过css解决了版面长草的问题，不需要再过滤了。
        #$text = preg_replace('/[\x{0e00}-\x{0e7f}]{10,}/u', '(为防止版面长草，过长的泰文被过滤)', $text);
        #$text = preg_replace('/[\x{0600}-\x{06ff}]{10,}/u', '(为防止版面长草，过长的阿拉伯文被过滤)', $text);
        
        // 删除连续的零宽空格、emoji等，阻止加密内容 <https://hu60.cn/q.php/bbs.topic.103403.html>
        $text = preg_replace("/[\x{200B}-\x{200F}\x{FEFF}🐱❤️😊]{8,}/u", '(虎绿林不允许内容加密)', $text);

        return $text;
    }

    // 将UTF-8中的特殊空格字符转换为普通空格
    // \2 和 \3 是 UBBDisplay 中使用的 Markdown 内容转义标记，在此转为空格以防冲突
    public static function nbsp2space($str) {
        return str_replace(["\xc2\xa0","\xe2\x80\x82", "\2", "\3"], ' ', (string)$str);
    }

    // 取得自然语言描述的时间差
    public static function ago($t) {
      if (empty($t) || $t == 0) {
        return '未记录';
      }
      $d = time() - $t;
      if($d < 60){
        return "刚刚";
      }elseif($d/60 < 60){
        return (string)round($d/60)."分钟前";
      }elseif($d/(60*60) < 24){
        return (string)round($d/(60*60))."小时前";
      }elseif($d/(60*60*24) < 2){
        return "1天前";
      }elseif(date('Y',time())==date('Y',$t)){
        return date('m-d H:i',$t);
      }else{
        return date('Y-m-d',$t);
      }
    }
	
	//将html标记转码
	public static function htmlTagToEntity($text) {
		//return preg_replace('#<(/?\w+(?:\s[^>]*)?)>#is', '&lt;\\1&gt;', $text);
		return str_replace('<', '&lt;', $text);
	}

	// 密码学安全的随机内容发生器
	public static function random_bytes($len) {
		return random_bytes($len);		
    }
    
    // 获取GET或者POST值
    public static function getOrPost($key, $defaultValue = null) {
        if (isset($_GET[$key]))
            return $_GET[$key];
        if (isset($_POST[$key]))
            return $_POST[$key];
        return $defaultValue;
    }

    // 判断GET或POST值是否存在
    public static function getOrPostExists($key) {
        return isset($_GET[$key]) || isset($_POST[$key]);
    }

    // 判断发言是否为空
    public static function isEmptyPost($content) {
        $content = preg_replace('/\s+/', '', $content);
        $content = preg_replace('@\[/?[^\]]*\]@', '', $content);
        return in_array($content, ['', '<!md>', '<!--markdown-->']);
    }

    // 产生 v4 uuid
    // 来自 <https://www.uuidgenerator.net/dev-corner/php>
    public static function guidv4() {
        // Generate 16 bytes (128 bits) of random data.
        $data = random_bytes(16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function webPlugId() {
        return strtr(base64_encode(str::random_bytes(12)), array('+'=>'t', '/'=>'j'));
    }

    public static function bitset2str($bitset) {
        $bitset = (int)$bitset;
        if ($bitset == 0) {
            return 'all';
        }
        $result = [];
        for ($i=0; $i<32; $i++) {
            $mask = 1 << $i;
            if ($bitset & $mask) {
                $result[] = $i + 1;
            }
        }
        return implode(',', $result);
    }

    public static function str2bitset($str) {
        $str = strtolower(trim($str));
        if ($str == 'all') {
            return 0;
        }
        $groups = explode(',', $str);
        $bitset = 0;
        foreach ($groups as $i) {
            $i = (int)(trim($i));
            $mask = 1 << ($i - 1);
            $bitset |= $mask;
        }
        return $bitset;
    }

    // 把所有字符都转换为HTML实体(包括字母数字)
    public static function htmlentities($str) {
        $result = '';
        $len = mb_strlen($str, 'utf-8');
        for ($i=0; $i<$len; $i++) {
            $result .= '&#x'.bin2hex(mb_substr($str, $i, 1, 'utf-8')).';';
        }
        return $result;
    }
//class str end
}
