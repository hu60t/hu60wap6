<?php

class UbbParser extends XUBBP
{
	// 是否处于markdown模式
	protected $markdownEnable = false;
	
    protected $parse = array(
        /*
        * 一次性匹配标记
        *
        * 如果标记可以一次性匹配，
        * 不需要分为开始标记和结束标记分别匹配，
        * 则在这一段定义（加在这一段末尾）。
        *
        * 注意：不要定义在code规则的前面，
        * 因为[code][/code]标记里的内容（代码块）不应该进行任何UBB解析。
        * 按照顺序解析，顺序非常重要，排在后面的匹配可能会被忽略。
        */

        /*code 代码高亮（标记独占一行，高优先级）*/
        '!^(^|.*?\n+)\[code(?:=([^\]]*))?\](\n+.*?\n+)\[/code\](\n+.*|$)$!is' => array(array(1, 4), 'code', array(2, 3)),
        /* html 通过iframe的srcdoc属性实现的HTML内容嵌入（标记独占一行，高优先级）*/
        '!^(^|.*?\n+)\[html(=[^\]]*)?\](\n+.*?\n+)\[/html\](\n+.*|$)$!is' => array(array(1, 4), 'html', array(2, 3)),
        /* textbox 文本框（标记独占一行，高优先级）*/
        '!^(^|.*?\n+)\[text(?:=([^\]]*))?\](\n+.*?\n+)\[/text\](\n+.*|$)$!is' => array(array(1, 4), 'textbox', array(2, 3)),

        /*code 代码高亮*/
        '!^(.*?)\[code(?:=([^\]]*))?\](.*?)\[/code\](.*)$!is' => array(array(1, 4), 'code', array(2, 3)),
        /* html 通过iframe的srcdoc属性实现的HTML内容嵌入 */
        '!^(.*?)\[html(=[^\]]*)?\](.*?)\[/html\](.*)$!is' => array(array(1, 4), 'html', array(2, 3)),
        /* textbox 文本框 */
        '!^(.*?)\[text(?:=([^\]]*))?\](.*?)\[/text\](.*)$!is' => array(array(1, 4), 'textbox', array(2, 3)),

        /* iframe 网页嵌入 */
        '!^(.*?)<iframe((?:\s+[a-zA-Z0-9_-]+(?:=(?:\'[^\']*\'|"[^"]*"|[^\s]*))?)*)>.*?</iframe>(.*)$!is' => array(array(1, 3), 'iframe', array(2)),

        /*time 时间*/
        '!^(.*?)\[time(?:=(.*?))?\](.*)$!is' => array(array(1, 3), 'time', array(2)),
        /*link 链接*/
        '!^(.*?)\[url(?:=(.*?))?\](.*?)\[/url\](.*)$!is' => array(array(1, 4), 'link', array('url', 2, 3)),
        '!^(.*?)《(链接|外链|锚)[：:∶](.*?)》(.*)$!uis' => array(array(1, 4), 'link', array(2, 3)),
        /*img 图片*/
        '!^(.*?)\[img(?:=(.*?))?\](.*?)\[/img\](.*)$!is' => array(array(1, 4), 'img', array('img', 2, 3)),
        '!^(.*?)《(图片|缩略图)[：:∶](.*?)》(.*)$!uis' => array(array(1, 4), 'img', array(2, 3)),
        '!^(.*?)《表情[：:∶](.*?)》(.*)$!uis' => array(array(1, 3), 'face', array(2)),
        /*video 视频*/
        '!^(.*?)《视频[：:∶](.*?)》(.*)$!uis' => array(array(1, 3), 'video', array(2)),
        /*videoStream 视频*/
        '!^(.*?)《视频流[：:∶](.*?)》(.*)$!uis' => array(array(1, 3), 'videoStream', array(2)),
        /*audio 音频*/
        '!^(.*?)《音频[：:∶](.*?)》(.*)$!uis' => array(array(1, 3), 'audio', array(2)),
        /*audioStream 视频*/
        '!^(.*?)《音频流[：:∶](.*?)》(.*)$!uis' => array(array(1, 3), 'audioStream', array(2)),
        /*copyright 版权*/
        '!^(.*?)《版权[：:∶](.*?)》(.*)$!uis' => array(array(1, 3), 'copyright', array(2)),
        /*battlenet 战网*/
        '!^(.*?)《战网[：:∶](.*?)》(.*)$!uis' => array(array(1, 3), 'battlenet', array(2)),
        /*math 数学公式*/
        '!^(.*?)\[math\](.*?)\[/math\](.*)$!is' => array(array(1, 3), 'math', array('math', 2)),
        '!^(.*?)《公式[：:∶](.*?)》(.*)$!uis' => array(array(1, 3), 'math', array('mathzh', 2)),
        /*tab 四个空格*/
        '!^(.*?)\[tab\](.*)$!is' => array(array(1, 2), 'tab', array(2)),
        /*empty UBB转义*/
        '!^(.*?)\[empty\](.*)$!is' => array(array(1, 2), 'emptyTag', array(2)),
        /*newline 换行*/
        //'!^(.*?)(\n)(.*)$!is' => array(array(1,3), 'newline', array(2)),
        '!^(.*?)\[([bh]r)\](.*)$!is' => array(array(1, 3), 'newline', array(2)),
        //'!^(.*?)(///|＜＜＜|＞＞＞)(.*)$!is' => array(array(1, 3), 'newline', array(2)),

        /*
        * 开始标记
        *
        * 这一段应该只包括开始标记，
        * 结束标记不应定义在这一段，
        * 否则会出现代码嵌套错误。
        */
        /*layoutStart 布局开始*/
        '!^(.*?)\[(b|i|u|center|left|right)\](.*)$!is' => array(array(1, 3), 'layoutStart', array(2)),
        /*style 样式开始*/
        '!^(.*?)\[(color|div|span)=(.*?)\](.*)$!is' => array(array(1, 4), 'styleStart', array(2, 3)),
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
        '!^(.*?)\[/(color|div|span)\](.*)$!is' => array(array(1, 3), 'styleEnd', array(2)),
        /*layout 布局结束*/
        '!^(.*?)\[/(b|i|u|center|left|right)\](.*)$!is' => array(array(1, 3), 'layoutEnd', array(2)),

        /*
        * 易误匹配的标记
        *
        * 这里的标记最后匹配，为了防止误匹配。
        * 可能会影响其他标记正常匹配的标记放在这里。
        */
        /*urltxt 文本链接*/
        '!^(.*?)((?:https?|ftps?|rtsp)\://[a-zA-Z0-9\.\,\?\!\(\)\[\]\@\/\:\_\;\+\&\%\*\=\~\^\#\-]+)(.*)$!is' => array(array(1, 3), 'urltxt', array(2)),
        #'#^(.*?)((?<!@)[a-zA-Z0-9._-]{1,255}\.(?:asia|mobi|name|com|net|org|xxx|cc|cn|hk|me|tk|tv|uk)(?:/[a-zA-Z0-9\.\,\?\!\(\)\[\]\@\/\:\_\;\+\&\%\*\=\~\^\#\-]+)?)(.*)$#is' => array(array(1,3), 'urltxt', array(2)),
        /*mailtxt 文本电子邮件地址*/
        '!^(.*?)((?:mailto:)?[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z]{2,4})(.*)$!is' => array(array(1, 3), 'mailtxt', array(2)),
        /*at @消息*/
        '!^(.*?)[@＠]([@＠#＃a-zA-Z0-9\p{Han}_-]+)(.*)$!uis' => array(array(1, 3), 'at', array(2)),
        /*face 表情*/
        '!^(.*?)\{(ok|\p{Han}{1,3})\}(.*)$!uis' => array(array(1, 3), 'face', array(2)),
        '!^(.*?)《[：:∶](ok|\p{Han}{1,3})》(.*)$!uis' => array(array(1, 3), 'face', array(2)),
    );

	/**
	* 前置过滤器，替换换行符和特殊空格，删除sid等敏感内容
	*/
	public function filter($text) {
        // \r\n 和 \r 替换为 \n
        $text = str_replace(["\r\n", "\r"], "\n", $text);
		//把utf-8中的特殊空格转换为普通空格，防止粘贴的代码发生莫名其妙的问题
        $text = str::nbsp2space($text);
        // 去除网址中的sid
		$text = preg_replace('#/q.php/[a-zA-Z0-9_-]+/#', '/q.php/', $text);
		return $text;
	}

	public function parse($text, $serialize = false) {
        global $USER, $USER_WORD_BLOCKLIST;
        
        // 敏感词检查
        if ($USER && $USER->uid && is_array($USER_WORD_BLOCKLIST)) {
            foreach ($USER_WORD_BLOCKLIST as $v) {
                if (empty($v['users']) || in_array($USER->uid, $v['users'])) {
                    if (preg_match($v['preg_match'], $text, $arr)) {
                        throw new Exception('“'.$arr[0].'”是敏感词', 400);
                    }
                }
            }
        }

		$text = $this->filter($text);

		$markdownTag = NULL;
		
		// markdown模式检测
		if (preg_match('#^<!--\s*markdown\s*-->#is', $text) || preg_match('#^<!md>#is', $text)) {
			$text = preg_replace(['#^<!--\s*markdown\s*-->#is', '#^<!md>#is'], '', $text);
			$markdownTag = $this->markdown();
		}
		
		$result = parent::parse($text, false);
		
		if (NULL !== $markdownTag) {
			$result = array_merge($markdownTag, $result);
		}
		
		if ($serialize) {
			$result = data::serialize($result);
		}
		
		return $result;
	}

    public function markdown(){
		$this->markdownEnable = true;
		
		// 保护markdown内容不受XUBBP解析器干扰
		
		/*urltxt 文本链接*/
		$this->parse['!^(.*?)((?:https?|ftps?|rtsp)\://[a-zA-Z0-9\.\,\?\!\(\)\[\]\@\/\:\_\;\+\&\%\*\=\~\^\#\-]+)(.*)$!is'] = array(array(1, 3), 'mdlink', array(2));
		
		/*mailtxt 文本电子邮件地址*/
		$this->parse['!^(.*?)((?:mailto:)?[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z]{2,4})(.*)$!is'] = array(array(1, 3), 'mdpre', array(2));
		
		// 添加新的匹配规则
		$parseHead = [
            /*mdcode markdown代码高亮*/
            '!^(^|.*?\n)( *)(`{3,})( *[^`\n]+?)?( *\n.*?\n *)\3( *\n.*| *$)$!is' => array(array(1, 6), 'mdcode', array(4, 5, 3, 2)),

            /*code 代码高亮（标记独占一行，高优先级）*/
            '!^(^|.*?\n+)\[code(?:=([^\]]*))?\](\n+.*?\n+)\[/code\](\n+.*|$)$!is' => array(array(1, 4), 'code', array(2, 3)),
            /* html 通过iframe的srcdoc属性实现的HTML内容嵌入（标记独占一行，高优先级）*/
            '!^(^|.*?\n+)\[html(=[^\]]*)?\](\n+.*?\n+)\[/html\](\n+.*|$)$!is' => array(array(1, 4), 'html', array(2, 3)),
            /* textbox 文本框（标记独占一行，高优先级）*/
            '!^(^|.*?\n+)\[text(?:=([^\]]*))?\](\n+.*?\n+)\[/text\](\n+.*|$)$!is' => array(array(1, 4), 'textbox', array(2, 3)),

            /* 4个空格或一个tab开头的markdown代码块 */
            // 注意如果代码块前面有任何内容，就必须至少有一个空行隔开，否则Parsedown不认为它是代码块
            '!^(^(?:\s*\n)*|.*?(?:\s*\n){2,})((?:\t|    )[^\n]*(?:\n+(?:\t|    )[^\n]*)*)(\n+.*|$)$!is' => array(array(1, 3), 'mdpre', array(2)),

            /*inline代码（优先级比上面的低）*/
            '!^(.*?)((`+).+?\3)(.*)$!is' => array(array(1, 4), 'mdpre', array(2)),
        ];

		$this->parse = $parseHead + $this->parse;
		
		return array(array(
          'type' => 'markdown',
		));
    }

    static public function parseWebPlug($lang, &$data) {
        if (!preg_match('/^(?:webplug|网页插件)(?:（统计ID勿删#([a-z0-9]{16})）)?(?:[:：](.*))?$/uis', $lang, $info)) {
            return false;
        }

        // id
        if (empty($info[1])) {
            $info[1] = str::webplugId();
        }

        $data['lang'] = 'html';
        $data['webplug'] = [
            'id' => $info[1],
            'name' => $info[2],
        ];
        return true;
    }

    /**
     * @brief 代码高亮
     */
    public function code($lang, $data)
    {
        $lang = trim($lang);
        $result = [
            'type' => 'code',
            'lang' => strtolower($lang),
            'data' => $data,
        ];

        self::parseWebPlug($lang, $result);

        return [ $result ];
    }

	/**
     * @brief markdown代码高亮
     */
    public function mdcode($lang, $data, $quote, $indent)
    {
        $lang = trim($lang);
		$result = [
            'type' => 'mdcode',
			'lang' => strtolower($lang),
            'data' => $data,
            'quote' => $quote,
        ];

        if (!empty($indent)) {
            $result['indent'] = $indent;
        }

        self::parseWebPlug($lang, $result);

        return [ $result ];
    }

	/**
     * @brief markdown受保护内容（不被XUBBP解析器干扰）
     */
    public function mdpre($data)
    {
        return [[
            'type' => 'mdpre',
            'data' => $data,
        ]];
    }

    /**
     * @brief markdown受保护链接（不被XUBBP解析器干扰）
     */
    public function mdlink($data)
    {
        return [[
            'type' => 'mdpre',
            'data' => url::decodeUrl64InLink($data),
        ]];
    }

    /**
     * @brief 时间标记
     */
    public function time($tag)
    {
        return array(array(
            'type' => 'time',
            'tag' => $tag,
        ));
    }

    /** @brief 链接 */
    public function link($type, $var, $var2 = '')
    {
        if ($type == '链接' || $type == '外链' || $type == '锚') {
            $var = $this->split('，', $var);
            $url = $var[0];
            $title = $var[1];

            $type = $type == '链接' ? 'urlzh' : ($type == '外链' ? 'urlout' : 'urlname');
        } else {
            $type = 'url';
            if ($var == '') {
                $url = $var2;
                $title = '';
            } else {
                $url = $var;
                $title = $var2;
            }
        }

        if ($title != null &&
            (preg_match('!\[(b|i|u|center|left|right|color|div|span|img)=?!is', $title) ||
            strpos($title, '《图片') !== false ||
            strpos($title, '《缩略图') !== false)) {
            $obj = new ubbParser;
            $obj->setParse(array(
                /*layoutStart 布局开始*/
                '!^(.*?)\[(b|i|u|center|left|right)\](.*)$!is' => array(array(1, 3), 'layoutStart', array(2)),
                /*style 样式开始*/
                '!^(.*?)\[(color|div|span)=(.*?)\](.*)$!is' => array(array(1, 4), 'styleStart', array(2, 3)),

                '!^(.*?)\[img(?:=(.*?))?\](.*?)\[/img\](.*)$!is' => array(array(1, 4), 'img', array('img_in_link', 2, 3)),
                '!^(.*?)《(图片|缩略图)[：:∶](.*?)》(.*)$!uis' => array(array(1, 4), 'img_in_link', array(2, 3)),

                /*style 样式结束*/
                '!^(.*?)\[/(color|div|span)\](.*)$!is' => array(array(1, 3), 'styleEnd', array(2)),
                /*layout 布局结束*/
                '!^(.*?)\[/(b|i|u|center|left|right)\](.*)$!is' => array(array(1, 3), 'layoutEnd', array(2)),
            ));
            $title = $obj->parse($title);
        }

        return array(array(
            'type' => $type,
            'url' => url::decodeUrl64InLink($url),
            'title' => $title,
        ));
    }

    /** @brief 链接中的图片 */
    public function img_in_link($type, $var, $var2 = '') {
        $result = $this->img($type, $var, $var2);
        $result[0]['in_link'] = true;
        return $result;
    }

    /** @brief 图片 */
    public function img($type, $var, $var2 = '')
    {
        if ($type == '缩略图') {
            $var = $this->split('，', $var);

	    if (count($var) == 1) {
	        array_unshift($var, '320'); //默认宽度
	    }

            $opt = $var[0];
            $url = $var[1];
            preg_match_all('![0-9]+!', $opt, $opt);
            return array(array(
                'type' => 'thumb',
                'src' => trim($url),
                'w' => $opt[0][0],
                'h' => $opt[0][1],
            ));
        } else {
            if ($type == '图片') {
                $var = $this->split('，', $var);
                $src = $var[0];
                $alt = $var[1];
            } elseif ($var == '') {
                $src = $var2;
                $alt = '';
            } else {
                $src = $var;
                $alt = $var2;
            }
            return array(array(
                'type' => $type == 'img' ? 'img' : 'imgzh',
                'src' => url::decodeUrl64InLink($src),
                'alt' => $alt,
            ));
        }
    }

    /**
     * @brief 视频
     */
    public function video($url)
    {
        // 哔哩哔哩app的分享链接，不能直接播放，需要转换为带BV号的视频链接
        if (preg_match('#^https?://b23.tv/[a-zA-Z0-9]+#is', $url)) {
            $headers = get_headers($url, 1, stream_context_create(['http' => ['timeout' => 1]]));
            if (is_array($headers) && is_array($headers['Location'])) {
                foreach ($headers['Location'] as $url) {
                    if (preg_match('#^https?://#i', $url)) {
                        $newUrl = $url;
                        break;
                    }
                }
            } else {
                $newUrl = $headers['Location'];
            }
            if (preg_match('#\b(?:bilibili\.com|b23\.tv)\b.*\b(BV[\w]+)(?:.*\bp=(\d+))?#', $newUrl)) {
                $url = $newUrl;
            }
        }

        return array(array(
            'type' => 'video',
            'url' => trim($url),
        ));
    }

    /**
     * @brief 音频
     */
    public function audio($url)
    {
        return array(array(
            'type' => 'audio',
            'url' => trim($url),
        ));
    }

    /**
     * @brief 视频流
     */
    public function videoStream($url)
    {
        $url = $this->split('，', $url);
        return array(array(
            'type' => 'videoStream',
            'url' => trim($url[0]),
            'title' => $url[1],
        ));
    }

    /**
     * @brief 音频流
     */
    public function audioStream($url)
    {
        $url = $this->split('，', $url);
        return array(array(
            'type' => 'audioStream',
            'url' => trim($url[0]),
            'title' => $url[1],
        ));
    }

    /**
     * @brief 版权声明标记
     */
    public function copyright($tag)
    {
        return array(array(
            'type' => 'copyright',
            'tag' => trim($tag),
        ));
    }

    /**
     * @brief 战网（魔兽世界英雄榜）链接标记
     */
    public function battlenet($tag)
    {
        $info = $this->split('@', str_replace('＠', '@', $tag));
        $name = $this->split('，', $info[1]);
        return array(array(
            'type' => 'battlenet',
            'name' => trim($info[0]),
            'server' => trim($name[0]),
            'display' => trim($name[1]),
        ));
    }

    /**
     * @brief 数学公式
     */
    public function math($type, $data)
    {
        return array(array(
            'type' => $type,
            'data' => $data,
        ));
    }

    /**
     * @brief 换行
     */
    public function newline($tag)
    {
        return array(array(
            'type' => 'newline',
            'tag' => $tag,
        ));
    }

    /** @brief tab 四个空格 */
    public function tab($tag)
    {
        return [[
            'type' => 'tab',
        ]];
    }

    /** @brief empty UBB转义 */
    public function emptyTag($tag)
    {
        return [[
            'type' => 'empty',
        ]];
    }


    /**
     * @brief 布局开始
     */
    function layoutStart($tag)
    {
        return array(array(
            'type' => 'layout',
            'tag' => strtolower($tag),
        ));
    }

    /**
     * @brief 布局结束
     */
    function layoutEnd($tag)
    {
        /*结束标记的tag以斜杠(/)开始*/
        return array(array(
            'type' => 'layout',
            'tag' => '/' . $tag,
        ));
    }

    /**
     * @brief 样式开始
     */
    function styleStart($tag, $opt)
    {
        return array(array(
            'type' => 'style',
            'tag' => strtolower($tag),
            'opt' => $opt,
        ));
    }

    /**
     * @brief 样式结束
     */
    function styleEnd($tag)
    {
        /*结束标记的tag以斜杠(/)开始*/
        return array(array(
            'type' => 'style',
            'tag' => '/' . $tag,
        ));
    }

    /**
     * @brief urltxt 网址文本
     */
    function urltxt($url)
    {
        return array(array(
            'type' => 'urltxt',
            'url' => url::decodeUrl64InLink($url),
        ));
    }

    /**
     * @brief mailtxt 电子邮箱文本
     */
    function mailtxt($mail)
    {
        return array(array(
            'type' => 'mailtxt',
            'mail' => trim($mail),
        ));
    }

    /**
     * @brief at消息
     */
    function at($tag)
    {
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

        // @机器人，机器人聊天请求
        if (UserInfo::uidIsBot($uid)) {
            $this->setOpt('flags.TYPE_BOT_REQUEST', true);
        }

        return array(array(
            'type' => 'at',
            'tag' => trim($tag),
            'uid' => $uid,
        ));
    }

    /**
     * @brief iframe 网页嵌入
     */
    function iframe($str)
    {
        $allowKeys = [
            'allowfullscreen',
            'height',
            'importance',
            'name',
            'referrerpolicy',
            'src',
            'srcdoc',
            'width',
            'align',
            'frameborder',
            'framespacing',
            'longdesc',
            'marginheight',
            'marginwidth',
            'scrolling',
            'style',
            'seamless',
            'border',
        ];
        $data = [];
        preg_match_all('/([a-zA-Z0-9_-]+)(?:=(?:\'([^\']*)\'|"([^"]*)"|([^\s]*)))?/s', $str, $arr, PREG_SET_ORDER);

        foreach ($arr as $v) {
            $k = strtolower($v[1]);
            if (in_array($k, $allowKeys)) {
                $v = html_entity_decode($v[2].$v[3].$v[4]);
                $data[$k] = $v;
            }
        }

        return array(array(
            'type' => 'iframe',
            'data' => $data,
        ));
    }

    /**
     * @brief 通过iframe的srcdoc属性实现的HTML内容嵌入
     */
    public function html($opt, $data)
    {
        return array(array(
            'type' => 'html',
            'opt' => $opt,
            'data' => $data,
        ));
    }

    /**
     * @brief textbox 文本框
     */
    public function textbox($style, $data)
    {
        return array(array(
            'type' => 'textbox',
            'style' => $style,
            'data' => $data,
        ));
    }

    /**
     * @brief 表情
     */
    function face($face)
    {
        return array(array(
            'type' => 'face',
            'face' => trim($face),
        ));
    }

    /**
     * 生成at消息的XUBBP数据
     */
    public static function createAtMsg($user, $pos, $url, $msg, $serialize = false)
    {
        $data = array(array(
            'type' => 'atMsg',
            'uid' => $user->uid,
            'pos' => $pos,
            'url' => $url,
            'msg' => $msg,
        ));

        if ($serialize) {
            $data = data::serialize($data);
        }

        return $data;
    }

    /**
     * 生成管理员编辑通知的XUBBP数据
     *
     * @param User $user 操作者
     * @param string $pos 编辑对象的名称
     * @param string $url 操作对象的路径
     * @param string $reason 操作原因
     * @param string $oriData 编辑对象的原始内容（serialize格式的XUBBP数据）
     * @param bool $serialize 是否返回串行化结果
     *
     * @return XUBBP 数据
     */
    public static function createAdminEditNotice($user, $pos, $url, $reason, $oriData, $serialize = false)
    {
        $data = array(array(
            'type' => 'adminEdit',
            'uid' => $user->uid,
            'pos' => $pos,
            'url' => $url,
            'reason' => $reason,
            'oriData' => data::unserialize($oriData),
        ));

        if ($serialize) {
            $data = data::serialize($data);
        }

        return $data;
    }

    /**
     * 生成管理员删除通知的XUBBP数据
     *
     * @param User $user 操作者
     * @param string $pos 删除对象的名称
     * @param string $url 删除对象的路径
     * @param string $reason 操作原因
     * @param string $oriData 删除对象的原始内容（serialize格式的XUBBP数据）
     * @param bool $serialize 是否返回串行化结果
     * @param int $ownUid 楼层所有者的uid
     *
     * @return XUBBP 数据
     */
    public static function createAdminDelNotice($user, $pos, $url, $reason, $oriData, $serialize = false, $ownUid = null, $topicUid = null)
    {
        $data = array(array(
            'type' => 'adminDel',
            'uid' => $user->uid,
            'ownUid' => $ownUid,
            'topicUid' => $topicUid,
            'pos' => $pos,
            'url' => $url,
            'reason' => $reason,
            'oriData' => data::unserialize($oriData),
        ));

        if ($serialize) {
            $data = data::serialize($data);
        }

        return $data;
    }

    /**
     * 生成管理员删除内容的XUBBP数据
     *
     * @param User $user 操作者
     * @param string $reason 操作原因
     * @param bool $serialize 是否返回串行化结果
     * @param int $ownUid 楼层所有者的uid
     *
     * @return XUBBP 数据
     */
    public static function createAdminDelContent($user, $reason, $serialize = false, $ownUid = null, $noTime = false, $topicUid = null)
    {
        $data = array(array(
            'type' => 'delContent',
            'uid' => $user->uid,
            'ownUid' => $ownUid,
            'reason' => $reason,
            'time' => $noTime ? null : $_SERVER['REQUEST_TIME'],
        ));
        if (!empty($topicUid)) {
            $data[0]['topicUid'] = $topicUid;
        }

        if ($serialize) {
            $data = data::serialize($data);
        }

        return $data;
    }

    /**
     * 生成管理员操作提醒的XUBBP数据
     *
     * @param User $user 操作者
     * @param string $reason 操作原因
     * @param bool $serialize 是否返回串行化结果
     * @param int $ownUid 楼层所有者的uid
     *
     * @return XUBBP 数据
     */
    public static function createAdminActionNotice($action, $admin, $title, $url, $reason, $ownUid = null, $serialize = false)
    {
        $data = array(array(
            'type' => 'adminAction',
            'act' => $action,
            'uid' => $admin->uid,
            'ownUid' => $ownUid,
            'pos' => $title,
            'url' => $url,
            'reason' => $reason,
        ));

        if ($serialize) {
            $data = data::serialize($data);
        }

        return $data;
    }

	/**
	* 生成“用户发言需要审核”提醒的XUBBP数据
	* 
	* @param $accessUser 访问帖子的用户
	* @param $authorUinfo 帖子作者
	* @param $content 帖子内容的XUBBP数据
	* @param $serialize 是否串行化
	*/
	public static function createPostNeedReviewNotice($accessUser, $authorUinfo, $contentId, $content, $topicId, $stat, $reviewLog, $serialize = false) {
		$data = array(array(
			'type' => 'postNeedReview',
			'contentId' => $contentId,
			'topicId' => $topicId,
			'isAdmin' => is_object($accessUser) && $accessUser->islogin && $accessUser->hasPermission(userinfo::PERMISSION_REVIEW_POST),
            'stat' => $stat,
            'reviewLog' => $reviewLog ? $reviewLog : [],
		));

        if ($serialize) {
            $content = data::unserialize($content);
        }

        /**
         * 如果用户@了我，我就获得查看内容的权限
         */
        $showContent = false;
        foreach ($content as $vv) {
            // 如果存在类型为at，且@的uid为我自己的记录，那么这个消息应该展示
            if ($vv['type'] == 'at' && $vv['uid'] == $accessUser['uid']) {
                $showContent = true;
                break;
            }
        }

		if (is_object($accessUser) && is_object($authorUinfo) && $accessUser->islogin && ($accessUser->uid == $authorUinfo->uid || $showContent || $accessUser->hasPermission(userinfo::PERMISSION_REVIEW_POST))) {
			$data = array_merge($data, $content);
		}

		if ($serialize) {
		    $data = data::serialize($data);
		}

		return $data;
	}

    /*按指定分隔符将字符串分成两半*/
    protected static function split($split, $str)
    {
        $pos = strpos($str, $split);
        $data = [];

        if ($pos === false) {
            $data[] = $str;
        } else {
            $data[] = substr($str, 0, $pos);
            $data[] = substr($str, $pos + strlen($split));
        }

        return $data;
    }

    /*class end*/
}
