{extends file='tpl:comm.default'}
{config_load file="conf:site.info"}
{block name='title'}
    搜索 - {#BBS_NAME#}
{/block}
{block name='body'}

    <!--导航栏-->
    <div class="pt breadcrumb">
        <a href="index.index.{$bid}" title="首页" class="pt_z">回首页</a>
        <a href="{$CID}.forum.{$BID}">{#BBS_NAME#}</a>
    </div>
    <div class="search-box">
        {include file="tpl:search_form"}
    </div>
    <div class="bar">
        找到{$count}个回复
        {if !empty($smarty.get.username)}
            (用户: <a href="user.info.{$BID}?name={$smarty.get.username|urlenc}">{$smarty.get.username|code}</a>)
        {/if}
    </div>
    {if $replyList}
        <!--回复列表-->
        <div class="search-list">
            <ul class="reply-ul">
                {foreach $replyList as $reply}
                    {$topic=$reply.topic}
                    <li>
                        <div class="topic-info">
                            <div class="comments-anchor">
                                <a href="user.info.{$reply.uinfo.uid}.{$BID}">
                                <img src="{if $reply.uinfo}{$reply.uinfo->avatar()}{/if}" class="avatar">
                                </a>
                                <a href="user.info.{$reply.uinfo.uid}.{$BID}">{$reply.uinfo.name|code}</a>
                            </div>
                            <div class="reply-floor">
                                <a href="bbs.topic.{$reply.topic_id}.{$BID}?floor={$reply.floor}#{$reply.floor}">{$reply.floor}楼回复</a>
                            </div>
                            <div class="topic-anchor">
                                <a href="user.info.{$reply.topicUinfo.uid}.{$BID}">
                                <img src="{if $reply.topicUinfo}{$reply.topicUinfo->avatar()}{/if}" class="avatar">
                                </a>
                                <a href="user.info.{$reply.topicUinfo.uid}.{$BID}">{$reply.topicUinfo.name|code}</a>
                            </div>
                            <div class="topic-title">
                                <a class="user-title" href="bbs.topic.{$reply.topic_id}.{$BID}?floor={$reply.floor}#{$reply.floor}">{$topic.title|code}</a>
                                <div class="topic-meta">
                                    <span>
                                        {$topic.read_count}点击 / {str::ago($topic.ctime)}发布 / {str::ago($reply.mtime)}回复 /
                                        <a href="javascript:hu60_user_style_toggle(document.querySelector('#floor_content_{$reply.floor}'))">样</a> /
                                        <a href="javascript:hu60_content_display_ubb('bbs.search', {$reply.id}, 'floor_content_{$reply.floor}')">源</a>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <blockquote class="reply-content user-content" id="floor_content_{$reply.floor}">
                            {$reply.ubb->display($reply.content, true)}
                        </blockquote>
                    </li>
                {/foreach}
            </ul>

            {include file="tpl:bbs.review-all"}

            <div class="widget-page">
                {$url="{$CID}.{$PID}.{$BID}?keywords={$smarty.get.keywords|urlenc}&username={$smarty.get.username|urlenc}&searchType={$smarty.get.searchType}&onlyReview={$smarty.get.onlyReview|urlenc}&p=##"}
                {jhinfunc::Pager($p,$maxP,$url)}
            </div>
        </div>
    {/if}
{/block}
