<?php

class UbbDisplay extends XUBBP
{
	// markdown解析器
    protected $Parsedown = NULL;
	// 是否处于markdown模式
	protected $markdownEnable = false;
	
    /*注册显示回调函数*/
    protected $display = array(
		/*开启markdown模式*/
        'markdown'=>'markdown',
        /*text 纯文本*/
        'text' => 'text',
        /*newline 换行*/
        'newline' => 'newline',
        'tab' => 'tab',
        'empty' => 'emptyTag',
        /*link 链接*/
        'url' => 'link',
        'urlzh' => 'link',
        'urlout' => 'link',
        'urlname' => 'urlname',
        /*img 图片*/
        'img' => 'img',
        'imgzh' => 'img',
        'thumb' => 'thumb',
        /*code 代码高亮*/
        'code' => 'code',
        /*time 时间标记*/
        'time' => 'time',
        /*video 视频*/
        'video' => 'video',
        'videoStream' => 'videoStream',
        'audioStream' => 'audioStream',
        /*copyright 版权声明*/
        'copyright' => 'copyright',
        /*battlenet 战网*/
        'battlenet' => 'battlenet',
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
        'atMsg' => 'atMsg',
        /*管理员操作*/
        'adminEdit' => 'adminEditNotice',
        'adminDel' => 'adminDelNotice',
        'delContent' => 'adminDelContent',
        'adminAction' => 'adminActionNotice',
        /*face 表情*/
        'face' => 'face',
    );
	
	public function display($ubbArray, $serialize = false, $maxLen = null, $page = null)
    {
		if ($serialize) {
            $ubbArray = unserialize($ubbArray);
		}
		
		$html = parent::display($ubbArray, false, $maxLen, $page);
		
		if ($this->markdownEnable) {
			if (!$this->Parsedown) {
				$this->Parsedown = new Parsedown();
				//$this->Parsedown->setUrlsLinked(false);
			}
	  
			$html = '<div class="markdown-body">' . $this->Parsedown->text($html) . '</div>';
		}
		
		//$html = nl2br(htmlspecialchars($html)); // debug
		
		return $html;
    }
	
	/*开启markdown模式*/
    public function markdown($data){
		$this->markdownEnable = true;
		return '';
    }
	
    /*text 纯文本*/
    public function text($data)
    {
        $text = str::过滤滥用($data['value']);
		
		if ($this->markdownEnable) {
			return str::htmlTagToEntity($text);
		}
		else {
			return code::html($text, '<br/>');
		}
    }

    /*代码高亮*/
    public function code($data)
    {
        global $PAGE;
		
        if ($PAGE->bid == 'wml') {
            return code::html($data['data'], '<br/>');
        }

        if (isset($data['html'])) {
			return $data['html'];
		}
		else {
			return code::highlight($data['data'], $data['lang']);
		}
    }

    /*time 时间*/
    public function time($data)
    {
        if ($data['tag'] == '') {
            $data['tag'] = 'Y-m-d H:i:s';
        }
        return code::html(date($data['tag']));
    }

    /*link 链接*/
    public function link($data)
    {
        global $PAGE;
        if (is_array($data['title'])) {
            $data['title'] = $this->display($data['title']);
        } else {
            if (trim($data['title']) == '') $data['title'] = $data['url'];
            $data['title'] = code::html($data['title']);
        }

	    if ($data['type'] == 'urlout') $data['url'] = 'http://' . $data['url'];

	    if ($PAGE->bid == 'json' || $data['url'][0] == '#') {
	        $url = $data['url'];
	    } else {
	        $url = $_SERVER['PHP_SELF'] . '/link.url.' . $PAGE->bid . '?url64=' . code::b64e($data['url']);
	    }

        return '<a href="' . code::html($url) . '">' . $data['title'] . '</a>';
    }

    public function urlname($data)
    {
        return '<a name="' . code::html($data['url']) . '">' . code::html($data['title']) . '</a>';
    }

    /*img 图片*/
    public function img($data)
    {
        /*global $PAGE;

        if (preg_match('#^data:image/#is', $data['src'])) {
            $url = $data['src'];
        } else {
            $url = $_SERVER['PHP_SELF'] . '/link.img.' . $PAGE->bid . '?url64=' . code::b64e($data['src']);
        }*/

        //减少HTTP请求次数，不再进行跳转
        $url = $data['src'];

        //百度输入法多媒体输入
        if (preg_match('#^(https?://ci.baidu.com)/([a-zA-Z0-9]+)$#is', $url, $arr)) {
            $prefix = $arr[1];
            $imgId = $arr[2];
            $url = $prefix . '/more?mm=' . $imgId;
        }

        return '<img src="' . code::html($url) . '"' . ($data['alt'] != '' ? ' alt="' . code::html($data['alt']) . '"' : '') . '/>';
    }

    /*thumb 缩略图*/
    public function thumb($data)
    {
        $src = code::html($data['src']);

        //百度输入法多媒体输入
        if (preg_match('#^(https?://ci\.baidu\.com)/([a-zA-Z0-9]+)$#is', $src, $arr)) {
            $prefix = $arr[1];
            $imgId = $arr[2];
            $src = $prefix . '/more?mm=' . $imgId;
        }

        return '<a href="' . $src . '"><img src="http://s.image.wap.soso.com/img/' . floor($data['w']) . '_' . floor($data['h']) . '_0_0_' . $src . '" alt="点击查看大图"/></a>';
    }

    /*video 视频*/
    public function video($data)
    {
        static $id = 0;
        $id ++;

        $url = $data['url'];
        $iframeUrl = null;

        //优酷
        if (preg_match('#\.youku\.com/.*_([a-zA-Z0-9=]+)#', $url, $arr)) {
            $iframeUrl = 'http://player.youku.com/embed/'.$arr[1];
        }
        //土豆
        else if (preg_match('#\.tudou\.com/.*/([a-zA-Z0-9=]+)#', $url, $arr)) {
            $iframeUrl = 'http://www.tudou.com/programs/view/html5embed.action?code='.$arr[1];
        }
        //腾讯视频
        else if (preg_match('#\.qq\.com/.*/([a-zA-Z0-9=]+)#', $url, $arr)) {
            $iframeUrl = 'http://v.qq.com/iframe/player.html?vid='.$arr[1].'&tiny=0&auto=0';
        }


        if (null !== $iframeUrl) {
            return '<p class="video_box"><iframe class="video" id="video_site_'.$id.'" src="'.code::html($iframeUrl).'" seamless="seamless"><a href="'.code::html($url).'">'.code::html($url).'</a></iframe></p><script>(function(){var box=document.getElementById("video_site_'.$id.'");box.style.height=(box.offsetWidth*2/3)+\'px\';})()</script>';
        }
        else {
            return '<p class="video_box"><a href="'.code::html($url).'">'.code::html($url).'</a></p>';
        }
    }

    /*videoStream 视频流*/
    public function videoStream($data)
    {
        static $id = 0;
        $id ++;
        $url = $data['url'];

        //百度输入法多媒体输入
        if (preg_match('#^(https?://ci.baidu.com)/([a-zA-Z0-9]+)$#is', $url, $arr)) {
            $prefix = $arr[1];
            $imgId = $arr[2];
            $url = $prefix . '/more?mm=' . $imgId;
        }

        return '<p class="video_box"><video class="video" id="video_stream_'.$id.'" src="'.code::html($url).'" controls="controls"><a href="'.code::html($url).'">'.code::html($url).'</a></video></p><script>(function(){var box=document.getElementById("video_stream_'.$id.'");box.style.height=(box.offsetWidth*2/3)+\'px\';})()</script>';
    }

    /*audioStream 音频流*/
    public function audioStream($data)
    {
        $url = $data['url'];

        //百度输入法多媒体输入
        if (preg_match('#^(https?://ci.baidu.com)/([a-zA-Z0-9]+)$#is', $url, $arr)) {
            $prefix = $arr[1];
            $imgId = $arr[2];
            $url = $prefix . '/more?mm=' . $imgId;
        }

        return '<p class="audio_box"><audio class="audio" src="'.code::html($url).'" controls="controls"><a href="'.code::html($url).'">'.code::html($url).'</a></audio></p>';
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
            return '<a rel="license" href="http://creativecommons.org/licenses/' . $en . '/3.0/cn/"><img alt="知识共享许可协议|Creative Commons" style="border-width:0" src="http://i.creativecommons.org/l/' . $en . '/3.0/cn/88x31.png" /></a><br/>本作品采用<a rel="license" href="http://creativecommons.org/licenses/' . $en . '/3.0/cn/">知识共享' . $cn . '3.0许可协议</a>进行许可。';
        }

        if ($x == 'gfdl') {
            return '本作品采用<a rel="license" href="http://baike.baidu.com/view/20722.htm">GNU自由文档许可证</a>进行许可。';
        }
        if ($x == '公有领域' or $x == '公共领域') {
            return '本作品属于<a rel="license" href="http://baike.baidu.com/view/556002.htm">公有领域</a>。';
        }
        return '本作品采用' . code::html($data['tag']) . '进行许可。';
    }

    /*battlenet 战网*/
    public function battlenet($data)
    {
        if ($data['server'] != '') {
            if ($data['display'] == null) {
                $data['display'] = "{$data['name']}@{$data['server']}";
            }

            return '<a href="http://www.battlenet.com.cn/wow/zh/character/' . urlencode($data['server']) . '/' . urlencode($data['name']) . '/simple">' . code::html($data['display']) . '</a>';
        } else {
            if ($data['display'] == null) {
                $data['display'] = "{$data['name']}";
            }

            return '<a href="http://www.battlenet.com.cn/wow/zh/search?q=' . urlencode($data['name']) . '&amp;f=wowcharacter">' . code::html($data['display']) . '</a>';
        }
    }

    /*newline 换行*/
    public function newline($data)
    {
        if (in_array($data['tag'], ['hr', '＜＜＜'])) {
            return '<br/>--------<br/>';
        } else {
            return '<br/>';
        }
    }

    /*tab 4em空格*/
    public function tab($data)
    {
        return '　　';
    }

    /*empty UBB转义*/
    public function emptyTag($data)
    {
        return '';
    }

    /*layout 布局*/
    public function layout($data)
    {
        if ($data['tag'][0] != '/') {
            $dataEnd = $data;
            $dataEnd['tag'] = '/' . $data['tag'];
            $this->regEndTag('/' . $data['tag'], 'layout', $dataEnd);
            switch ($data['tag']) {
                case 'b':
                    return '<span style="font-weight:bold">';
                case 'i':
                    return '<span style="font-style:italic">';
                case 'u':
                    return '<span style="text-decoration:underline">';
                case 'center':
                case 'left':
                case 'right':
                    return '<span style="text-align:' . $data['tag'] . '">';
                default:
                    return '<span>';
            }
        } else {
            $html = '';
            if ($this->rmEndTag($data['tag'], $html)) {
                return $html . '</span>';
            } else {
                return '';
            }
        }
    }

    /*style 风格*/
    public function style($data)
    {
        $disable = $this->getOpt('style.disable');

        if ($data['tag'][0] != '/') {
            if ($disable) {
                return '<div style="border:red solid 1px">由于该用户使用div和span标签破坏论坛版面，影响其他人正常的发言和聊天，该用户的div和span标签已被禁用。请大家引以为戒！</div>';
            }

            $dataEnd = $data;
            $dataEnd['tag'] = '/' . $data['tag'];
            $this->regEndTag('/' . $data['tag'], 'style', $dataEnd);

            $opt = &$data['opt'];

            if (!empty($opt)) {
                $opt = preg_replace('#/\*.*\*/#sU', '', $opt);
                $opt = preg_replace('#position\s*:[^;]*;?#is', '', $opt);
            }

            switch ($data['tag']) {
                case 'color':
                    return '<span style="color:' . code::html($data['opt'], false, true) . '">';
                case 'div':
                    return '<div style="' . code::html($data['opt'], false, true) . '">';
                case 'span':
                    return '<span style="' . code::html($data['opt'], false, true) . '">';
            }
        } else {
            if ($disable) {
                return '';
            }

            $html = '';
            if ($this->rmEndTag($data['tag'], $html)) {
                switch ($data['tag']) {
                    case '/color':
                        $html .= '</span>';
                        break;
                    case '/div':
                        $html .= '</div>';
                        break;
                    case '/span':
                        $html .= '</span>';
                        break;
                }
                return $html;
            } else {
                return '';
            }
        }
    }

    /*urltxt 网址文本*/
    public function urltxt($data)
    {
        global $PAGE;

        //百度输入法多媒体输入
        if (preg_match('#^(https?://ci.baidu.com)/([a-zA-Z0-9]+)$#is', $data['url'], $arr)) {
            $prefix = $arr[1];
            $imgId = $arr[2];
            $url = code::html($data['url']);
            $imgUrl = code::html($prefix . '/more?mm=' . $imgId);

            static $id = 0;
            $id ++;

            if (1 === $id) {
                $script = <<<HTML
<script>
    if ('function' != typeof baidu_media_change) {
    baidu_media_change = function (id, hideTag, showTag) {
    console.log(id,hideTag,showTag);
        var hideDom = document.getElementById('baidu_media_' + hideTag + '_' + id);
        var showDom = document.getElementById('baidu_media_' + showTag + '_' + id);
        if ('audio' == showTag) { showDom.src = hideDom.src; }
        hideDom.style.display = 'none';
        showDom.style.display = 'inline';
    }}
</script>
HTML;
            } else {
                $script = '';
            }


            return <<<HTML
<div class="baidu_media_box">
    <p>多媒体输入（<a id="baidu_media_link_{$id}" href="{$url}">{$url}</a>）</p>
    <img id="baidu_media_img_{$id}" src="{$imgUrl}" al="图片加载中" onerror="baidu_media_change({$id}, 'img', 'audio')" />
    <audio id="baidu_media_audio_{$id}" class="audio" style="display:none" controls="controls" onerror="baidu_media_change({$id}, 'audio', 'txt')"></audio>
    <span id="baidu_media_txt_{$id}" style="display:none">内容无法解析，请点击上方链接查看↑</span>
</div>
{$script}
HTML;
        } else {
            if ($PAGE->bid == 'json') {
                $url = $data['url'];
            } else {
                $url = $_SERVER['PHP_SELF'] . '/link.url.' . $PAGE->bid . '?url64=' . code::b64e($data['url']);
            }

            return '<a href="' . code::html($url) . '">' . code::html($data['url']) . '</a>';
        }
    }

    /*mailtxt 邮箱文本*/
    public function mailtxt($data)
    {
        return '<a href="mailto:' . code::html($data['mail']) . '">' . code::html($data['mail']) . '</a>';
    }

    /*at用户名*/
    public function at($data)
    {
        global $PAGE;

        $jsFunc = $this->getOpt('at.jsFunc');
		$uid = (int)$data['uid'];
        $uinfo = new UserInfo();
        $uinfo->uid($uid);
        $name = $uinfo->name === null ? $data['tag'] : $uinfo->name;

        if ($jsFunc) {
            return '<a href="#" onclick="' . $jsFunc . '(\'' . $name . '\',this);return false">@</a><a href="user.info.' . $uid . '.' . $PAGE->bid . '">' . code::html($name) . '</a>';
        } else {
            return '<a href="user.info.' . $uid . '.' . $PAGE->bid . '">@' . code::html($name) . '</a>';
        }
    }

    /*at通知信息*/
    public function atMsg($data)
    {
        global $PAGE;

        $url = code::html(str_replace('{$BID}', $PAGE->bid, $data['url']));
        $pos = code::html($data['pos']);
        $msg = code::html($data['msg']);
        $uinfo = new UserInfo();
        $uinfo->uid($data['uid']);

        return <<<HTML
<a href="user.info.{$uinfo->uid}.{$PAGE->bid}">{$uinfo->name}</a> 在 <a href="{$url}">{$pos}</a> at你：
<blockquote>
{$msg}
</blockquote>
HTML;
    }

    /*管理员编辑通知信息*/
    public function adminEditNotice($data)
    {
        global $PAGE;

        $url = code::html($data['url']);
        $pos = code::html($data['pos']);
        $reason = code::html($data['reason']);
        $uinfo = new UserInfo();
        $uinfo->uid($data['uid']);
        $oriData = $this->display($data['oriData']);

        return <<<HTML
管理员 <a href="user.info.{$uinfo->uid}.{$PAGE->bid}">{$uinfo->name}</a> 编辑了您在 <a href="{$url}">{$pos}</a> 的发言，编辑理由如下：
<blockquote>
{$reason}
</blockquote>
您发言的原始内容如下：
<blockquote>
{$oriData}
</blockquote>
HTML;
    }

    /*管理员删除通知信息*/
    public function adminDelNotice($data)
    {
        global $PAGE, $USER;

        $url = code::html($data['url']);
        $pos = code::html($data['pos']);
        $reason = code::html($data['reason']);
        $uinfo = new UserInfo();
        $uinfo->uid($data['uid']);
        $oriData = $this->display($data['oriData']);

        if ($data['uid'] == $data['ownUid']) {
            $own = "您";
            $reason = "。";
        } else {
            $own = "管理员 <a href=\"user.info.{$uinfo->uid}.{$PAGE->bid}\">{$uinfo->name}</a> ";

            $reason = <<<HTML
，理由如下：
<blockquote>
{$reason}
</blockquote>
HTML;

        }

        return <<<HTML
{$own}删除了您在 <a href="{$url}">{$pos}</a> 的发言{$reason}
您发言的原始内容如下：
<blockquote>
{$oriData}
</blockquote>
HTML;
    }

    /*管理员操作通知信息*/
    public function adminActionNotice($data)
    {
        global $PAGE, $USER;

        $actName = [
            bbs::ACTION_SINK_TOPIC => '下沉',
        ];

        $act = $actName[$data['act']];
        $url = code::html($data['url']);
        $pos = code::html($data['pos']);
        $reason = code::html($data['reason']);
        $uinfo = new UserInfo();
        $uinfo->uid($data['uid']);

        if ($data['uid'] == $data['ownUid']) {
            $own = "您";
            $reason = "。";
        } else {
            $own = "管理员 <a href=\"user.info.{$uinfo->uid}.{$PAGE->bid}\">{$uinfo->name}</a> ";

            $reason = <<<HTML
，理由如下：
<blockquote>
{$reason}
</blockquote>
HTML;

        }

        return <<<HTML
{$own}{$act}了您的 <a href="{$url}">{$pos}</a>{$reason}
HTML;
    }

    /*管理员删除的内容*/
    public function adminDelContent($data)
    {
        global $PAGE, $USER;

        $reason = code::html($data['reason']);
        $uinfo = new UserInfo();
        $uinfo->uid($data['uid']);

        $jsFunc = $this->getOpt('at.jsFunc');
        $name = $uinfo->name === null ? $data['tag'] : $uinfo->name;

        if ($jsFunc) {
            $admin = '<a href="#" onclick="' . $jsFunc . '(\'' . $name . '\',this);return false">@</a><a href="user.info.' . code::html($data['uid']) . '.' . $PAGE->bid . '">' . code::html($name) . '</a>';
        } else {
            $admin = '<a href="user.info.' . code::html($data['uid']) . '.' . $PAGE->bid . '">@' . code::html($name) . '</a>';
        }

        $time = '';

        if (isset($data['time'])) {
            $time = '于 ' . date('Y-m-d H:i ', $data['time']);
        }

        if ($data['uid'] == $data['ownUid']) {
            $own = '层主';
            $reason = '。';
        } else {
            $own = '管理员';

            $reason = <<<HTML
，理由如下：
<p>{$reason}</p>
HTML;
        }

        return <<<HTML
<div class="tp info-box">
{$own} {$admin} {$time}删除了该楼层{$reason}
</div>
HTML;
    }

    /*face 表情*/
    public function face($data)
    {
        global $PAGE;

        $path = 'img/face/' . bin2hex($data['face']) . '.gif';

        try {
            $url = $PAGE->getTplUrl($path);

            if ($PAGE->bid == 'json') {
                $url = $PAGE->getUrlPrefix() . $url;
            }

            $html = '<img title="' . code::html($data['face']) . '" src="' . code::html($url) . '" />';
        } catch (Exception $e) {
            $html = code::html('{' . $data['face'] . '}');
        }

        return $html;
    }
}
