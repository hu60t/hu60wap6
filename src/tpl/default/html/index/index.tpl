{config_load file="conf:site.info"}
{include file="tpl:comm.head" title=#SITE_NAME#}
<div class="todayposts">
<span style='position:absolute;left:20%;'><b><a href="addin.chat.{$BID}"><!--img src="http://www.iconpng.com/png/sleek_xp/chat.png" style="margin-top:7px;" alt="{#BBS_NAME#}"  width="20" /-->聊天</a></b></span>
<!--今日0--><span class="pipe"><!--|--></span><!--会员37--><span class="pipe"><!--|--></span><!--在线1-->
<span style='position:absolute;right:20%;'><b><a href="bbs.forum.{$BID}"><!--img src="http://yxw.webatu.pw/images/forum/forum_new.gif" alt="{#BBS_NAME#}" style="margin-top:7px;" width="20" /-->{#BBS_NAME#}</a></b></span></div>

        <div class="fl cl indexthreadlist">
            <div class="bm">
                <div class="bm_title_2" id="threadalllist"><span class=" a">[New Post]新帖</span></div>
                <div id="threadalllist_c">
                <div>
                                <ul>
    {foreach $newTopicList as $topic}
                                <li><a href="bbs.topic.{$topic.topic_id}.{$BID}">{$topic.title|code}</a>   <!--{$topic.uinfo.name|code} {date('m-d H:i',$topic.mtime)}--!>
</li>
    {/foreach}
<li style="padding: 8px 0px">
    {if $topicPage > 1}<a style="display:inline" href="?p={$topicPage-1}">上一页</a>{/if}
    {if $hasNextPage}<a style="display:inline" href="?p={$topicPage + 1}">下一页</a>{/if}
</li>
                               </ul>
                                </div>
                </div>
            </div>
        </div>
{include file="tpl:comm.foot"}
