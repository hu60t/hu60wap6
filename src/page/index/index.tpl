{config_load file="conf:site.info"}
{include file="tpl:comm.head" title=#SITE_NAME#}
{div class="new_topic_forum"}
    {div class="title titletext forum_title"}<a href="bbs.forum.{$BID}">{#BBS_NAME#}</a>{/div}
    {foreach $forumInfo as $forum}
        {div class="forum_area"}
		    {div class="forum_title"}
                {span class="titletext"}<b><a href="bbs.forum.{$forum.id}.{$BID}">{$forum.name|code}</a></b>{/span}
                {if !$forum.notopic}{span class="righttext titletip"}共{$forum.topic_count}帖子{/span}{/if}
	        {/div}
			{div class="topic_list"}
                {foreach $forum['topic'] as $topic}
                    {div class="{cycle id=$topic.topic_id values="tip,content"}"}
                        {span class="titletext"}<a href="bbs.topic.{$forum.id}.{$topic.topic_id}.{$BID}">{$topic.title|code}</a>{/span}<br/>
                        {$topic.uinfo.name|code} 于 {date('Y-m-d H:i:s',$topic.mtime)} 发表
                    {/div}
                {/foreach}
                {/div}
        {/div}
    {/foreach}
{/div}
{div class="title"}{#CLOCK_NAME#}{/div}
{include file="tpl:comm.foot"}