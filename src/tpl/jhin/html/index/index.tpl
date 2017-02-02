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
					{$topic.reply_count}回复/{str::ago($topic.mtime)}
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
	<p>
		<a href="http://chinalol.cc/">老司机LOL</a> |
		<a href="http://2cbk.com/">二超博客</a> |
		<a href="http://www.mhcf.net/">梦幻辰风</a> | <a href="http://blog.isoyu.com/">长信博客</a> | <a href="https://morz.org/">喵萌博客</a></p>
	<p>
		<a href="http://lehuidc.cn/">乐虎IDC</a></p>
	<p>虎绿林使用<a href="http://www.vultr.com/?ref=6945913-3B">Vultr</a>的服务器，由<a href="user.info.13716.html">OrzLAN</a>提供网络加速。
</div>
{/block}
