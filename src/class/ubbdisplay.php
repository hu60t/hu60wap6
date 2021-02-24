<?php

class UbbDisplay extends XUBBP
{
	// markdown解析器
    protected $Parsedown = NULL;
	// 是否处于markdown模式
    protected $markdownEnable = false;
    // markdown受保护内容（防止markdown解析干扰这些内容）
    protected $mdProtectTags = [];
    protected $mdProtectTexts = [];
	
    /*注册显示回调函数*/
    protected $display = array(
		/*开启markdown模式*/
        'markdown'=>'markdown',
		/*markdown受保护内容（不被XUBBP解析器干扰）*/
		'mdpre' => 'mdpre',
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
		'mdcode' => 'code',
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
        'atMsg' => 'atMsg',
        /*face 表情*/
        'face' => 'face',
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
            return '<div class="tp info-box userblocked">用户被禁言，发言自动屏蔽。</div>';
        }
		
		if ($serialize) {
            $ubbArray = unserialize($ubbArray);
		}
		
		$this->markdownEnable = false;
		
		$html = parent::display($ubbArray, false, $maxLen, $page);
		
		if ($this->markdownEnable) {
			if (!$this->Parsedown) {
				$this->Parsedown = new Parsedown();
				$this->Parsedown->setBreaksEnabled(true); //自动换行
				//$this->Parsedown->setMarkupEscaped(true); //转义html
				//$this->Parsedown->setUrlsLinked(false); //不解析链接
			}

            $html = '<div class="markdown-body">' . $this->Parsedown->text($html) . '</div>';

            // 还原被保护的内容
            $html = str_replace($this->mdProtectTags, $this->mdProtectTexts, $html);
            $this->mdProtectTags = [];
            $this->mdProtectTexts = [];
		}
		
		//$html = nl2br(htmlspecialchars($html)); // debug
		
		return $html;
    }
	
	/*开启markdown模式*/
    public function markdown($data){
		$this->markdownEnable = true;
		return '';
    }
	
	/*markdown受保护内容（不被XUBBP解析器干扰）*/
	public function mdpre($data){
		return $data['data'];
    }

    /* 保护内容免受markdown解析的干扰 */
    public function markdownProtect($html) {
        if ($this->markdownEnable) {
            $this->mdProtectTexts[] = $html;
            /*
             * 添加块级的<p>标签是为了确保上下文关系不被破坏，比如
             * 
             * aaa
             * ```
             * xxx
             * ```
             * ------------
             * 
             * 这条线本来应该原样保留，如果保护标记不是块级的，
             * 保护后它和上面的aaa结合，就会使aaa成为二级标题。
             */
            $html = "<p>\2".count($this->mdProtectTexts)."\3</p>";
            $this->mdProtectTags[] = $html;
        }
        return $html;
    }

    /* 保护inline内容免受markdown解析的干扰 */
    public function markdownProtectInline($html) {
        if ($this->markdownEnable) {
            $this->mdProtectTexts[] = $html;
            $html = "\2".count($this->mdProtectTexts)."\3";
            $this->mdProtectTags[] = $html;
        }
        return $html;
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
        
        //不再把代码高亮交给markdown引擎处理，而是由PHP统一处理
		/*if (empty($data['lang']) && $this->markdownEnable) {
            $quote = isset($data['quote']) ? $data['quote'] : '```';
			return $quote . $data['data'] . $quote;
        }*/

        $code = $data['data'];

        // 去除开头的第一个换行符
        if (strlen($code) > 1) {
            if ($code[0] == "\r") {
                $code = substr($code, 1);
            }
            if ($code[0] == "\n") {
                $code = substr($code, 1);
            }
        }

        if ($data['lang'] == 'latex') {
            $html = '<latex-js class="userlatex">'.htmlspecialchars($code).'</latex-js>';
        } else {
            $html = '<pre class="hu60_code"><code class="'.code::html($data['lang']).'">'.code::html($code).'</code></pre>';
        }

        /*if (isset($data['html'])) {
			$html = $data['html'];
		}
		else {
			$html = code::highlight($data['data'], $data['lang']);
        }*/

        return $this->markdownProtect($html);
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
            $ubb = new UbbDisplay;
            $ubb->opt = $this->opt;
            $data['title'] = $ubb->display($data['title']);
        } else {
            if (trim($data['title']) == '') $data['title'] = $data['url'];
            $data['title'] = code::html($data['title']);
        }

	    if ($data['type'] == 'urlout') $data['url'] = 'http://' . $data['url'];

	    if (JsonPage::isJsonPage() || $data['url'][0] == '#') {
	        $url = $data['url'];
	    } else {
	        $url = SITE_ROUTER_PATH . '/link.url.' . $PAGE->bid . '?url64=' . code::b64e($data['url']);
	    }

        return '<a class="userlink" href="' . code::html($url) . '">' . $data['title'] . '</a>';
    }

    public function urlname($data)
    {
        return '<a class="usertarget" name="' . code::html($data['url']) . '">' . code::html($data['title']) . '</a>';
    }

    /*img 图片*/
    public function img($data)
    {
        global $PAGE;

        $url = $data['src'];

        //百度输入法多媒体输入
        if (preg_match('#^(https?://ci.baidu.com)/([a-zA-Z0-9]+)$#is', $url, $arr)) {
            $prefix = $arr[1];
            $imgId = $arr[2];
            $url = $prefix . '/more?mm=' . $imgId;
        }
        
		if (!JsonPage::isJsonPage() && !preg_match('#^data:image/#is', $url)) {
            $url = SITE_ROUTER_PATH . '/link.img.' . $PAGE->bid . '?url64=' . code::b64e($url);
        }

        if (!$data['in_link'])
		    return '<a class="userimglink" href="'.code::html($url).'"><img class="userimg" src="' . code::html($url) . '"' . ($data['alt'] != '' ? ' alt="' . ($alt = code::html($data['alt'])) . '" title="' . $alt . '"' : '') . '/></a>';
	    else
		    return '<img class="userimg" src="' . code::html($url) . '"' . ($data['alt'] != '' ? ' alt="' . ($alt = code::html($data['alt'])) . '" title="' . $alt . '"' : '') . '/>';

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
        
        if (JsonPage::isJsonPage()) {
            $url = $src;
            $base = SITE_URL_BASE;
        } else {
            $url = SITE_ROUTER_PATH . '/link.img.' . $PAGE->bid . '?url64=' . code::b64e($src);
            $base = '';
        }

        if (!$data['in_link']) {
        	return '<a class="userthumblink" href="' . $url . '"><img class="userthumb" src="' . $base . 'link.thumb.' . floor($data['w']) . '.' . floor($data['h']) . '.' . bin2hex($src) . '.png" alt="点击查看大图"/></a>';
        }
        else {
        	return '<img class="userthumb" src="' . $base . 'link.thumb.' . floor($data['w']) . '.' . floor($data['h']) . '.' . bin2hex($src) . '.png" alt="点击进入链接"/>';
        }
    }

    /*video 视频*/
    public function video($data)
    {
        global $PAGE;

        static $id = 0;
        $id ++;

        $url = $data['url'];
        $iframeUrl = null;
		$heightJs = 'box.offsetWidth*2/3';

        //优酷
        if (preg_match('#\.youku\.com/.*/id_([a-zA-Z0-9=]+)#', $url, $arr)) {
            $iframeUrl = 'https://player.youku.com/embed/'.$arr[1];
        }
        //土豆（失效）
        //else if (preg_match('#\.tudou\.com/.*/([a-zA-Z0-9=]+)#', $url, $arr)) {
        //    $iframeUrl = 'https://www.tudou.com/programs/view/html5embed.action?code='.$arr[1];
        //}
		//全民K歌
        else if (preg_match('#kg.*\.qq\.com/.*\bs=([a-zA-Z0-9=]+)#', $url, $arr)) {
            $iframeUrl = 'https://kg.qq.com/node/play?s='.$arr[1];
			$heightJs = 'box.offsetWidth';
        }
        //腾讯视频
        else if (preg_match('#\.qq\.com/.*/([a-zA-Z0-9=]+)#', $url, $arr)) {
            $iframeUrl = 'https://v.qq.com/txp/iframe/player.html?vid='.$arr[1];
        }
        //哔哩哔哩 av号
        else if (preg_match('#\b(?:bilibili\.com|b23\.tv)\b.*\bav(\d+)(?:.*\bp=(\d+))?#', $url, $arr)) {
            $iframeUrl = 'https://player.bilibili.com/player.html?aid='.$arr[1].'&page='.$arr[2];
        }
        //哔哩哔哩 BV号
        else if (preg_match('#\b(?:bilibili\.com|b23\.tv)\b.*\b(BV[\w]+)(?:.*\bp=(\d+))?#', $url, $arr)) {
            $iframeUrl = 'https://player.bilibili.com/player.html?bvid='.$arr[1].'&page='.$arr[2];
        }

        if (null !== $iframeUrl) {
            $html = '<p class="video_box"><a class="uservideosite" target="_blank" href="'.code::html($url).'">视频链接</a><br/><iframe class="video" id="video_site_'.$id.'" src="'.code::html($iframeUrl).'" seamless allowfullscreen><a href="'.code::html($url).'">'.code::html($url).'</a></iframe></p><script>(function(){var box=document.getElementById("video_site_'.$id.'");box.style.height=('.$heightJs.')+\'px\';})()</script>';
        }
        else {
            if (JsonPage::isJsonPage()) {
                $link = $url;
            } else {
                $link = SITE_ROUTER_PATH . '/link.url.' . $PAGE->bid . '?url64=' . code::b64e($data['url']);
            }
            $html = '<p class="video_box"><a class="uservideosite" target="_blank" href="'.code::html($link).'">'.code::html($url).'</a></p>';
        }

        return $this->markdownProtect($html);
    }

    /*videoStream 视频流*/
    public function videoStream($data)
    {
        global $PAGE;

        static $id = 0;
        $id ++;
        $url = $data['url'];

		if (QINIU_USE_HTTPS) {
			$url = preg_replace('#^http://'.QINIU_STORAGE_HOST.'/#i', 'https://'.QINIU_STORAGE_HOST.'/', $url);
		}

        //百度输入法多媒体输入
        if (preg_match('#^(https?://ci.baidu.com)/([a-zA-Z0-9]+)$#is', $url, $arr)) {
            $prefix = $arr[1];
            $imgId = $arr[2];
            $url = $prefix . '/more?mm=' . $imgId;
        }
        
        if (JsonPage::isJsonPage()) {
            $link = $data['url'];
        } else {
            $link = SITE_ROUTER_PATH . '/link.url.' . $PAGE->bid . '?url64=' . code::b64e($data['url']);
        }

        return '<p class="video_box"><a class="uservideolink" target="_blank" href="'.code::html($link).'">视频链接</a><br/><video class="video" id="video_stream_'.$id.'" src="'.code::html($url).'" controls="controls"></video></p><script>(function(){var box=document.getElementById("video_stream_'.$id.'");box.style.height=(box.offsetWidth*2/3)+\'px\';})()</script>';
    }

    /*audioStream 音频流*/
    public function audioStream($data)
    {
        global $PAGE;

        $url = $data['url'];
		
		if (QINIU_USE_HTTPS) {
			$url = preg_replace('#^http://'.QINIU_STORAGE_HOST.'/#i', 'https://'.QINIU_STORAGE_HOST.'/', $url);
		}


        //百度输入法多媒体输入
        if (preg_match('#^(https?://ci.baidu.com)/([a-zA-Z0-9]+)$#is', $url, $arr)) {
            $prefix = $arr[1];
            $imgId = $arr[2];
            $url = $prefix . '/more?mm=' . $imgId;
        }

        if (JsonPage::isJsonPage()) {
            $link = $data['url'];
        } else {
            $link = SITE_ROUTER_PATH . '/link.url.' . $PAGE->bid . '?url64=' . code::b64e($data['url']);
        }

        return '<p class="audio_box"><a class="useraudiolink" target="_blank" href="'.code::html($link).'">音频链接</a><br/><audio class="audio" src="'.code::html($url).'" controls="controls"></audio></p>';
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

            return '<a class="userbattlenet" href="http://www.battlenet.com.cn/wow/zh/character/' . urlencode($data['server']) . '/' . urlencode($data['name']) . '/simple">' . code::html($data['display']) . '</a>';
        } else {
            if ($data['display'] == null) {
                $data['display'] = "{$data['name']}";
            }

            return '<a class="userbattlenet" href="http://www.battlenet.com.cn/wow/zh/search?q=' . urlencode($data['name']) . '&amp;f=wowcharacter">' . code::html($data['display']) . '</a>';
        }
    }

    /*math 数学公式*/
    public function math($data) {
        $html = '<hu60-math class="usermath">'.code::html($data['data']).'</hu60-math>';

        return $this->markdownProtect($html);
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
            if ($this->getOpt("style.hideUserCSS")) {
                return '';
            }

            if ($disable) {
                if ($disable !== 'noticed') {
                    $this->setOpt('style.disable', 'noticed');
                    return '<div class="tp info-box">由于该用户使用div和span标签破坏论坛版面，影响其他人正常的发言和聊天，该用户的div和span标签已被禁用。请大家引以为戒！</div>';
                } else {
                    return '';
                }
            }

            $dataEnd = $data;
            $dataEnd['tag'] = '/' . $data['tag'];
            $this->regEndTag('/' . $data['tag'], 'style', $dataEnd);

            $opt = &$data['opt'];

            if (!empty($opt)) {
                $opt = preg_replace('#/\*.*\*/#sU', '', $opt);
                $opt = preg_replace('#position\s*:[^;]*;?#is', '', $opt);
            }

            $uid = (int)($this->getOpt('uid'));
            switch ($data['tag']) {
                case 'color':
                    return '<span class="usercss uid-'.$uid.'" style="color:' . $this->markdownProtectInline(code::html($data['opt'], false, true)) . '">';
                case 'div':
                    return '<div class="usercss uid-'.$uid.'" style="' . $this->markdownProtectInline(code::html($data['opt'], false, true)) . '">';
                case 'span':
                    return '<span class="usercss uid-'.$uid.'" style="' . $this->markdownProtectInline(code::html($data['opt'], false, true)) . '">';
            }
        } else {
            if ($this->getOpt("style.hideUserCSS")) {
                return '';
            }

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
            if (JsonPage::isJsonPage()) {
                $url = $data['url'];
            } else {
                $url = SITE_ROUTER_PATH . '/link.url.' . $PAGE->bid . '?url64=' . code::b64e($data['url']);
            }

            return '<a class="userlink" href="' . code::html($url) . '">' . code::html($data['url']) . '</a>';
        }
    }

    /*mailtxt 邮箱文本*/
    public function mailtxt($data)
    {
        return '<a class="usermail" href="mailto:' . code::html($data['mail']) . '">' . code::html($data['mail']) . '</a>';
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
            return '<a class="userat" href="#" onclick="' . $jsFunc . '(\'' . $name . '\',this);return false">@</a><a class="userinfo" href="user.info.' . $uid . '.' . $PAGE->bid . '">' . code::html($name) . '</a>';
        } else {
            return '<a class="userinfo" href="user.info.' . $uid . '.' . $PAGE->bid . '">@' . code::html($name) . '</a>';
        }
    }

    /*at通知信息*/
    public function atMsg($data)
    {
        global $PAGE;

        $uinfo = new UserInfo();
        $uinfo->uid($data['uid']);

        $url = code::html(str_replace('{$BID}', $PAGE->bid, $data['url']));
        $pos = code::html($data['pos']);

		if (is_array($data['msg'])) {
            $uinfo->setUbbOpt($this);
			$msg = $this->display($data['msg']);
		} else {
	        $msg = code::html($data['msg']);
		}

        return <<<HTML
<a class="userinfo" href="user.info.{$uinfo->uid}.{$PAGE->bid}">{$uinfo->name}</a> 在 <a class="hu60_pos" href="{$url}">{$pos}</a> at你：
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
管理员 <a class="userinfo" href="user.info.{$uinfo->uid}.{$PAGE->bid}">{$uinfo->name}</a> 编辑了您在 <a class="hu60_pos" href="{$url}">{$pos}</a> 的发言，编辑理由如下：
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
            $own = "管理员 <a class=\"userinfo\" href=\"user.info.{$uinfo->uid}.{$PAGE->bid}\">{$uinfo->name}</a> ";

            $reason = <<<HTML
，理由如下：
<blockquote>
{$reason}
</blockquote>
HTML;

        }

        return <<<HTML
{$own}删除了您在 <a class="hu60_pos" href="{$url}">{$pos}</a> 的发言{$reason}
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
            bbs::ACTION_ADD_BLOCK_POST => '已将您禁言',
            bbs::ACTION_REMOVE_BLOCK_POST => '将您解除禁言',
            bbs::ACTION_SET_ESSENCE_TOPIC => '加精',
            bbs::ACTION_UNSET_ESSENCE_TOPIC => '取消精华',
        ];

        $act = $actName[$data['act']];
        $url = code::html($data['url']);
        $pos = code::html($data['pos']);
        $reason = code::html($data['reason']);
        $uinfo = new UserInfo();
        $uinfo->uid($data['uid']);

	    if (in_array($data['act'], [bbs::ACTION_ADD_BLOCK_POST, bbs::ACTION_REMOVE_BLOCK_POST])) {
		    return <<<HTML
管理员 <a class="userinfo" href="user.info.{$uinfo->uid}.{$PAGE->bid}">{$uinfo->name}</a> {$act}，理由如下：
<blockquote>
{$reason}
</blockquote>
HTML;
	    }
	    else {
	        if ($data['uid'] == $data['ownUid']) {
        	    $own = "您";
	            $reason = "。";
	        } else {
        	    $own = "管理员 <a class=\"userinfo\" href=\"user.info.{$uinfo->uid}.{$PAGE->bid}\">{$uinfo->name}</a> ";

	            $reason = <<<HTML
，理由如下：
<blockquote>
{$reason}
</blockquote>
HTML;

        	}

	        return <<<HTML
{$own}将您的 <a class="hu60_pos" href="{$url}">{$pos}</a> {$act}{$reason}
HTML;
	    }
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
            $admin = '<a class="userat" href="#" onclick="' . $jsFunc . '(\'' . $name . '\',this);return false">@</a><a class="userinfo" href="user.info.' . code::html($data['uid']) . '.' . $PAGE->bid . '">' . code::html($name) . '</a>';
        } else {
            $admin = '<a class="userinfo" href="user.info.' . code::html($data['uid']) . '.' . $PAGE->bid . '">@' . code::html($name) . '</a>';
        }

        $time = '';

        if (isset($data['time'])) {
            $time = '于 ' . date('Y-m-d H:i ', $data['time']);
        }

        if ($data['uid'] == $data['ownUid']) {
            $own = '层主';
        } else {
            $own = '管理员';
        }

        if (!empty($reason)) {
            $reason = <<<HTML
，理由如下：
<p>{$reason}</p>
HTML;
        } else {
            $reason = '。';
        }

        return <<<HTML
<div class="tp info-box">
{$own} {$admin} {$time}删除了该楼层{$reason}
</div>
HTML;
    }

	/*待审核的内容*/
	public function postNeedReviewNotice($data) {
        global $PAGE;

		$reviewForm = '';
		if ($data['isAdmin']) {
			$reviewForm = <<<HTML
<form class="hu60_review" action="bbs.review.$data[contentId].$data[topicId].$PAGE[bid]" method="post">
	<label><input id="hu60_review_pass_$data[contentId]" type="radio" name="pass" value="1" />审核通过</label>
	<label><input id="hu60_review_nopass_$data[contentId]" type="radio" name="pass" value="0" checked />审核未通过</label>
    <input type="input" name="comment" placeholder="审核未通过理由" onclick="document.querySelector('#hu60_review_nopass_$data[contentId]').checked = true" />
	<input type="submit" value="确定" />
</form>
HTML;
		}

        $reviewLog = '';
        if (!empty($data['reviewLog'])) {
            $reviewLog = [];
            foreach ($data['reviewLog'] as $v) {
                $user = $this->at([ 'uid' => $v['uid'] ]);
                $action = bbs::getReviewActionName($v['stat']);
                $comment = code::html($v['comment'], false, true);
                $time = str::ago($v['time']);
                $reviewLog[] = <<<HTML
<div class="review_log_line">
    <span class="review_log_time">[$time]</span>
    <span class="review_log_user">$user</span>
    <span class="review_log_action">$action</span>:
    <div class="review_log_comment">$comment</div>
</div>
HTML;
            }
            $reviewLog = implode("\n", $reviewLog);
        }

        $stat = bbs::getReviewStatName($data['stat']);

        // 注意：不要产生连续8个空格，否则markdown解析器会把后续内容当作代码块显示！
        return <<<HTML
<div class="tp info-box">
	发言{$stat}，仅管理员和作者本人可见。
    <div class="review_log">$reviewLog</div>
	$reviewForm
</div>
HTML;
	}

    /*face 表情*/
    public function face($data)
    {
        global $PAGE;

        $path = 'img/face/' . bin2hex($data['face']) . '.gif';

        try {
            $url = $PAGE->getTplUrl($path).'?'.filemtime($path);

            if (JsonPage::isJsonPage()) {
                $url = SITE_URL_PREFIX . $url;
            }

            $html = '<img class="hu60_face" title="' . code::html($data['face']) . '" src="' . code::html($url) . '" />';
        } catch (Exception $e) {
            $html = code::html('{' . $data['face'] . '}');
        }

        return $html;
    }
}
