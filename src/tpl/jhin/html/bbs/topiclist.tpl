{extends file='tpl:comm.default'}
{config_load file="conf:site.info"}

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

	(<a href="{$CID}.newtopic.{(int)$forum.id}.{$BID}">发帖</a>)

	{if $forumInfo}
			&gt;
			<select id="forum" onchange="location='{$CID}.{$PID}.'+this.options[this.selectedIndex].value+'.{$BID}'">
				<option value="0">进入子版块</option>
				{foreach $forumInfo as $forum}
					<option value="{$forum.id}">{$forum.name|code}</option>
				{/foreach}
			</select>
	{/if}
	<input
		type="checkbox"
		id="only_essence"
		{if $onlyEssence}checked{/if}
		onchange="location='{$CID}.{$PID}.{$fid}.1.' + (this.checked ? 1 : 0) + '.{$BID}'"
	/><label for="only_essence">只看精华</label>
</div>


<!--帖子列表-->

<div class="topic-list">
	{include file='tpl:bbs.list'}
	<div class="widget-page">
		{jhinfunc::Pager($p,$pMax,"{$CID}.{$PID}.{$fid}.##.{intval($onlyEssence)}.{$BID}")}
	</div>
</div>

{/block}
