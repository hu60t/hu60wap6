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
  {if $USER->hasPermission(userinfo::PERMISSION_REVIEW_POST)}
    <label for="onlyReview" id="onlyReview-label">
      <input name="onlyReview" id="onlyReview" type="checkbox" value="1" {if $smarty.get.onlyReview}checked{/if} />仅看待审核
    </label>
  {/if}
</form>

<p class="cr_cb">
  {if $err}
      {$err->getMessage()|code}
  {else}
      找到{$count}个主题
  {/if}
</p>

{if $topicList}
<!--帖子列表-->
<div class="fl cl indexthreadlist">
	<ul>
        {foreach $topicList as $topic}
			<li><a href="{$CID}.topic.{$topic.id}.{$BID}">{$topic.title|code}</a>
			{$topic.uinfo.name|code} 于 {date('Y-m-d H:i:s',$topic.mtime)} 发表</li>
        {/foreach}
    </ul>
</div>

{$url="{$CID}.{$PID}.{$BID}?keywords={$smarty.get.keywords|urlencode}&amp;username={$smarty.get.username|urlencode}&amp;onlyReview={$smarty.get.onlyReview|urlencode}&amp;p="}

<div class="pager">
	{if $p < $maxP}<a style="display:inline" href="{$url}{$p + 1}">下一页</a>{/if}
    {if $p > 1}<a style="display:inline" href="{$url}{$p-1}">上一页</a>{/if}
    {if $maxP > 1}({$p} / {$maxP}页){/if}
</div>

{/if}
{include file="tpl:comm.foot"}
