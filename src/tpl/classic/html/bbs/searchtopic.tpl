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
        (用户: <a href="user.info.{$BID}?name={$smarty.get.username|urlenc}">{$smarty.get.username|code}</a>)
      {/if}
      {if $order}
          <br/>排序:
          {if $order == 'ctime'}
              发布时间 | <a href="{$CID}.{$PID}.{$BID}?keywords={$smarty.get.keywords|urlenc}&username={$smarty.get.username|urlenc}&onlyReview={$smarty.get.onlyReview|urlenc}&order=mtime">回复时间</a>
          {else}
              <a href="{$CID}.{$PID}.{$BID}?keywords={$smarty.get.keywords|urlenc}&username={$smarty.get.username|urlenc}&onlyReview={$smarty.get.onlyReview|urlenc}&order=ctime">发布时间</a> | 回复时间
          {/if}
      {/if}
  {/if}
</p>
{if $topicList}
<hr/>
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
				{if $topic.locked == 2}
					<div class="topic-status">评论关闭</div>
				{elseif $topic.locked}
					<div class="topic-status">被锁定</div>
				{/if}
        {if $topic.level < 0}
          <div class="topic-status">被下沉</div>
        {/if}
				{if $USER->hasPermission(User::PERMISSION_EDIT_TOPIC) && $topic.access == 0}
					<div class="topic-status">公开</div>
				{/if}
        <br>
			  (<a href="user.info.{$topic.uinfo.uid}.{$BID}">{$topic.uinfo.name|code}</a> / {str::ago($topic.ctime)}发布 / {str::ago($topic.mtime)}回复)</li>
        {/foreach}
    </ul>
</div>

{$url="{$CID}.{$PID}.{$BID}?keywords={$smarty.get.keywords|urlenc}&amp;username={$smarty.get.username|urlenc}&amp;onlyReview={$smarty.get.onlyReview|urlenc}&amp;order={$order}&amp;p="}

<div class="pager">
	{if $p < $maxP}<a style="display:inline" href="{$url}{$p + 1}">下一页</a>{/if}
  {if $p > 1}<a style="display:inline" href="{$url}{$p-1}">上一页</a>{/if}
  {if $maxP > 1}
    ({$p} / {$maxP}页)
    <form class="pager-form"><input placeholder="跳页" id="page" size="2" onkeyup="if(event.keyCode==13){ location='{$url}'+this.value; }"></form>
  {/if}
</div>

{/if}
{include file="tpl:comm.foot"}
