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
        <form method="get" action="{$CID}.{$PID}.{$BID}" class="search-form">
            <input name="keywords" value="{$smarty.get.keywords|code}" class="search-form-keyword" placeholder="搜索词"/>
            <input name="username" value="{$smarty.get.username|code}" class="serch-form-user" placeholder="用户名"/>
            <input type="submit" class="search-form-submit" value="搜索"/>
            <label for="searchType" id="searchType-label">
                <input name="searchType" id="searchType" type="checkbox" value="reply" {if $smarty.get.searchType=='reply'}checked{/if} />搜索用户回复
            </label>
        </form>
    </div>
    <div class="bar">
        找到{$count}个回复
    </div>
    {if $replyList}
        <!--回复列表-->
        <div class="search-list">
            <ul class="reply-ul">
                {foreach $replyList as $reply}
                    {$topic=$reply.topic}
                    <li>
                        <div class="topic-info">
                            <div class="topic-anchor">
                                <a href="user.info.{$reply.uinfo.uid}.{$BID}">
                                <img src="{$reply.uinfo->avatar()}" class="avatar">
                                </a>
                                <a href="user.info.{$reply.uinfo.uid}.{$BID}">{$reply.uinfo.name|code}</a>
                            </div>
                            <div class="topic-title">
                                <a href="bbs.topic.{$reply.topic_id}.{$BID}">{$topic.title|code}</a>
                                <div class="topic-meta">
                                    {$topic.read_count}点击 / {str::ago($topic.mtime)}
                                </div>
                            </div>
                            <div class="reply-floor">
                                <a href="bbs.topic.{$reply.topic_id}.{$BID}">{$reply.floor}楼</a>
                            </div>
                        </div>
                        <blockquote class="reply-content">
                            {$ubb->display($reply.content, true)}
                        </blockquote>
                    </li>
                {/foreach}
            </ul>
            <div class="widget-page">
                {$url="{$CID}.{$PID}.{$BID}?keywords={$smarty.get.keywords|urlencode}&username={$smarty.get.username|urlencode}&searchType={$smarty.get.searchType}&p=##"}
                {jhinfunc::Pager($p,$maxP,$url)}
            </div>
        </div>
    {/if}
{/block}
