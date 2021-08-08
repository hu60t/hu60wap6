{extends file='tpl:comm.default'}
{config_load file="conf:site.info"}
{block name='title'}
{if $fid == 0}
	{$fName=#BBS_INDEX_NAME#}
	{$title=#BBS_INDEX_NAME#}
{else}
	{$fIndex.0.name=#BBS_INDEX_NAME#}
	{$title="{$fName} - {#BBS_NAME#}"}
{/if}
{$title}
{/block}

{block name='body'}
<!--导航栏-->
<div class="breadcrumb">
	<a  href="index.index.{$bid}">首页</a> &gt;
	{$fName}
	(<a href="{$CID}.{$PID}.0.1.{$BID}">新帖</a> |
     <a href="bbs.forum.0.1.1.{$BID}">精华</a> |
	 <a href="{$CID}.newtopic.0.{$BID}">发帖</a>{if $countReview}
	| <a href="{$CID}.search.{$BID}?onlyReview=1">{$countReview}待审核</a>{/if})
</div>

<!--搜索框-->
<div class="widget-search">
	<form method="get" action="{$CID}.search.{$BID}">
		<input name="keywords" placeholder="搜索词" />
		<input name="username" placeholder="用户名" />
		<input type="submit" value="搜索" />
	</form>
</div>
<!--版块列表-->
<div>
{foreach $forumInfo as $forum name="forum"}
	<div class="forum-name">
		<a href="bbs.forum.{$forum['id']}.{$BID}" >{$forum.name|code}</a>
	</div>
	<ul class="topic-ul">
	  {foreach $forum.newTopic as $topic}
        <li>
            <div class="topic-anchor">
                <a href="user.info.{$topic.uinfo.uid}.{$BID}">
                <img src="{$topic.uinfo->avatar()}" class="avatar">
                </a>
                <a href="user.info.{$topic.uinfo.uid}.{$BID}">{$topic.uinfo.name|code}</a>
            </div>
            <div class="topic-title">
                <a class="user-title" href="bbs.topic.{$topic.topic_id}.{$BID}">{if $topic.essence}<i class="material-icons" style="color:red;">whatshot</i>{/if}{$topic.title|code}</a>
                <div class="topic-meta">
                    {$topic.read_count}点击 / {str::ago($topic.mtime)}
					{if $topic.review}
                        &nbsp;<div class="topic-status">{bbs::getReviewStatName($topic.review)}</div>
                    {/if}
                    {if $topic.uinfo->hasPermission(UserInfo::DEBUFF_BLOCK_POST)}
                        &nbsp;<div class="topic-status">被禁言</div>
                    {/if}
                    {if $topic.locked}
                        &nbsp;<div class="topic-status">被锁定</div>
                    {/if}
                    {if $topic.level < 0}
                        &nbsp;<div class="topic-status">被下沉</div>
                    {/if}
                </div>
            </div>
			<div class="topic-forum-name">
                <a href="bbs.forum.{$topic.forum_id}.{$BID}" class="topic-title">{$topic.forum_name}</a>
            </div>
            <div class="topic-reply-count">
                <a href="bbs.topic.{$topic.topic_id}.{$BID}">{$topic.reply_count}</a>
            </div>
        </li>
	  {/foreach}
	</ul>
{/foreach}
</div>

{/block}
