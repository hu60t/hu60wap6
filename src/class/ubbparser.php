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
    '!^(.*)\[code(?:=(.*?))?\](.*?)\[/code\](.*)$!is' => array(array(1,4), 'code', array(2,3)),
/*time 时间*/
    '!^(.*)\[time(?:=(.*?))?\](.*)$!is' => array(array(1,3), 'time', array(2)),
/*link 链接*/
    '!^(.*)\[url(?:=(.*?))?\](.*?)\[/url\](.*)$!is' => array(array(1,4), 'link', array('url',2,3)),
    '!^(.*)《(链接|外链|锚)：(.*?)》(.*)$!is' => array(array(1,4), 'link', array(2,3)),
/*img 图片*/
    '!^(.*)\[img(?:=(.*?))?\](.*?)\[/img\](.*)$!is' => array(array(1,4), 'img', array('img',2,3)),
    '!^(.*)《(图片|缩略图)：(.*?)》(.*)$!is' => array(array(1,4), 'img', array(2,3)),
/*copyright 版权*/
    '!^(.*)《版权：(.*?)》(.*)$!is' => array(array(1,3), 'copyright', array(2)),
/*battlenet 战网*/
'!^(.*)《战网：(.*?)》(.*)$!is' => array(array(1,3), 'battlenet', array(2)),
/*tab 四个空格*/
'!^(.*)\[tab\](.*)$!is' => array(array(1,2), 'tab', array(2)),
/*empty UBB转义*/
'!^(.*)\[empty\](.*)$!is' => array(array(1,2), 'emptyTag', array(2)),
/*newline 换行*/
#    '!^(.*)(\r\n)(.*)$!is' => array(array(1,3), 'newline', array(2)),
#    '!^(.*)([\r\n])(.*)$!is' => array(array(1,3), 'newline', array(2)),
    '!^(.*)\[([bh]r)\](.*)$!is' => array(array(1,3), 'newline', array(2)),
    '!^(.*)(///|＜＜＜|＞＞＞)(.*)$!is' => array(array(1,3), 'newline', array(2)),

/*
* 开始标记
* 
* 这一段应该只包括开始标记，
* 结束标记不应定义在这一段，
* 否则会出现代码嵌套错误。
*/
/*layoutStart 布局开始*/
    '!^(.*)\[(b|i|u|center|left|right)\](.*)$!is' => array(array(1,3), 'layoutStart', array(2)),
/*style 样式开始*/
    '!^(.*)\[(color|div|span)=(.*?)\](.*)$!is' => array(array(1,4), 'styleStart', array(2,3)),
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
    '!^(.*?)\[/(color|div|span)\](.*)$!is' => array(array(1,3), 'styleEnd', array(2)),
/*layout 布局结束*/
    '!^(.*?)\[/(b|i|u|center|left|right)\](.*)$!is' => array(array(1,3), 'layoutEnd', array(2)),

/*
* 易误匹配的标记
* 
* 这里的标记最后匹配，为了防止误匹配。
* 可能会影响其他标记正常匹配的标记放在这里。
*/
/*urltxt 文本链接*/
    '!^(.*)((?:https?|ftps?|rtsp)\://[a-zA-Z0-9\.\,\?\!\(\)\@\/\:\_\;\+\&\%\*\=\~\^\#\-]+)(.*)$!is' => array(array(1,3), 'urltxt', array(2)),
    #'#^(.*?)((?<!@)[a-zA-Z0-9._-]{1,255}\.(?:asia|mobi|name|com|net|org|xxx|cc|cn|hk|me|tk|tv|uk)(?:/[a-zA-Z0-9\.\,\?\!\(\)\@\/\:\_\;\+\&\%\*\=\~\^\#\-]+)?)(.*)$#is' => array(array(1,3), 'urltxt', array(2)),
/*mailtxt 文本电子邮件地址*/
    '!^(.*?)((?:mailto:)?[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z]{2,4})(.*)$!is' => array(array(1,3), 'mailtxt', array(2)),
/*at @消息*/
    '!^(.*?)[@＠]([@＠#＃a-zA-Z0-9_\x{4e00}-\x{9fa5}]+)(.*)$!uis' => array(array(1,3), 'at', array(2)),
/*face 表情*/
    '!^(.*)\{(ok|[\x{4e00}-\x{9fa5}]{1,3})\}(.*)$!uis' => array(array(1,3), 'face', array(2)),
    '!^(.*)《(?:表情)?(?:：|:)(ok|[\x{4e00}-\x{9fa5}]{1,3})》(.*)$!uis' => array(array(1,3), 'face', array(2)),
);
  
/**
* @brief 代码高亮
*/
public function code($lang, $data) {
    $lang = strtolower(trim($lang));
    if ($lang == '') $lang = 'php';
    return array(array(
        'type' => 'code',
        'lang' => $lang,
        'data' => $data,
		'len' => $this->len($data)
    ));
}

/**
* @brief 时间标记
*/
public function time($tag) {
    return array(array(
        'type' => 'time',
        'tag' => $tag,
		'len' => $this->len($tag)
    ));
}
  
/** @brief 链接*/
public function link($type,$var,$var2='') {
    if($type=='链接' || $type=='外链' || $type=='锚') {
        $arr=explode('，',$var);
        $url=$arr[0];
        $title=$arr[1];
        $type = $type=='链接' ? 'urlzh' : ($type == '外链' ? 'urlout' : 'urlname');
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
	$len = $this->len($url)+$this->len($title);
    if (strpos($title, '[img')!==false || strpos($title, '《图片：')!==false || strpos($title, '《缩略图：')!==false) {
        $obj = new ubbParser;
        $obj->setParse(array(
            '!^(.*)\[img(?:=(.*?))?\](.*?)\[/img\](.*)$!is' => array(array(1,4), 'img', array('img',2,3)),
            '!^(.*)《(图片|缩略图)：(.*?)》(.*)$!is' => array(array(1,4), 'img', array(2,3))
        ));
        $title = $obj->parse($title);
    }

    return array(array(
        'type'=>$type,
        'url'=>trim($url),
        'title'=>$title,
		'len'=>$len
    ));
}

/** @brief 图片*/
public function img($type,$var,$var2='') {
    if($type=='缩略图') {
        $var=explode('，',$var);
        $opt=$var[0]; 
        $url=$var[1];
        preg_match_all('![0-9]+!',$opt,$opt);
        return array(array(
            'type' => 'thumb',
            'src' => trim($url),
            'w' => $opt[0][0],
            'h' => $opt[0][1],
			'len' => $this->len($url)
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
            'src' => trim($src),
            'alt' => $alt,
			'len' => $this->len($src) + $this->len($alt)
        ));
    }
}

/**
* @brief 版权声明标记
*/
public function copyright($tag) {
    return array(array(
        'type' => 'copyright',
        'tag' => trim($tag),
		'len' => $this->len($tag)
    ));
}

/**
* @brief 战网（魔兽世界英雄榜）链接标记
*/
public function battlenet($tag) {
    $info = explode('@', str_replace('＠', '@', $tag));
    $name = explode('，', $info[1]);
    return array(array(
        'type' => 'battlenet',
        'name' => trim($info[0]),
        'server' => trim($name[0]),
        'display' => trim($name[1]),
		'len' => $this->len($tag)
    ));
}

/**
* @brief 换行
*/
public function newline($tag) {
    return array(array(
	    'type' => 'newline',
		'tag' => $tag,
		'len' => $this->len($tag)
	));
}

/** @brief tab 四个空格 */
public function tab($tag) {
    return [[
        'type' => 'tab',
        'len' => 4
    ]];
}

/** @brief empty UBB转义 */
public function emptyTag($tag) {
    return [[
        'type' => 'empty',
        'len' => 0
    ]];
}


/**
* @brief 布局开始
*/
function layoutStart($tag) {
    return array(array(
	    'type' => 'layout',
		'tag' => strtolower($tag),
		'len' => $this->len($tag)
	));
}

/**
* @brief 布局结束
*/
function layoutEnd($tag) {
    /*结束标记的tag以斜杠(/)开始*/
    return array(array(
	    'type' => 'layout',
		'tag' => '/'.$tag,
		'len' => $this->len($tag)
	));
}

/**
* @brief 样式开始
*/
function styleStart($tag, $opt) {
    return array(array(
	    'type' => 'style',
		'tag' => strtolower($tag),
		'opt' => $opt,
		'len' => $this->len($tag.$opt)
	));
}

/**
* @brief 样式结束
*/
function styleEnd($tag, $opt) {
    /*结束标记的tag以斜杠(/)开始*/
    return array(array(
	    'type' => 'style',
		'tag' => '/'.$tag,
		'opt' => $opt,
		'len' => $this->len($tag.$opt)
	));
}

/**
* @brief urltxt 网址文本
*/
function urltxt($url) {
    return array(array(
        'type' => 'urltxt',
        'url' => trim($url),
        'len' => $this->len($url)
    ));
}

/**
* @brief mailtxt 电子邮箱文本
*/
function mailtxt($mail) {
    return array(array(
        'type' => 'mailtxt',
        'mail' => trim($mail),
        'len' => $this->len($mail)
    ));
}

/**
* @brief at消息
*/
function at($tag) {
    $tag = str_replace('＠', '@', $tag);
    $arr = explode('@', $tag);
    if (count($arr) > 1) {
        $result = array();
        foreach ($arr as $v) {
            $res = $this->at($v);
            $result = array_merge($result, $res);
        }
        return $result;
    }
    global $USER;
    //user的at方法产生at消息并返回at对象的uid
    //会生成at信息的页面须用regAt()方法注册at消息
    //若未注册，则不产生at消息，但uid正常返回
    if (!is_object($USER)) {
        $user = new user;
    } else {
        $user = $USER;
    }
    $uid = $user->at($tag);
    return array(array(
        'type' => 'at',
        'tag' => trim($tag),
        'uid' => $uid,
        'len' => $this->len($tag)
    ));
}

/**
* @brief 表情
*/
function face($face) {
    return array(array(
        'type' => 'face',
        'face' => trim($face),
        'len' => $this->len($face)
    ));
}

/*class end*/
}
