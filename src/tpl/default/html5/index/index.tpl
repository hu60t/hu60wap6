{config_load file="conf:site.info"}
{include file="tpl:comm.head" title=#SITE_NAME#}
<div class="todayposts">今日0<span class="pipe">|</span>会员37<span class="pipe">|</span>在线1</div>
    {foreach $forumInfo as $forum}
        <div class="fl">
        <div class="bm">
            <div class="bm_title a" id="cr180_catlist_{$forum.id}" onclick="cr180_swap_displaybox('cr180_catlist_{$forum.id}');">
            <span class="tit">{$forum.name|code}</span>
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
    {/foreach}

        <div class="fl cl indexthreadlist">
            <div class="bm">
                <div class="bm_title_2" id="threadalllist"><span class=" a">[New Post]新帖</span></div>
                <div id="threadalllist_c">
                <div>
                                <ul>
    {foreach $newTopicC as $topic}
                                <li><a href="bbs.topic.{$forum.id}.{$topic.topic_id}.{$BID}">{$topic.title|code}</a>   <!--{$topic.uinfo.name|code} 发表于{date('Y-m-d H:i:s',$topic.mtime)}--!>
</li>
    {/foreach}
                                </ul>
                                </div>
                </div>
            </div>
        </div>
{include file="tpl:comm.foot"}