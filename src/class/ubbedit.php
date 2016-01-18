<?php
class ubbEdit extends XUBBP {
    /*注册显示回调函数*/
    protected $display=array(
        /*text 纯文本*/
        'text' => 'text',
        /*newline 换行*/
        'newline' => 'newline',
        'tab' => 'tab',
        /*link 链接*/
        'url' => 'url',
        'urlzh' => 'urlzh',
        'urlout' => 'urlout',
        'urlname' => 'urlname',
        /*img 图片*/
        'img' => 'img',
        'imgzh' => 'imgzh',
        'thumb' => 'thumb',
        /*code 代码高亮*/
        'code' => 'code',
        /*time 时间标记*/
        'time' => 'time',
        /*copyright 版权声明*/
        'copyright' => 'copyright',
        /*battlenet 战网*/
        'battlenet' => 'battlenet',
        /*layout 布局*/
        'layout' => 'layout',
        /*style 风格*/
        'style' => 'style',
        /*urltxt 网址文本*/
        'urltxt' => 'text',
        /*mailtxt 邮箱文本*/
        'mailtxt' => 'text',
        /*at消息*/
        'at' => 'at',
        /*face 表情*/
        'face' => 'face',
    );

    /*text 纯文本*/
    public function text($data) {
        return code::html($data['value']);
    }

    /*代码高亮*/
    public function code($data) {
        $lang = '='.code::html($data['lang']);
        if ($lang == '=php') {$lang = '';}
        return '[code'.$lang.']'.code::html($data['data']).'[/code]';
    }

    /*time 时间*/
    public function time($data) {
        $tag = '='.code::html($data['tag']);
        if ($tag == '=Y-m-d H:i:s') {
            $tag = '';
        }
        return '[time'.$tag.code::html($data['tag']).']';
    }

    /*link 链接*/
    public function url($data) {
        if ($data['title'] == '') {
            $html = '[url]'.code::html($data['url']).'[/url]';
        } else {
            $html = '[url='.code::html($data['url']).']'.code::html($data['title']).'[/url]';
        }
        return $html;
    }
    public function urlzh($data) {
        if ($data['title'] == '') {
            $html = '《链接：'.code::html($data['url']).'》';
        } else {
            $html = '《链接：'.code::html($data['url']).'，'.code::html($data['title']).'》';
        }
        return $url;
    }
    public function urlout($data) {
        if ($data['title'] == '') {
            $html = '《外链：'.code::html($data['url']).'》';
        } else {
            $html = '《外链：'.code::html($data['url']).'，'.code::html($data['title']).'》';
        }
        return $url;
    }
    public function urlname($data) {
        if ($data['title'] == '') {
            $html = '《锚：'.code::html($data['url']).'》';
        } else {
            $html = '《锚：'.code::html($data['url']).'，'.code::html($data['title']).'》';
        }
        return $html;
    }
    /*img 图片*/
    public function img($data) {
        if ($data['alt'] == '') {
            $html = '[img]'.code::html($data['src']).'[/img]';
        } else {
            $html = '[img='.code::html($data['src']).']'.code::html($data['alt']).'[/img]';
        }
        return $html;
    }
    public function imgzh($data) {
        if ($data['alt'] == '') {
            $html = '《图片：'.code::html($data['src']).'》';
        } else {
            $html = '《图片：'.code::html($data['src']).'，'.code::html($data['alt']).'》';
        }
        return $html;
    }
    /*thumb 缩略图*/
    public function thumb($data) {
        $opt = (int)$data['w'];
        if ($data['h'] != '') {$opt .= 'x'.(int)$data['h'];}
        return '《缩略图：'.$opt.','.code::html($data['src']).'》';
    }

    /*copyright 版权声明*/
    public function copyright($data) {
        return '《版权：'.code::html(data['tag']).'》';
    }

    /*battlenet 战网*/
    public function battlenet($data) {
        $name = code::html($data['name']);
        if ($data['server']!='') {$name.='@'.code::html($data['server']);}
        return '《战网：'.name.'》';
    }

    /*newline 换行*/
    public function newline($data) {
        return code::html($data['tag']);
    }

    /* [tab] */
    public function tab($data) {
        return '[tab]';
    }

    /*layout 布局*/
    public function layout($data) {
        return '['.code::html($data['tag']).']';
    }

    /*style 风格*/
    public function style($data) {
        $opt = '='.code::html($data['opt']);
        if ($opt == '=') {$opt = '';}
        return '['.code::html($data['tag']).$opt.']';
    }

    /*at消息*/
    public function at($data) {
        global $PAGE;
        return '@'.code::html($data['tag']);
    }

    /*face 表情*/
    public function face($data) {
        return '{'.$data['face'].'}';
    }
}
















