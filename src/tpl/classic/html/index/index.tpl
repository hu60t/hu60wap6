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
	<p>[<a href="link.css.default.{$BID}?url64={url::b64e($page->geturl())}">白天模式</a>|<a href="link.css.night.{$BID}?url64={url::b64e($page->geturl())}">夜间模式</a>]</p>
</div>
<hr>
<div>
	<p>『用户专区』</p>
	<p>
		-我的：帖子|回复|<a href="msg.index.{$bid}">内信</a>|<a href="msg.index.@.{$bid}">@消息</a>|收藏
	</p>
</div>
<hr>
<div>
	<p>『<a href="bbs.forum.{$BID}">绿虎论坛</a> - <a href="bbs.forum.0.1.{$BID}">新帖</a>|新回复|发帖|优秀源码』</p>
	<ol style="padding-left:2em">
		{foreach $newTopicList as $topic}
			<li><a href="bbs.topic.{$topic.topic_id}.{$BID}">{$topic.title|code}</a></li>
		{/foreach}
	</ol>
	<p>
		{if $topicPage > 1}<a style="display:inline" href="?p={$topicPage-1}">上一页</a>{/if}
		{if $hasNextPage}<a style="display:inline" href="?p={$topicPage + 1}">下一页</a>{/if}
	</p>
</div>
{include file="tpl:comm.foot"}
