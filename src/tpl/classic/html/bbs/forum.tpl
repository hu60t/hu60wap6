{config_load file="conf:site.info"}
{if $fid == 0}
	{$fName=#BBS_INDEX_NAME#}
	{$title=#BBS_NAME#}
{else}
	{$fIndex.0.name=#BBS_INDEX_NAME#}
	{$title="{$fName} - {#BBS_NAME#}"}
{/if}
{include file="tpl:comm.head" title=$title}

<!--导航栏-->
<div class="tp">
	<a  href="index.index.{$bid}">首页</a> &gt;
	{$fName}
	(<a href="{$CID}.{$PID}.0.1.{$BID}">新帖</a>)
</div>

<!--搜索框-->
<div class="tp">
	<form method="get" action="{$CID}.search.{$BID}">
		<input name="keywords" placeholder="搜索词" />
		<input name="username" placeholder="用户名" />
		<input type="submit" value="搜索" />
	</form>
</div>
<hr>
<!--版块列表-->
<div>
{foreach $forumInfo as $forum name="forum"}
	<div>
		<div class="tp">&gt;{$forum.name|code}(<a href="bbs.forum.{$forum['id']}.{$BID}" >新帖/发帖</a>)</div>
		<ol style="padding-left:1.5em">
		{foreach $forum.newTopic as $topic}
			<li><a href="{$CID}.topic.{$topic.topic_id}.{$BID}">{$topic.title|code}</a></li>
		{/foreach}
		</ol>
	</div>
	{if !$smarty.foreach.forum.last}<hr>{/if}
{/foreach}
	
{include file="tpl:comm.foot"}
