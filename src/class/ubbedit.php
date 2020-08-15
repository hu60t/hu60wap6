<?php

class ubbEdit extends XUBBP
{
    /*注册显示回调函数*/
    protected $display = array(
		/*开启markdown*/
        'markdown' => 'markdown',
		/*markdown受保护内容（不被XUBBP解析器干扰）*/
		'mdpre' => 'mdpre',
        /*text 纯文本*/
        'text' => 'text',
        /*newline 换行*/
        'newline' => 'newline',
        'tab' => 'tab',
        'empty' => 'emptyTag',
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
		/*markdown风格代码高亮*/
		'mdcode' => 'mdcode',
        /*time 时间标记*/
        'time' => 'time',
        /*video 视频*/
        'video' => 'video',
        'videoStream' => 'video',
        'audioStream' => 'video',
        /*copyright 版权声明*/
        'copyright' => 'copyright',
        /*battlenet 战网*/
        'battlenet' => 'battlenet',
        /*math 数学公式*/
        'math' => 'math',
        'mathzh' => 'math',
        /*layout 布局*/
        'layout' => 'layout',
        /*style 风格*/
        'style' => 'style',
        /*urltxt 网址文本*/
        'urltxt' => 'urltxt',
        /*mailtxt 邮箱文本*/
        'mailtxt' => 'mailtxt',
        /*at消息*/
        'at' => 'at',
        /*face 表情*/
        'face' => 'face',
        /*管理员操作*/
        'delContent' => 'adminDelContent',
    );

    protected static function html($str) {
        return code::html($str, false, true);
    }

    public function display($ubbArray, $serialize = false, $maxLen = null, $page = null)
    {
        $disable = $this->getOpt('all.blockPost');

        if ($disable) {
            return '[div=border:red solid 1px]用户被禁言，发言自动屏蔽。[/div]';
        }

        return parent::display($ubbArray, $serialize, $maxLen, $page);
    }

    /*text 纯文本*/
    public function text($data)
    {
        return self::html($data['value']);
    }

	/*开启markdown模式*/
    public function markdown($text){
      return '<!-- markdown -->'.$text['data'];
    }
	
	/*markdown受保护内容（不被XUBBP解析器干扰）*/
	public function mdpre($data){
		return $data['data'];
    }
	
    /*代码高亮*/
    public function code($data)
    {
        $lang = '=' . self::html($data['lang']);
        if ($lang == '=php') {
            $lang = '';
        }
        return '[code' . $lang . ']' . self::html($data['data']) . '[/code]';
    }
	
	/*markdown风格代码高亮*/
    public function mdcode($data)
    {
        $quote = isset($data['quote']) ? $data['quote'] : '```';
        return $quote . $data['lang'] . self::html($data['data']) . $quote;
    }

    /*time 时间*/
    public function time($data)
    {
        $tag = '=' . self::html($data['tag']);
        if ($tag == '=Y-m-d H:i:s') {
            $tag = '';
        }
        return '[time' . $tag . self::html($data['tag']) . ']';
    }

    /*link 链接*/
    public function url($data)
    {
        if ($data['title'] == '') {
            $html = '[url]' . self::html($data['url']) . '[/url]';
        } else {
            if (is_array($data['title'])) {
                $data['title'] = $this->display($data['title']);
            }

            $html = '[url=' . self::html($data['url']) . ']' . self::html($data['title']) . '[/url]';
        }
        return $html;
    }

    public function urlzh($data)
    {
        if ($data['title'] == '') {
            $html = '《链接：' . self::html($data['url']) . '》';
        } else {
            $html = '《链接：' . self::html($data['url']) . '，' . self::html($data['title']) . '》';
        }
        return $html;
    }

    public function urlout($data)
    {
        if ($data['title'] == '') {
            $html = '《外链：' . self::html($data['url']) . '》';
        } else {
            $html = '《外链：' . self::html($data['url']) . '，' . self::html($data['title']) . '》';
        }
        return $html;
    }

    public function urlname($data)
    {
        if ($data['title'] == '') {
            $html = '《锚：' . self::html($data['url']) . '》';
        } else {
            $html = '《锚：' . self::html($data['url']) . '，' . self::html($data['title']) . '》';
        }
        return $html;
    }

    /*img 图片*/
    public function img($data)
    {
        if ($data['alt'] == '') {
            $html = '[img]' . self::html($data['src']) . '[/img]';
        } else {
            $html = '[img=' . self::html($data['src']) . ']' . self::html($data['alt']) . '[/img]';
        }
        return $html;
    }

    public function imgzh($data)
    {
        if ($data['alt'] == '') {
            $html = '《图片：' . self::html($data['src']) . '》';
        } else {
            $html = '《图片：' . self::html($data['src']) . '，' . self::html($data['alt']) . '》';
        }
        return $html;
    }

    /*thumb 缩略图*/
    public function thumb($data)
    {
        $opt = (int)$data['w'];
        if ($data['h'] != '') {
            $opt .= 'x' . (int)$data['h'];
        }
        return '《缩略图：' . $opt . '，' . self::html($data['src']) . '》';
    }

    /*video 视频*/
    public function video($data)
    {
        switch ($data['type']) {
            case 'video':
            default:
                $tag = '视频';
                break;
            case 'videoStream':
                $tag = '视频流';
                break;
            case 'audioStream':
                $tag = '音频流';
                break;
        }

        return '《'.$tag.'：' . self::html($data['url']) . '》';
    }

    /*copyright 版权声明*/
    public function copyright($data)
    {
        return '《版权：' . self::html($data['tag']) . '》';
    }

    /*battlenet 战网*/
    public function battlenet($data)
    {
        $name = self::html($data['name']);
        if ($data['server'] != '') {
            $name .= '@' . self::html($data['server']);
        }
        if ($data['display'] != null) {
            $name .= "，" . self::html($data['display']);
        }
        return '《战网：' . $name . '》';
    }

    /*math 数学公式*/
    public function math($data) {
        $content = self::html($data['data']);
        if ($data['type'] == 'math') {
            return '[math]'.$content.'[/math]';
        }
        return '《公式：'.$content.'》';
    }

    /*newline 换行*/
    public function newline($data)
    {
        $tag = $data['tag'];

        // br 或 hr
        if ($tag[1] == 'r') {
            $tag = "[$tag]";
        }

        return self::html($tag);
    }

    /* [tab] */
    public function tab($data)
    {
        return '[tab]';
    }

    /* [empty] */
    public function emptyTag($data)
    {
        return '[empty]';
    }

    /*layout 布局*/
    public function layout($data)
    {
        return '[' . self::html($data['tag']) . ']';
    }

    /*style 风格*/
    public function style($data)
    {
        $opt = '=' . self::html($data['opt']);
        if ($opt == '=') {
            $opt = '';
        }
        return '[' . self::html($data['tag']) . $opt . ']';
    }

    /*at消息*/
    public function at($data)
    {
        global $PAGE;

        $uinfo = new UserInfo();
        $ok = $uinfo->uid($data['uid']);

        if ($ok && $uinfo->name != $data['tag']) {
            return '@#'.self::html($data['uid']);
        } else {
            return '@' . self::html($data['tag']);
        }
    }

    /*face 表情*/
    public function face($data)
    {
        return '{' . self::html($data['face']) . '}';
    }

    /*urltxt 链接文本*/
    public function urltxt($data)
    {
        return self::html($data['url']);
    }

    /*mailtxt 邮件链接文本*/
    public function mailtxt($data)
    {
        return self::html($data['mail']);
    }

    /*管理员删除的内容*/
    public function adminDelContent($data) {
        return '';
    }
}
