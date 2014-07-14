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
&nbsp;&nbsp;{$fName}
<span class="pt_y"><a href="{$CID}.{$PID}.{$forum.id}.{$BID}">刷新</a></span>
</div>
{if $fid != 0 && !$forum.notopic}<a href="{$CID}.newtopic.{$forum.id}.{$BID}" class="cr_login_submit" style="background:#060">发帖</a>{/if}


<!--版块列表-->
{if $forumInfo}
{if $fid!=0}
<div class="pt">
<div class="cr180_ptzmenu">
{/if}
    {foreach $forumInfo as $forum}
{if $fid==0}
        <div class="fl">
        <div class="bm">
            <div class="bm_title a" id="cr180_catlist_{$forum.id}" onclick="cr180_swap_displaybox('cr180_catlist_{$forum.id}');">
            <span class="tit">{$forum.name|code}</span><span class="pt_y"><a  href="javascript:;" onclick="location.href='bbs.forum.{$forum['id']}.{$BID}'" >以下所有贴</a></span>
            </div>
            <div id="cr180_catlist_{$forum.id}_menu" class="cl onboxl">

{if $forum['plate']!=''}
                {foreach $forum['topic'] as $topic}
                                                                <div class="bm_c add">
                                                <a  href="javascript:;" onclick="location.href='bbs.forum.{$topic['id']}.{$BID}'" >
                        <h1>{$topic['name']}</h1>
                        <p class="cr_cb">一共{$topic.topic_count}主题 </p>
                        </a>
                    </div>
                {/foreach}
{else}
                                                                <div class="bm_c add">
                                                <a  href="javascript:;" onclick="location.href='bbs.forum.{$forum['id']}.{$BID}'" >
                        <h1>{$forum['name']}</h1>
                        <p class="cr_cb">一共{$forum.topic_count}主题 </p>
                        </a>
                    </div>
{/if}
                                                            </div>
        </div>
    </div>
{else}
<a  href="javascript:;" onclick="location.href='bbs.forum.{$forum['id']}.{$BID}'" >{$forum['name']}</a>
{/if}
    {/foreach}
{if $fid!=0}
</div>
</div>
{/if}
{/if}
<!--帖子列表-->
{if $topicList}
<div class="fl cl indexthreadlist">
                <div id="threadalllist_c">
                <div>
                                <ul>
        {foreach $topicList as $topic}
                <li><a href="{$CID}.topic.{$fid}.{$topic.topic_id}.{$BID}">{$topic.title|code}</a>
                {$topic.uinfo.name|code} 于 {date('Y-m-d H:i:s',$topic.time)} 发表</li>
        {/foreach}
                                </ul>
                </div>
                </div>
<div class="pt">
<div class="cr180_ptzmenu">
<span class="pt_z">{$sy}</span>
<span class="pt_c">{$yg}</span>
<span class="pt_y">{$xy}</span>
</div>
</div>
</div>
{/if}
{include file="tpl:comm.foot"}