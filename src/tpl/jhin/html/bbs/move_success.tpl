{config_load file="conf:site.info"}
{$url="$CID.topic.$topicId.$BID"}
{if $fid == 0}
{$fName=#BBS_INDEX_NAME#}
{else}
{$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}
{include file="tpl:comm.head" title="移动帖子 - {$tMeta.title} - {#BBS_NAME#}" time=3 url=$url}
    <div class="tip">
        移动成功，3秒后返回帖子。<br/>
        <a href="{$url|code}">点击立即进入</a>
    </div>
{include file="tpl:comm.foot"}
