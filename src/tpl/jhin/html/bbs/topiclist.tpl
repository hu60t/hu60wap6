{extends file='tpl:comm.default'}
{if $fid == 0}
	{$fName=#BBS_INDEX_NAME#}
	{$title=#BBS_NAME#}
{else}
	{$fIndex.0.name=#BBS_INDEX_NAME#}
	{$title="{$fName} - {#BBS_NAME#}"}
{/if}
{block name='title'}
{$title}
{/block}
{block name='body'}

<!--导航栏-->
<div class="breadcrumb">
	<a  href="index.index.{$bid}">首页</a> &gt;
	{$size=count($fIndex)-1}
	{foreach $fIndex as $i=>$forum}
		{if $i<$size}
			<a href="{$CID}.forum.{$forum.id}.{$BID}">{$forum.name|code}</a> &gt;
		{/if}
	{/foreach}
	{if $fid == 0}
		<a href="{$CID}.forum.{$fid}.{$BID}">{$fName}</a>
	{else}
		{$fName}
	{/if}

	(<a href="{$CID}.newtopic.{$forum.id}.{$BID}">发帖</a>)

	{if $forumInfo}
			&gt;
			<select id="forum" onchange="location='{$CID}.{$PID}.'+this.options[this.selectedIndex].value+'.{$BID}'">
				<option value="0">进入子版块</option>
				{foreach $forumInfo as $forum}
					<option value="{$forum.id}">{$forum.name|code}</option>
				{/foreach}
			</select>
	{/if}
</div>


<!--帖子列表-->
<hr>
<div class="topic-list">
	{include file='tpl:bbs.list'}
	<div class="widget-page">
		{if $p < $pMax}<a href="{$CID}.{$PID}.{$fid}.{$p+1}.{$BID}">下一页</a>{/if}
		{if $p > 1}<a href="{$CID}.{$PID}.{$fid}.{$p-1}.{$BID}">上一页</a>{/if}
		{$p}/{$pMax}页,共{$topicCount}条
		<input placeholder="跳页" id="page" size="2" onkeypress="if(event.keyCode==13){ location='{$CID}.{$PID}.{$fid}.'+this.value+'.{$BID}'; }">
	</div>
</div>

{/block}
