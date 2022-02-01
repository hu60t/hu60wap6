{extends file='tpl:comm.default'}
{block name='title'}
    {#SITE_NAME#}
{/block}
{block name='body'}
    <div class="widget">
        <div class="widget">
            <div class="bar">
                <a href="bbs.forum.{$BID}">绿虎论坛</a>：
                <a href="bbs.forum.0.1.{$BID}">新帖</a> |
                <a href="bbs.forum.0.1.1.{$BID}">精华</a> |
                <a href="bbs.search.{$BID}">搜索</a> |
                <a href="bbs.newtopic.0.{$BID}">发帖</a>
                {if $countReview}| <a href="bbs.search.{$BID}?onlyReview=1">{$countReview}待审核</a>{/if}
                {if $chatCountReview}| <a href="addin.chat.@.{$BID}">{$chatCountReview}聊天待审核</a>{/if}
            </div>
        </div>
        <ul class="topic-ul">
            {foreach $newTopicList as $topic}
                <li>
                    <div class="topic-anchor">
                        <a href="user.info.{$topic.uinfo.uid}.{$BID}">
                            <img src="{$topic.uinfo->avatar()}" class="avatar">
                        </a>
                        <a href="user.info.{$topic.uinfo.uid}.{$BID}">{$topic.uinfo.name|code}</a>
                    </div>
                    <div class="topic-title">
                        <a class="user-title" href="bbs.topic.{$topic.topic_id}.{$BID}">
                        {if $topic.essence}
                        <i class="material-icons" style="color:red;">whatshot</i>
                        {/if}
                        {$topic.title|code}
                        </a>
                        <div class="topic-meta">
                            {$topic.read_count}点击 / {str::ago($topic.ctime)}发布 / {str::ago($topic.mtime)}回复
                            {if $topic.review}
                                &nbsp;<div class="topic-status">{bbs::getReviewStatName($topic.review)}</div>
                            {/if}
                            {if $topic.uinfo->hasPermission(UserInfo::DEBUFF_BLOCK_POST)}
                                &nbsp;<div class="topic-status">被禁言</div>
                            {/if}
                            {if $topic.locked == 2}
				    	        &nbsp;<div class="topic-status">评论关闭</div>
			    	        {elseif $topic.locked}
                                &nbsp;<div class="topic-status">被锁定</div>
                            {/if}
                            {if $topic.level < 0}
                                <div class="topic-status">被下沉</div>
                            {/if}
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
        <div class="forum-list">
            {foreach $forumList as $forum}
                <div class="forum-list-line">
                    <div class="forum-list-parent">
                        <a href="bbs.forum.{$forum.id}.{$BID}" >{$forum.name|code}</a>
                    </div>
                    {if $forum.child}
                        <div class="forum-list-child">
                            {foreach $forum.child as $child}
                                <a href="bbs.forum.{$child.id}.{$BID}" >{$child.name|code}</a>
                            {/foreach}
                        </div>
                    {/if}
                </div>
            {/foreach}
        </div>

    </div>
    <div class="widget">
        <div class="bar">Linux游戏</div>
        <div class="content-box">
			<p><a href="https://winegame.net/">Wine游戏助手</a></p>
			<p><a href="https://winegame.net/games">游戏列表</a></p>
			<p><a href="https://winegame.net/games?genres=26">软件列表</a></p>
			<p><a href="bbs.forum.170.{$BID}">论坛板块</a></p>
			<p><a href="bbs.topic.95988.{$BID}">QQ群/微信群</a></p>
        </div>
    </div>
    <div class="widget">
        <div class="bar">实用工具</div>
        <div class="content-box">
	        <p><a href="addin.webplug.html">网页插件</a></p>
            <p><a href="tools.ua.html">查看浏览器UA</a></p>
            <p><a href="tools.coder.html">编码解码器</a></p>
        </div>
    </div>
    {if $USER->unlimit()}
    <div class="widget">
        <div class="bar">小说阅读</div>
        <div class="content-box">
            <p><a href="book.index.html">小说列表</a></p>
			<p><a href="https://xrzww.com/?t={time()}">息壤中文网</a>：承诺不剥削作者，由网文作家月影梧桐创建</p>
        </div>
    </div>
    <div class="widget">
        <div class="bar" id="friend_links_title">虎友网站展示</div>
        <div class="content-box">
            {include file="tpl:comm.friend_links"}
        </div>
    </div>
    {/if}
{/block}
