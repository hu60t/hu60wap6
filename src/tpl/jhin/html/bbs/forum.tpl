{extends file='tpl:comm.default'}

{block name='title'}
{if $fid == 0}
	{$fName=#BBS_INDEX_NAME#}
	{$title=#BBS_NAME#}
{else}
	{$fIndex.0.name=#BBS_INDEX_NAME#}
	{$title="{$fName} - {#BBS_NAME#}"}
	{$title}
{/if}
{/block}

{block name='body'}
<!--导航栏-->
<div class="breadcrumb">
	<a  href="index.index.{$bid}">首页</a> &gt;
	{$fName}
	(<a href="{$CID}.{$PID}.0.1.{$BID}">新帖</a> | <a href="{$CID}.newtopic.0.{$BID}">发帖</a>)
</div>

<!--搜索框-->
<div class="widget-search">
	<form method="get" action="{$CID}.search.{$BID}">
		<input name="keywords" placeholder="搜索词" /><br />
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
	      <a href="{$CID}.topic.{$topic.topic_id}.{$BID}" class="topic-title">{$topic.title|code}</a>
	    </li>
	  {/foreach}
	</ul>
{/foreach}
</div>

{/block}
