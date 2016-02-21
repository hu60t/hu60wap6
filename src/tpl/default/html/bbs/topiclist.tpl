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
<div class="pt cr180_ptzmenu">
 <a  href="javascript:;" onclick="location.href='index.index.{$bid}'" title="首页" class="pt_z">回首页</a>
{$size=count($fIndex)-1}
{foreach $fIndex as $i=>$forum}
{if $i<$size}
<a href="{$CID}.forum.{$forum.id}.{$BID}">{$forum.name|code}</a>
{/if}
{/foreach}
{if $fid == 0}
		<a href="{$CID}.forum.{$fid}.{$BID}">{$fName}</a>
	{else}
		&nbsp;&nbsp;{$fName}
	{/if}
<span class="pt_y"><a href="{$CID}.{$PID}.{$forum.id}.{$BID}">刷新</a></span>
</div>
{if $fid != 0 && !$forum.notopic}<a href="{$CID}.newtopic.{$forum.id}.{$BID}" class="cr_login_submit" style="background:#060">发帖</a>{/if}
<div>
<form method="get" action="{$CID}.search.{$BID}">
<input name="keywords" placeholder="搜索词" />
<input name="username" placeholder="用户名" />
<input type="submit" value="搜索" />
</form>
</div>

<!--版块列表-->
<div class="pt">
<div class="cr180_ptzmenu">
    {foreach $forumInfo as $forum}
<a  href="javascript:;" onclick="location.href='bbs.forum.{$forum['id']}.{$BID}'" >{$forum['name']}</a>
    {/foreach}
</div>
</div>

<!--帖子列表-->
{if $topicList}
<div class="fl cl indexthreadlist">
                <div id="threadalllist_c">
                <div>
                                <ul>
        {foreach $topicList as $topic}
                <li><a href="{$CID}.topic.{$topic.topic_id}.{$BID}">{$topic.title|code}</a>
                {$topic.uinfo.name|code} 于 {date('Y-m-d H:i:s',$topic.time)} 发表</li>
        {/foreach}
                                </ul>
                </div>
                </div>
<div class="pt">
<div class="cr180_ptzmenu">
<span class="pt_z">{if $p > 1}<a href="{$CID}.{$PID}.{$fid}.{$p-1}.{$BID}">上一页</a>{/if}</span>
<span class="pt_c">第{$p}页/{$pMax}页/共{$topicCount}条</span>
<span class="pt_y">{if $p < $pMax}<a href="{$CID}.{$PID}.{$fid}.{$p+1}.{$BID}">下一页</a>{/if}</span>
</div>
</div>
</div>
{/if}
{include file="tpl:comm.foot"}
