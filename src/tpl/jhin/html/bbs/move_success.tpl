
{$url="$CID.topic.$topicId.$BID"}

{if $fid == 0}
  {$fName=#BBS_INDEX_NAME#}
{else}
  {$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}

{extends file='tpl:comm.default'}

{block name='title'}
  移动帖子 - {$tMeta.title} - {#BBS_NAME#}
{/block}
{block name='body'}
  <div class="breadcrumb">
    {$tMeta.title|code} | <a href="{$CID}.topic.{$topicId}.{$BID}">返回帖子</a>
  </div>
  <div class="text-notice">
    移动成功，3秒后返回帖子。<br/>
    <a href="{$url|code}">点击立即进入</a>
    {include file="tpl:comm.go" url=$url}
  </div>
{/block}
