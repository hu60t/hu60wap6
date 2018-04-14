{extends file='tpl:comm.default'}
{block name='title'}
	{#SITE_NAME#}
{/block}
{block name='body'}

<div class="widget">
	<ul class="topic-ul">
		{foreach jhinfunc::IndexTopic() as $topic}
			<li>
                <div class="topic-anchor">
                    <a href="user.info.{$topic.uinfo.uid}.{$BID}">
                        <img src="{$topic.uinfo->avatar()}" class="avatar">
                    </a>
                    <a href="user.info.{$topic.uinfo.uid}.{$BID}">{$topic.uinfo.name|code}</a>
                </div>
                <div class="topic-title">
                    <a href="bbs.topic.{$topic.topic_id}.{$BID}">{$topic.title|code}</a>
                    <div class="topic-meta">
                        {$topic.read_count}点击 / {str::ago($topic.mtime)}
                    </div>
                </div>
                <div class="topic-reply-count">
                    <a href="bbs.topic.{$topic.topic_id}.{$BID}">{$topic.reply_count}</a>
                </div>
                <div class="topic-forum-name">
                    <a href="bbs.forum.{$topic.forum_id}.{$BID}" class="topic-title">{$topic.forum_name}</a>
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
        <div class="bar">版块</div>
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
    <div class="widget">
        <div class="bar">实用工具</div>
        <div class="content-box">
            <p><a href="tools.ua.html">查看浏览器UA</a></p>
            <p><a href="tools.coder.html">编码解码器</a></p>
        </div>
    </div>
    <div class="widget">
        <div class="bar">友情链接</div>
        <div class="content-box">
            {include file="tpl:comm.friend_links"}
        </div>
    </div>
{/block}
