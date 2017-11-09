{extends file='tpl:comm.default'}
{block name='title'}
	{#SITE_NAME#}
{/block}
{block name='body'}

<div class="widget">
	<ul class="topic-ul">
		{foreach $newTopicList as $topic}
			<li>
				<a href="bbs.topic.{$topic.topic_id}.{$BID}" class="topic-title">{$topic.title|code}</a>
				<div class="topic-meta">
					(
					<span class="topic-author"><a href="user.info.{$topic.uinfo.uid}.{$BID}">{$topic.uinfo.name|code}</a></span>/
					{$topic.read_count}点击/
					{str::ago($topic.mtime)}
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
    <div class="widget">
        {jhinfunc::randTopic($user)}
        <ul class="forum-list">
            {foreach jhinfunc::forum() as $forum}
                <li>
                    <a href="bbs.forum.{$forum.id}.{$BID}" >{$forum.name|code}</a>
                    {if $forum.child}
                        <ul class="forum-list-child">
                            {foreach $forum.child as $child}
                                <li><a href="bbs.forum.{$child.id}.{$BID}" >{$child.name|code}</a></li>
                            {/foreach}
                        </ul>
                    {/if}
                </li>
            {/foreach}
        </ul>

    </div>
{/block}
