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
            (用户: <a href="user.info.{$BID}?name={$smarty.get.username|urlencode}">{$smarty.get.username|code}</a>)
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
                                <a href="bbs.topic.{$reply.topic_id}.{$BID}?floor={$reply.floor}#{$reply.floor}">{$topic.title|code}</a>
                                <div class="topic-meta">
                                    {$topic.read_count}点击 / {str::ago($topic.mtime)}
                                </div>
                            </div>
                        </div>
                        <blockquote class="reply-content">
                            {$reply.ubb->display($reply.content, true)}
                        </blockquote>
                    </li>
                {/foreach}
            </ul>
            <div class="widget-page">
                {$url="{$CID}.{$PID}.{$BID}?keywords={$smarty.get.keywords|urlencode}&username={$smarty.get.username|urlencode}&searchType={$smarty.get.searchType}&onlyReview={$smarty.get.onlyReview|urlencode}&p=##"}
                {jhinfunc::Pager($p,$maxP,$url)}
            </div>
        </div>
    {/if}
{/block}
