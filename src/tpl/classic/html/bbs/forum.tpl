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
	{$size=count($fIndex)-1}
	{foreach $fIndex as $i=>$forum}
		{if $i<$size}
			<a href="{$CID}.forum.{$forum.id}.{$BID}">{$forum.name|code}</a> &gt;
		{/if}
	{/foreach}
	{$fName}
	{if $fid != 0 && !$forum.notopic}
		(<a href="{$CID}.newtopic.{$forum.id}.{$BID}">发帖</a>)
	{/if}
	{if $fid != 0 && $forumInfo}
			&gt;
			<select id="forum" onchange="location='{$CID}.{$PID}.'+this.options[this.selectedIndex].value+'.{$BID}'">
				<option value="0"></option>
				{foreach $forumInfo as $forum}
					<option value="{$forum.id}">{$forum.name|code}</option>
				{/foreach}
			</select>
	{/if}
</div>

{if $fid==0}
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
			<div class="tp">&gt;{$forum.name|code}(<a href="bbs.forum.{$forum['id']}.{$BID}'" >新帖/发帖</a>)</div>
			<ol style="padding-left:1.5em">
			{foreach $forum.newTopic as $topic}
				<li><a href="{$CID}.topic.{$topic.topic_id}.{$BID}">{$topic.title|code}</a></li>
			{/foreach}
			</ol>
		</div>
		{if !$smarty.foreach.forum.last}<hr>{/if}
	{/foreach}
{else}
	<!--帖子列表-->
	<hr>
	<div>
		<ol style="padding-left:2em">
			{foreach $topicList as $topic}
				<li>
					<a href="{$CID}.topic.{$topic.topic_id}.{$BID}">{$topic.title|code}</a>
					<br>
					({$topic.uinfo.name|code}/{$topic.read_count}点击/{$topic.reply_count}回复/{date('Y-m-d H:i',$topic.time)})
				</li>
			{/foreach}
		</ol>
		<hr>
		<p class="tp">
			{if $p > 1}<a href="{$CID}.{$PID}.{$fid}.{$p-1}.{$BID}">上一页</a>{/if}
			{if $p < $pMax}<a href="{$CID}.{$PID}.{$fid}.{$p+1}.{$BID}">下一页</a>{/if}
			{$p}/{$pMax}页,共{$topicCount}条
			<input placeholder="跳页" id="page" size="2" onkeypress="if(event.keyCode==13){ location='{$CID}.{$PID}.{$fid}.'+this.value+'.{$BID}'; }">
		</p>
	</div>

{/if}
{include file="tpl:comm.foot"}
