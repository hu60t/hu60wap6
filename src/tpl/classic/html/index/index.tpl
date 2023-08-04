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
	<p>[<a href="#hu60_footer_action">夜间模式</a> | <a href="addin.webplug.{$BID}">网页插件</a> | <a href="addin.jhtml.{$BID}">JHTML</a>]</p>
</div>
<hr>
<div id="my_heart"><!-- 由@肖申克(uid:21156)命名 -->
	<p>『用户专区』</p>
	<p>
		-我的：
			<a href="bbs.search.{$BID}?username={$USER->name|urlenc}">帖子</a> |
			<a href="bbs.search.{$BID}?username={$USER->name|urlenc}&searchType=reply">回复</a> |
			<a href="msg.index.{$bid}">内信</a> |
			<a href="msg.index.@.{$bid}">@消息</a> |
			<a href="bbs.myfavorite.{$BID}">收藏</a>
	</p>
</div>
<hr>
<div>
	<p>
		『<a href="bbs.forum.{$BID}">绿虎论坛</a> -
			<a href="bbs.forum.0.1.{$BID}">新帖</a> |
			<a href="bbs.forum.0.1.1.{$BID}">精华</a> |
			<a href="bbs.search.{$BID}">搜索</a> |
			<a href="bbs.newtopic.0.{$BID}">发帖</a>
			{if $countReview}| <a href="bbs.search.{$BID}?{if $smarty.get.showBot == 1}showBot=1&amp;{/if}onlyReview=1">{$countReview}待审核</a>{/if}
			{if $chatCountReview}| <a href="addin.chat.@.{$BID}{if $smarty.get.showBot == 1}?showBot=1{/if}">{$chatCountReview}聊天待审核</a>{/if}
			{if $USER->hasPermission(userinfo::PERMISSION_REVIEW_POST)}
				{if $smarty.get.showBot == 1}
					| <a href="?showBot=0">隐藏机器人待审</a>
				{else}
					| <a href="?showBot=1">显示机器人待审</a>
				{/if}
			{/if}
		』
	</p>
	<ol style="padding-left:2em">
		{foreach $newTopicList as $topic}
			<li>
				{if $topic.essence}<span style="color:red;">[精]</span>{/if}
				<a class="user-title" href="bbs.topic.{$topic.topic_id}.{$BID}">{$topic.title|code}</a>
				{if $topic.review}
					<div class="topic-status">{bbs::getReviewStatName($topic.review)}</div>
				{/if}
				{if $topic.uinfo->hasPermission(UserInfo::DEBUFF_BLOCK_POST)}
					<div class="topic-status">被禁言</div>
				{/if}
				{if $topic.locked == 2}
					<div class="topic-status">评论关闭</div>
				{elseif $topic.locked}
					<div class="topic-status">被锁定</div>
				{/if}
                {if $topic.level < 0}
                    <div class="topic-status">被下沉</div>
                {/if}
				{if $USER->canAccess(1) && $topic.access == 0}
					<div class="topic-status">公开</div>
				{/if}
			</li>
		{/foreach}
	</ol>
	<p>
		{if $hasNextPage}<a style="display:inline" href="?p={$topicPage + 1}">下一页</a>{/if}
		{if $topicPage > 1}<a style="display:inline" href="?p={$topicPage-1}">上一页</a>{/if}
	</p>
</div>
<hr>
<div id="tools">
	<p>『Linux游戏』</p>
	<p>-<a href="https://winegame.net/">Wine游戏助手</a></p>
	<p>-<a href="https://winegame.net/games">游戏列表</a></p>
	<p>-<a href="https://winegame.net/games?genres=26">软件列表</a></p>
	<p>-<a href="bbs.forum.170.{$BID}">论坛板块</a></p>
	<p>-<a href="bbs.topic.95988.{$BID}">QQ群/微信群</a></p>
</div>
<hr>
<div id="tools">
	<p>『实用工具』</p>
	<p>-<a href="addin.webplug.{$BID}">网页插件</a></p>
	<p>-<a href="tools.ua.{$BID}">查看HTTP请求</a></p>
	<p>-<a href="tools.coder.{$BID}">编码解码器</a></p>
</div>
{if $USER->unlimit()}
<hr>
<div id="friend_links">
	<p id="friend_links_title">『虎友网站展示』</p>
	{include file="tpl:comm.friend_links"}
</div>
{/if}
{include file="tpl:comm.foot"}
