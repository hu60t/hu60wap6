<?php

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
        $preg = '/^[\x{4e00}-\x{9fa5}' . $extra . ']+$/u';
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
        $f = preg_replace('![^a-zA-Z0-9_\\-]!', '', $f);
        if ($tolower)
            $f = strtolower($f);
        return $f;
    }

    static function cut($str, $off, $len, $add = '', $code = 'utf-8')

    {
        $slen = mb_strlen($str, $code);
        if ($off < 0) $off = $slen - $off;
        $str = mb_substr($str, $off, $len, $code);
        if ($off > 0) $str = $add . $str;
        if ($off + $len < $slen) $str .= $add;
        return $str;
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

//class str end
}
