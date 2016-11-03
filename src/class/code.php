<?php

/**
 * 编码解码类
 *
 * 目前包含html编码、适合url传输的base64编解码、文本编码转换三个功能
 */
class code
{
    /**
     * 格式化纯文本为html文本
     *
     * 把纯文本格式化成可直接输出的html文本，包括进行html实体替换（&变&amp;等）、换行转<br/>等操作
     *
     * 参数：
     *     $str 欲转换的纯文本
     *     $br 换行方式，可取的值：
     *         false 不转义换行符（默认）
     *         true 或 1 转成&#13;&#10;（在输入框里使用）
     *         2 转成<br/>
     *         其他值 转成指定的字符
     *     $NOnbsp 是否不把空格转换成&nbsp;
     *         false 否，转换空格（默认）
     *         true 是，不转换空格
     *
     * 注：该函数可以自动根据$PAGE['bid']是否为'wml'来判断是否需要转码$为$$
     */
    static function html($str, $br = false, $NOnbsp = false)
    {
        global $PAGE;
        $str = htmlspecialchars($str, ENT_QUOTES, 'utf-8');
        if ($br !== false) {
            if ($br === true || $br == 1) $br = '&#13;&#10;';
            elseif ($br == 2) $br = '<br/>';
            $str = str_replace(array("\r\n", "\r", "\n"), $br, $str);
        }
        if (!$NOnbsp)
            $str = str_replace(' ', '&ensp;', $str);
        if ($PAGE['bid'] == 'wml')
            $str = str_replace('$', '$$', $str);

        $str = str::过滤滥用($str);

        return $str;
    }

    /**
     * 编码转换
     *
     * 参数：
     *     $text 欲转换的文本
     *     $in 原编码
     *     $out 转换后的编码
     */
    static function conv($text, $in, $out)
    {
        if (function_exists('iconv')) {
            $out .= '//TRANSLIT//IGNORE';
            return iconv($in, $out, $text);
        } elseif (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($text, $out, $in);
        } else {
            return $text;
        }
    }

    /**
     * 适合url的base64编码
     */
    static function b64e($data)
    {
        return strtr(base64_encode($data), array('+' => '-', '/' => '_', '=' => '.'));
    }

    /**
     * 适合url的base64解码
     */
    static function b64d($code)
    {
        return base64_decode(strtr($code, array('-' => '+', '_' => '/', '.' => '=')));
    }

    /*code类结束
    */
}
