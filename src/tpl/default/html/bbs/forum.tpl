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
<a href="{$CID}.{$PID}.0.1.{$BID}">新帖</a>
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
{if $forumInfo}
    {foreach $forumInfo as $forum}
        <div class="fl">
        <div class="bm">
            <div class="bm_title a" id="cr180_catlist_{$forum.id}" onclick="cr180_swap_displaybox('cr180_catlist_{$forum.id}');">
            <span class="tit">{$forum.name|code}</span><span class="pt_y"><a  href="javascript:;" onclick="location.href='bbs.forum.{$forum['id']}.{$BID}'" >以下所有贴</a></span>
            </div>
            <div id="cr180_catlist_{$forum.id}_menu" class="cl onboxl">
                    <div class="bm_c add">
						<ol>
						{foreach $forum.newTopic as $topic}
							<li><a href="{$CID}.topic.{$topic.topic_id}.{$BID}">{$topic.title|code}</a></li>
						{/foreach}
						</ol>
                        <p class="cr_cb">一共{$BBS->topicCount($forum.id)}主题 </p>
                    </div>
                                                            </div>
        </div>
    </div>
    {/foreach}
</div>
</div>
{/if}
{include file="tpl:comm.foot"}
