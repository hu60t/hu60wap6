<?php

class UbbText extends XUBBP
{
    // 解析at通知信息时得到的url
    public $atMsgUrl = null;

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
        /*at通知信息（用于推送通知）*/
        'atMsg' => 'atMsg',
    );

    protected static function html($str) {
        return $str;
    }

    public function display($ubbArray, $serialize = false, $maxLen = null, $page = null)
    {
        $disable = $this->getOpt('all.blockPost');

        if ($disable) {
            return '用户被禁言，发言自动屏蔽。';
        }

        return parent::display($ubbArray, $serialize, $maxLen, $page);
    }

    /*text 纯文本*/
    public function text($data)
    {
        return $data['value'];
    }

	/*开启markdown模式*/
    public function markdown($text){
      return $text['data'];
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
        return "\n\n".$data['data']."\n\n";
    }
	
	/*markdown风格代码高亮*/
    public function mdcode($data)
    {
        return "\n\n".$data['data']."\n\n";
    }

    /*time 时间*/
    public function time($data)
    {
        if ($data['tag'] == '') {
            $data['tag'] = 'Y-m-d H:i:s';
        }
        return date($data['tag']);
    }

    /*link 链接*/
    public function url($data)
    {
        if ($data['title'] == '') {
            $html = $data['url'];
        } else {
            if (is_array($data['title'])) {
                $data['title'] = $this->display($data['title']);
            }

            $html = $data['title'].': '.$data['url'];
        }
        return $html;
    }

    public function urlzh($data)
    {
        if ($data['title'] == '') {
            $html = $data['url'];
        } else {
            if (is_array($data['title'])) {
                $data['title'] = $this->display($data['title']);
            }

            $html = $data['title'].': '.$data['url'];
        }
        return $html;
    }

    public function urlout($data)
    {
        if ($data['title'] == '') {
            $html = 'http://'.$data['url'];
        } else {
            if (is_array($data['title'])) {
                $data['title'] = $this->display($data['title']);
            }

            $html = $data['title'].': http://'.$data['url'];
        }
        return $html;
    }

    public function urlname($data)
    {
        return '';
    }

    /*img 图片*/
    public function img($data)
    {
        if ($data['alt'] == '') {
            $html = $data['src'];
        } else {
            $html = $data['alt'].': '.$data['src'];
        }
        return $html;
    }

    public function imgzh($data)
    {
        if ($data['alt'] == '') {
            $html = $data['src'];
        } else {
            $html = $data['alt'].': '.$data['src'];
        }
        return $html;
    }

    /*thumb 缩略图*/
    public function thumb($data)
    {
        global $PAGE;
        $src = code::html($data['src']);

        //百度输入法多媒体输入
        if (preg_match('#^(https?://ci\.baidu\.com)/([a-zA-Z0-9]+)$#is', $src, $arr)) {
            $prefix = $arr[1];
            $imgId = $arr[2];
            $src = $prefix . '/more?mm=' . $imgId;
        }
		
        return $src;
    }

    /*video 视频*/
    public function video($data)
    {
        return $data['url'];
    }

    /*copyright 版权声明*/
    public function copyright($data)
    {
        $x = strtolower($data['tag']);

        if (substr($x, 0, 3) == 'cc-') {
            $en = 'by';
            $cn = '署名';
            if (strpos($x, '-nc')) {
                $en .= '-nc';
                $cn .= '-非商业性使用';
            }
            if (strpos($x, '-nd')) {
                $en .= '-nd';
                $cn .= '-禁止演绎';
            } elseif (strpos($x, '-sa')) {
                $en .= '-sa';
                $cn .= '-相同方式共享';
            }
            return '本作品采用知识共享' . $cn . '3.0许可协议进行许可。';
        }

        if ($x == 'gfdl') {
            return '本作品采用GNU自由文档许可证进行许可。';
        }
        if ($x == '公有领域' or $x == '公共领域') {
            return '本作品属于公有领域。';
        }
        return '本作品采用' . code::html($data['tag']) . '进行许可。';
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
        if (in_array($data['tag'], ['hr', '＜＜＜'])) {
            return "\n--------\n";
        } else {
            return '\n';
        }
    }

    /* [tab] */
    public function tab($data)
    {
        return '    ';
    }

    /* [empty] */
    public function emptyTag($data)
    {
        return '';
    }

    /*layout 布局*/
    public function layout($data)
    {
        return '';
    }

    /*style 风格*/
    public function style($data)
    {
        return '';
    }

    /*at消息*/
    public function at($data)
    {
        global $PAGE;

        $uinfo = new UserInfo();
        $ok = $uinfo->uid($data['uid']);

        if ($ok && $uinfo->name != $data['tag']) {
            return '@'.$uinfo->name;
        } else {
            return '@'.$data['tag'];
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
        return $data['url'];
    }

    /*mailtxt 邮件链接文本*/
    public function mailtxt($data)
    {
        return $data['mail'];
    }

    /*管理员删除的内容*/
    public function adminDelContent($data) {
        return '';
    }

    /*at通知信息（用于推送通知）*/
    public function atMsg($data)
    {
        global $PAGE;

        $uinfo = new UserInfo();
        $uinfo->uid($data['uid']);

        $this->atMsgUrl = str_replace('{$BID}', $PAGE->bid, $data['url']);
        $pos = $data['pos'];

		if (is_array($data['msg'])) {
            $uinfo->setUbbOpt($this);
            $msg = $this->display($data['msg']);
            $msg = preg_replace("#^<!--\s*markdown\s*-->\s+#s", '', $msg);
		} else {
	        $msg = $data['msg'];
		}

        return <<<HTML
@{$uinfo->name} 在 $pos @你：

{$msg}
HTML;
    }
}
