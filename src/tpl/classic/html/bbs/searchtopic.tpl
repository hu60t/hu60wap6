{config_load file="conf:site.info"}
{$title="搜索 - {#BBS_NAME#}"}
{include file="tpl:comm.head" title=$title}
<!--导航栏-->
<div class="nav">
	<a  href="index.index.{$bid}" title="首页" class="pt_z">回首页</a>
	<a href="{$CID}.forum.{$BID}">{#BBS_NAME#}</a>
</div>

{include file="tpl:search_form"}

<p class="cr_cb">
  {if $err}
      {$err->getMessage()|code}
  {else}
      找到{$count}个主题
      {if !empty($smarty.get.username)}
        (用户: <a href="user.info.{$BID}?name={$smarty.get.username|urlencode}">{$smarty.get.username|code}</a>)
      {/if}
  {/if}
</p>

{if $topicList}
<!--帖子列表-->
<div class="fl cl indexthreadlist">
	<ul>
        {foreach $topicList as $topic}
			<li>
        {if $topic.essence}<span style="color:red;">[精]</span>{/if}
        <a class="user-title" href="{$CID}.topic.{$topic.id}.{$BID}">{$topic.title|code}</a>
      	{if $topic.review}
					<div class="topic-status">{bbs::getReviewStatName($topic.review)}</div>
				{/if}
				{if $topic.uinfo->hasPermission(UserInfo::DEBUFF_BLOCK_POST)}
					<div class="topic-status">被禁言</div>
				{/if}
				{if $topic.locked}
					<div class="topic-status">被锁定</div>
				{/if}
        {if $topic.level < 0}
          <div class="topic-status">被下沉</div>
        {/if}
        <br>
			{$topic.uinfo.name|code} 于 {date('Y-m-d H:i:s',$topic.mtime)} 发表</li>
        {/foreach}
    </ul>
</div>

{$url="{$CID}.{$PID}.{$BID}?keywords={$smarty.get.keywords|urlencode}&amp;username={$smarty.get.username|urlencode}&amp;onlyReview={$smarty.get.onlyReview|urlencode}&amp;p="}

<div class="pager">
	{if $p < $maxP}<a style="display:inline" href="{$url}{$p + 1}">下一页</a>{/if}
  {if $p > 1}<a style="display:inline" href="{$url}{$p-1}">上一页</a>{/if}
  {if $maxP > 1}
    ({$p} / {$maxP}页)
    <input placeholder="跳页" id="page" size="2" onkeypress="if(event.keyCode==13){ location='{$url}'+this.value; }">
  {/if}
</div>

{/if}
{include file="tpl:comm.foot"}
