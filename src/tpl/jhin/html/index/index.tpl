{extends file='tpl:comm.default'}
{block name='title'}
	{#SITE_NAME#}
{/block}
{block name='body'}

<div class="widget">
	<div class="bar">
		最新帖子-<a href="bbs.forum.0.html">论坛</a>
	</div>
	<ul class="topic-ul">
		{foreach $newTopicList as $topic}
			<li>
				<a href="bbs.topic.{$topic.topic_id}.{$BID}" class="topic-title">{$topic.title|code}</a>
				<div class="topic-meta">
					(
					<span class="topic-author"><a href="user.info.{$topic.uinfo.uid}.{$BID}">{$topic.uinfo.name|code}</a></span>/
					{$topic.read_count}点击/
					{$topic.reply_count}回复/{date('Y-m-d H:i',$topic.time)}
					)
				</div>
			</li>
		{/foreach}
	</ul>
	<div class="widget-page">
		{if $hasNextPage}<a style="display:inline" href="?p={$topicPage + 1}">下一页</a>{/if}
		{if $topicPage > 1}<a style="display:inline" href="?p={$topicPage-1}">上一页</a>{/if}
	</div>
</div>
<div class="widget">
	<div class="bar">实用工具</div>
	<a href="tools.ua.html">查看浏览器UA</a>
	<a href="tools.coder.html">编码解码器</a>
</div>
<div id="friend_links">
	<div class="bar">友情链接</div>
	{include file="tpl:comm.friend_links"}
</div>
{/block}
