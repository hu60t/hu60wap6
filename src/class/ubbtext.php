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
        /*audio 音频*/
        'audio' => 'video',
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
        /*at通知信息（用于推送通知）*/
        'atMsg' => 'atMsg',
        /*face 表情*/
        'face' => 'face',
        /*iframe 网页嵌入*/
        'iframe' => 'iframe',
        /* html 通过iframe的srcdoc属性实现的HTML内容嵌入 */
        'html' => 'html',
        /* textbox 文本框 */
        'textbox' => 'textbox',
        /*管理员操作*/
        'adminEdit' => 'adminEditNotice',
        'adminDel' => 'adminDelNotice',
        'delContent' => 'adminDelContent',
        'adminAction' => 'adminActionNotice',
		'postNeedReview' => 'postNeedReviewNotice',
    );

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
        return $data['src'];
    }

    /*video 视频*/
    public function video($data)
    {
        if (empty($data['title'])) {
            return $data['url'];
        } else {
            return $data['title'] . '：' . $data['url'];
        }
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
        return '本作品采用' . $data['tag'] . '进行许可。';
    }

    /*battlenet 战网*/
    public function battlenet($data)
    {
        $name = $data['name'];
        if ($data['server'] != '') {
            $name .= '@' . $data['server'];
        }
        if ($data['display'] != null) {
            $name .= "，" . $data['display'];
        }
        return '《战网：' . $name . '》';
    }

    /*math 数学公式*/
    public function math($data) {
        $content = $data['data'];
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
        return '{' . $data['face'] . '}';
    }

    /*iframe 网页嵌入*/
    public function iframe($data) {
        $data = $data['data'];
        if (isset($data['src'])) {
            return $data['src'];
        }
        if (isset($data['srcdoc'])) {
            return strip_tags($data['srcdoc']);
        }
        return '';
    }

    /*html 通过iframe的srcdoc属性实现的HTML内容嵌入*/
    public function html($data) {
        return strip_tags($data['data']);
    }

    /*textbox 文本框*/
    public function textbox($data) {
        return $data['data'];
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

    /*at通知信息（用于推送通知）*/
    public function atMsg($data)
    {
        global $PAGE;

        $uinfo = new UserInfo();
        $uinfo->uid($data['uid']);

        $url = str_replace('{$BID}', $PAGE->bid, $data['url']);
        $this->setOpt('atMsg.Url', $url);

        $pos = $data['pos'];
        if (!$this->getOpt('display.textWithoutUrl')) {
            $url = SITE_URL_BASE.$url;
            $pos .= " $url";
        }

		if (is_array($data['msg'])) {
            $uinfo->setUbbOpt($this);
            $msg = $this->display($data['msg']);
            $msg = trim(preg_replace("#^<!--\s*markdown\s*-->\s+#s", '', $msg));
		} else {
	        $msg = trim($data['msg']);
		}

        return <<<HTML
@{$uinfo->name} 在 $pos @你：

{$msg}
HTML;
    }

    /*管理员编辑通知信息*/
    public function adminEditNotice($data)
    {
        $pos = $data['pos'];
        if (!$this->getOpt('display.textWithoutUrl')) {
            $url = SITE_URL_BASE.$data['url'];
            $pos .= " $url";
        }
        $reason = $data['reason'];
        $uinfo = new UserInfo();
        $uinfo->uid($data['uid']);
        $oriData = $this->display($data['oriData']);

        return <<<HTML
管理员 {$uinfo->name} 编辑了您在 {$pos} 的发言，编辑理由如下：

{$reason}

您发言的原始内容如下：

{$oriData}
HTML;
    }

    /*管理员删除通知信息*/
    public function adminDelNotice($data)
    {
        $pos = $data['pos'];
        if (!$this->getOpt('display.textWithoutUrl')) {
            $url = SITE_URL_BASE.$data['url'];
            $pos .= " $url";
        }
        $reason = $data['reason'];
        $uinfo = new UserInfo();
        $uinfo->uid($data['uid']);
        $oriData = $this->display($data['oriData']);

        if ($data['uid'] == $data['ownUid']) {
            $own = "您";
            $reason = "。";
        } else {
            $who = ($data['uid'] == $data['topicUid']) ? '楼主' : '管理员';
            $own = "$who {$uinfo->name} ";

            $reason = <<<HTML
，理由如下：

{$reason}

HTML;

        }

        return <<<HTML
{$own}删除了您在 {$pos} 的发言{$reason}
您发言的原始内容如下：

{$oriData}
HTML;
    }

    /*管理员操作通知信息*/
    public function adminActionNotice($data)
    {
        $actName = [
            bbs::ACTION_SINK_TOPIC => '下沉',
            bbs::ACTION_ADD_BLOCK_POST => '已将您禁言',
            bbs::ACTION_REMOVE_BLOCK_POST => '将您解除禁言',
            bbs::ACTION_SET_ESSENCE_TOPIC => '加精',
            bbs::ACTION_UNSET_ESSENCE_TOPIC => '取消精华',
            bbs::ACTION_REPLY_LOCK => '关闭评论',
            bbs::ACTION_REPLY_UNLOCK => '开放评论',
        ];

        $act = $actName[$data['act']];
        $pos = $data['pos'];
        if (!$this->getOpt('display.textWithoutUrl')) {
            $url = SITE_URL_BASE.$data['url'];
            $pos .= " $url";
        }
        $reason = $data['reason'];
        $uinfo = new UserInfo();
        $uinfo->uid($data['uid']);

	    if (in_array($data['act'], [bbs::ACTION_ADD_BLOCK_POST, bbs::ACTION_REMOVE_BLOCK_POST])) {
		    return <<<HTML
管理员 {$uinfo->name} {$act}，理由如下：

{$reason}
HTML;
	    }
	    else {
	        if ($data['uid'] == $data['ownUid']) {
        	    $own = "您";
	            $reason = "。";
	        } else {
        	    $own = "管理员 {$uinfo->name} ";

	            $reason = <<<HTML
，理由如下：

{$reason}

HTML;

        	}

	        return <<<HTML
{$own}将您的 {$pos} {$act}{$reason}
HTML;
	    }
    }

    /*管理员删除的内容*/
    public function adminDelContent($data)
    {
        $reason = $data['reason'];
        $uinfo = new UserInfo();
        $uinfo->uid($data['uid']);

        $admin = $uinfo->name === null ? $data['tag'] : $uinfo->name;

        $time = '';

        if (isset($data['time'])) {
            $time = '于 ' . date('Y-m-d H:i ', $data['time']);
        }

        if ($data['uid'] == $data['ownUid']) {
            $own = '层主';
        } elseif ($data['uid'] == $data['topicUid']) {
            $own = '楼主';
        } else {
            $own = '管理员';
        }

        if (!empty($reason)) {
            $reason = <<<HTML
，理由如下：

{$reason}
HTML;
        } else {
            $reason = '。';
        }

        return <<<HTML
{$own} {$admin} {$time}删除了该楼层{$reason}
HTML;
    }

	/*待审核的内容*/
	public function postNeedReviewNotice($data) {
        $stat = bbs::getReviewStatName($data['stat']);
        return <<<HTML
发言{$stat}，仅管理员和作者本人可见。

HTML;
	}

}
