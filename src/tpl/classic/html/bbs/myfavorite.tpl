{config_load file="conf:site.info"}
{$title="我收藏的帖子"}
{include file="tpl:comm.head" title=$title}
<!--导航栏-->
<div class="nav">
	<a  href="index.index.{$bid}" title="首页" class="pt_z">回首页</a>
	<a href="{$CID}.forum.{$BID}">{#BBS_NAME#}</a>
</div>

{if $topicList}
<!--帖子列表-->
<div class="fl cl indexthreadlist">
	<ul>
        {foreach $topicList as $topic}
			<li><a class="user-title" href="{$CID}.topic.{$topic.id}.{$BID}">{$topic.title|code}</a>
			{$topic.uinfo.name|code} 于 {date('Y-m-d H:i:s',$topic.ctime)} 发布</li>
        {/foreach}
    </ul>
</div>

{$url="{$CID}.{$PID}.{$BID}?p="}

<div class="pager">
	{if $p < $maxP}<a style="display:inline" href="{$url}{$p + 1}">下一页</a>{/if}
    {if $p > 1}<a style="display:inline" href="{$url}{$p-1}">上一页</a>{/if}
    {if $maxP > 1}({$p} / {$maxP}页){/if}
</div>

{else}
<p>你还没有收藏任何帖子！</p>
{/if}
{include file="tpl:comm.foot"}
