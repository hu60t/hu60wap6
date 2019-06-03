{config_load file="conf:site.info"}
{$title="搜索 - {#BBS_NAME#}"}
{include file="tpl:comm.head" title=$title}
<!--导航栏-->
<div class="nav">
	<a  href="index.index.{$bid}" title="首页" class="pt_z">回首页</a>
	<a href="{$CID}.forum.{$BID}">{#BBS_NAME#}</a>
</div>

<form method="get" action="{$CID}.{$PID}.{$BID}">
	<input name="keywords" value="{$smarty.get.keywords|code}" placeholder="搜索词" />
	<input name="username" value="{$smarty.get.username|code}" placeholder="用户名" />
	<input type="submit" value="搜索" />
  <label for="searchType" id="searchType-label">
    <input name="searchType" id="searchType" type="checkbox" value="reply" {if $smarty.get.searchType=='reply'}checked{/if} />搜索用户回复
  </label>
</form>

<p class="cr_cb">找到{$count}个回复</p>

{if $replyList}
<!--回复列表-->
<div class="fl cl indexthreadlist">
  {foreach $replyList as $reply}
	<div class="reply-box">
    <p>回复了 <a href="bbs.topic.{$reply.topic_id}.{$BID}">{$reply.topic.title|code}</a> （<a href="user.info.{$reply.uinfo.uid}.{$BID}">{$reply.uinfo.name|code}</a> 于 {str::ago($reply.mtime)} 发表）</p>
    <blockquote class="floor_content">
      {$ubb->display($reply.content, true)}
    </blockquote>
  </div>
  {/foreach}
</div>

{$url="{$CID}.{$PID}.{$BID}?keywords={$smarty.get.keywords|urlencode}&amp;username={$smarty.get.username|urlencode}&amp;searchType={$smarty.get.searchType}&amp;p="}

<div class="pager">
	{if $p < $maxP}<a style="display:inline" href="{$url}{$p + 1}">下一页</a>{/if}
    {if $p > 1}<a style="display:inline" href="{$url}{$p-1}">上一页</a>{/if}
    {if $maxP > 1}({$p} / {$maxP}页){/if}
</div>

{/if}
{include file="tpl:comm.foot"}
