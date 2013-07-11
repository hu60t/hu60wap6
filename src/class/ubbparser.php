<?php
class ubbParser extends XUBBP {
protected $parse=array(

/*
* 一次性匹配标记
* 
* 如果标记可以一次性匹配，
* 不需要分为开始标记和结束标记分别匹配，
* 则在这一段定义（加在这一段末尾）。
* 
* 注意：不要定义在code规则的前面，
* 因为[code][/code]标记里的内容（代码块）不应该进行任何UBB解析。
*/
/*code 代码高亮*/
    '!^(.*)\[code(?:=(.*?))?\](.*?)\[/code\](.*)$!ies' => "\$this->parser('\\1'),\$this->code('\\2','\\3'),\$this->parser('\\4')",
/*link 链接*/
    '!^(.*)\[url(?:=(.*?))?\](.*?)\[/url\](.*)$!ies' => "\$this->parser('\\1'),\$this->link('url','\\2','\\3'),\$this->parser('\\4')",
    '!^(.*)《(链接|外链|锚)：(.*?)》(.*)$!ies' => "\$this->parser('\\1'),\$this->link('\\2','\\3'),\$this->parser('\\4')",
/*img 图片*/
    '!^(.*)\[img(?:=(.*?))?\](.*?)\[/img\](.*)$!ies' => "\$this->parser('\\1'),\$this->img('img','\\2','\\3'),\$this->parser('\\4')",
    '!^(.*)《(图片|缩略图)：(.*?)》(.*)$!ies' => "\$this->parser('\\1'),\$this->img('\\2','\\3'),\$this->parser('\\4')",
/*copyright 版权*/
    '!^(.*)《版权：(.*?)》(.*)$!ies' => "\$this->parser('\\1'),\$this->copyright('\\2'),\$this->parser('\\3')",
/*battlenet 战网*/
    '!^(.*)《战网：(.*?)》(.*)$!ies' => "\$this->parser('\\1'),\$this->battlenet('\\2'),\$this->parser('\\3')",
/*newline 换行*/
    '!^(.*)\[([bh]r)\](.*)$!ies' => "\$this->parser('\\1'),\$this->newline('\\2'),\$this->parser('\\3')",
    '!^(.*)(///|＜＜＜|＞＞＞)(.*)$!ies' => "\$this->parser('\\1'),\$this->newline('\\2'),\$this->parser('\\3')",
/*time 时间*/
    '!^(.*)\[time(?:=(.*?))?\](.*)$!ies' => "\$this->parser('\\1'),\$this->time('\\2'),\$this->parser('\\3')",

/*
* 开始标记
* 
* 这一段应该只包括开始标记，
* 结束标记不应定义在这一段，
* 否则会出现代码嵌套错误。
*/
/*layoutStart 布局开始*/
    '!^(.*)\[(b|i|u|center|left|right)\](.*)$!eis' => "\$this->parser('\\1'),\$this->layoutStart('\\2'),\$this->parser('\\3')",
/*style 样式开始*/
    '!^(.*)\[(color|div|span)=(.*?)\](.*)$!eis' => "\$this->parser('\\1'),\$this->styleStart('\\2','\\3'),\$this->parser('\\4')",
/*
* 结束标记
* 
* 结束标记应该以与开始标记相反的顺序出现，
* 就像[b][i][/i][/b]一样排列。
* 当然这不是强制的，只是这样排比较美观。
* 
* 这一段应该只有结束标记，
* 开始标记不要放在这里，
* 否则会出现嵌套错误。
*/
/*style 样式结束*/
    '!^(.*?)\[/(color|div|span)\](.*)$!eis' => "\$this->parser('\\1'),\$this->styleEnd('\\2'),\$this->parser('\\3')",
/*layout 布局结束*/
    '!^(.*?)\[/(b|i|u|center|left|right)\](.*)$!eis' => "\$this->parser('\\1'),\$this->layoutEnd('\\2'),\$this->parser('\\3')",

/*
* 易误匹配的标记
* 
* 这里的标记最后匹配，为了防止误匹配。
* 可能会影响其他标记正常匹配的标记放在这里。
*/
/*urltxt 文本链接*/
    '!^(.*)((?:https?|ftps?|rtsp)\://[a-zA-Z0-9\.\,\?\!\(\)\@\/\:\_\;\+\&\%\*\=\~\^\#\-]+)(.*)$!eis' => "\$this->parser('\\1'),\$this->urltxt('\\2'),\$this->parser('\\3')",
    '!^(.*)([a-zA-Z0-9._-]+\.(?:asia|mobi|name|com|net|org|xxx|cc|cn|hk|me|tk|tv|uk)(?:/[a-zA-Z0-9\.\,\?\!\(\)\@\/\:\_\;\+\&\%\*\=\~\^\#\-]+)?)(.*)$!eis' => "\$this->parser('\\1'),\$this->urltxt('\\2'),\$this->parser('\\3')",
/*mailtxt 文本电子邮件地址*/
    '!^(.*)((?:mailto:)?[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z]{2,4})(.*)$!eis' => "\$this->parser('\\1'),\$this->mailtxt('\\2'),\$this->parser('\\3')",
/*at @消息*/
    '!^(.*?)[@＠][@＠#＃a-zA-Z0-9_\x{4e00}-\x{9fa5}]+(.*)$!ueis' => "\$this->parser('\\1'),\$this->layoutEnd('\\2'),\$this->parser('\\3')",
/*face 表情*/
    '!^(.*)\{(ok|[\x{4e00}-\x{9fa5}]{1,2})\}(.*)$!ueis' => "\$this->parser('\\1'),\$this->face('\\2'),\$this->parser('\\3')",
    '!^(.*)《表情(?:：|:)(ok|[\x{4e00}-\x{9fa5}]{1,2})》(.*)$!ueis' => "\$this->parser('\\1'),\$this->face('\\2'),\$this->parser('\\3')",
);
  
/*link  链接*/
public function link($type,$var,$var2='') {
    if($type=='链接' || $type=='外链') {
        $arr=explode('，',$var);
        $url=$arr[0];
        $title=$arr[1];
        $type = $type=='链接' ? 'urlzh' : 'urlout';
    } else {
        $type='url';
        if($var=='') {
            $url=$var2;
            $title='';
        } else {
            $url=$var;
            $title=$var2;
        }
    }
    return array(array(
        'type'=>$type,
        'url'=>$url,
        'title'=>$title
    ));
}

/*img 图片*/
public function img($type,$var,$var2='') {
    if($type=='缩略图') {
        $var=explode('，',$var);
        $opt=$var[0]; 
        $url=$var[1];
        preg_match_all('![0-9]+!',$opt,$opt);
        return array(array(
            'type' => 'thumb',
            'src' => $url,
            'w' => $opt[0][0],
            'h' => $opt[0][1]
        ));
    } else {
        if($type=='图片') {
            $var=explode('，',$var);
            $src=$var[0];
            $alt=$var[1];
        } elseif($var=='') {
            $src=$var2;
            $alt='';
        } else {
            $src=$var;
            $alt=$var2;
        }
        return array(array(
            'type' => $type=='img' ? 'img' : 'imgzh',
            'src' => $src,
            'alt' => $alt
        ));
    }
}
  
public function code($lang, $data) {
    $lang = trim($lang);
    if ($lang == '') $lang = 'php';
    return array(array(
        'type' => 'code',
        'lang' => $lang,
        'data' => $data,
    ));
}
/*class end*/
}