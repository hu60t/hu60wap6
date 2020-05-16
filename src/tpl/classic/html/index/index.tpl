{config_load file="conf:site.info"}
{include file="tpl:comm.head" title=#SITE_NAME# no_user=true}
<div class="tp">
	<p><img style="max-width:200px" src="{$PAGE->getTplUrl("img/hulvlin3.png")}"></p>
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
			已掉线，<a href="user.login.{$bid}?u={urlencode($page->geturl())}">重新登录</a>
		{/if}
	{else}
		<a href="user.login.{$bid}?u={urlencode($page->geturl())}" title="登录" style="margin-right:10px">登录</a>
		<a href="user.reg.{$bid}?u={urlencode($page->geturl())}" title="立即注册">立即注册</a>
	{/if}
	<p>[<a href="link.css.default.{$BID}?url64={code::b64e($page->geturl())}">白天</a> | <a href="link.css.night.{$BID}?url64={code::b64e($page->geturl())}">夜间</a> | <a href="addin.webplug.{$BID}">网页插件</a> | <a href="addin.jhtml.{$BID}">JHTML</a>]</p>
</div>
<hr>
<div id="my_heart"><!-- 由@肖申克(uid:21156)命名 -->
	<p>『用户专区』</p>
	<p>
		-我的：<a href="bbs.search.{$BID}?username={$USER->name|urlencode}">帖子</a>|<a href="bbs.search.{$BID}?username={$USER->name|urlencode}&searchType=reply">回复</a>|<a href="msg.index.{$bid}">内信</a>|<a href="msg.index.@.{$bid}">@消息</a>|<a href="bbs.myfavorite.{$BID}">收藏</a>
	</p>
</div>
<hr>
<div>
	<p>『<a href="bbs.forum.{$BID}">绿虎论坛</a> - <a href="bbs.forum.0.1.{$BID}">新帖</a>|<a href="bbs.forum.0.1.1.{$BID}">精华</a>|<a href="bbs.newtopic.0.{$BID}">发帖</a>』</p>
	<ol style="padding-left:2em">
		{foreach $newTopicList as $topic}
			<li>{if $topic.essence==1}<span style="color:red;">[精]</span>{/if}<a href="bbs.topic.{$topic.topic_id}.{$BID}">{$topic.title|code}</a></li>
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
<div class="books">
    <p class="bar">『小说阅读』</p>
	<p>-<a href="book.index.html">小说列表</a></p>
	<p>-<a href="https://www.unionread.vip/">联合阅读</a>：承诺不剥削作者，由网文作家月影梧桐创建（公测中）</p>
</div>
<hr>
<div id="friend_links">
	<p id="friend_links_title">『虎友网站展示』</p>
	{include file="tpl:comm.friend_links"}
</div>
{include file="tpl:comm.foot"}
