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
<hr>
<div>
	<ol style="padding-left:2em">
		{foreach $topicList as $topic}
			<li>
				<a href="{$CID}.topic.{$topic.topic_id}.{$BID}">{$topic.title|code}</a>
				<br>
				({$topic.uinfo.name|code}/{$topic.read_count}点击/{$topic.reply_count}回复/{date('Y-m-d H:i',$topic.mtime)})
			</li>
		{/foreach}
	</ol>
	<hr>
	<p class="tp">
		{if $p < $pMax}<a href="{$CID}.{$PID}.{$fid}.{$p+1}.{$onlyEssence}.{$BID}">下一页</a>{/if}
		{if $p > 1}<a href="{$CID}.{$PID}.{$fid}.{$p-1}.{$onlyEssence}.{$BID}">上一页</a>{/if}
		{$p}/{$pMax}页,共{$topicCount}条
		<input placeholder="跳页" id="page" size="2" onkeypress="if(event.keyCode==13){ location='{$CID}.{$PID}.{$fid}.'+this.value+'.{$onlyEssence}.{$BID}'; }">
	</p>
</div>

{include file="tpl:comm.foot"}
