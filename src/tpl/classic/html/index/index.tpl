{config_load file="conf:site.info"}
{include file="tpl:comm.head" title=#SITE_NAME# no_user=true}
<div class="tp">
	<p><img src="{$PAGE->getTplUrl("img/hulvlin2.gif")}"></p>
	<p>分享阳光，树木变成森林！</p>
	{if $user->uid}
		{if $user->islogin}
			{$MSG=msg::getInstance($USER)}
			<a href="user.index.{$bid}">{$user->name|code}</a>
			{$newMSG=$MSG->newMsg()}
			{$newATINFO=$MSG->newAtInfo()}
			{if $newMSG > 0}<a href="msg.index.inbox.no.{$bid}">{$newMSG}条新内信</a>{/if}
			{if $newATINFO > 0}<a href="msg.index.@.no.{$bid}">{$newATINFO}条新@消息</a>{/if}
			<a href="user.exit.{$bid}?u={urlencode($page->geturl())}">退出</a>
		{else}
			已掉线，<a href="user.login.{$bid}?u={urlencode($page->geturl())}">重新登陆</a>
		{/if}
	{else}
		<a href="user.login.{$bid}?u={urlencode($page->geturl())}" title="登录" style="margin-right:10px">登录</a>
		<a href="user.reg.{$bid}?u={urlencode($page->geturl())}" title="立即注册">立即注册</a>
	{/if}
	<p>[<a href="link.css.default.{$BID}?url64={code::b64e($page->geturl())}">白天</a> | <a href="link.css.night.{$BID}?url64={code::b64e($page->geturl())}">夜间</a> | <a href="addin.webplug.{$BID}">插件</a> | <a href="addin.jhtml.{$BID}">JHTML</a>]</p>
</div>
<hr>
<div class="news">
	<p>『<a href="addin.speedtest.{$BID}">网速测试（找到我的最佳访问线路）</a>』</p>
	<p>-机房搬迁，测试数据重置 ↑</p>
</div>
<hr>
<div>
	<p>『用户专区』</p>
	<p>
		-我的：<a href="bbs.search.{$BID}?username={$USER->name|urlencode}">帖子</a>|回复|<a href="msg.index.{$bid}">内信</a>|<a href="msg.index.@.{$bid}">@消息</a>|收藏
	</p>
</div>
<hr>
<div>
	<p>『<a href="bbs.forum.{$BID}">绿虎论坛</a> - <a href="bbs.forum.0.1.{$BID}">新帖</a>|新回复|<a href="bbs.newtopic.0.{$BID}">发帖</a>|优秀源码』</p>
	<ol style="padding-left:2em">
		{foreach $newTopicList as $topic}
			<li><a href="bbs.topic.{$topic.topic_id}.{$BID}">{$topic.title|code}</a></li>
		{/foreach}
	</ol>
	<p>
		{if $hasNextPage}<a style="display:inline" href="?p={$topicPage + 1}">下一页</a>{/if}
		{if $topicPage > 1}<a style="display:inline" href="?p={$topicPage-1}">上一页</a>{/if}
	</p>
</div>
<hr>
<div id="tools">
	<p>『实用工具』</p>
	<p>-<a href="tools.ua.{$BID}">查看HTTP请求</a></p>
	<p>-<a href="tools.coder.{$BID}">编码解码器</a></p>
</div>
<hr>
<div id="friend_links">
	<p>『友情链接』</p>
	<p><a href="http://chinalol.cc/">老司机LOL</a> | <a href="http://2cbk.com/">二超博客</a> | <a href="http://www.mhcf.net/">梦幻辰风</a> | <a href="http://blog.isoyu.com/">长信博客</a> | <a href="https://morz.org/">喵萌博客</a></p>
	<p><a href="http://lehuidc.cn/">乐虎IDC</a></p>
	<p>虎绿林使用<a href="http://www.vultr.com/?ref=6945913-3B">Vultr</a>的服务器，由<a href="user.info.13716.html">OrzLAN</a>提供网络加速。
</div>
{include file="tpl:comm.foot"}
