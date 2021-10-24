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
        {if $err}
            {$err->getMessage()|code}
        {else}
            找到{$count}个主题
            {if !empty($smarty.get.username)}
                (用户: <a href="user.info.{$BID}?name={$smarty.get.username|urlencode}">{$smarty.get.username|code}</a>)
            {/if}
            {if $order}
                <div style="display: inline-block">排序:
                {if $order == 'ctime'}
                    发布时间 | <a href="{$CID}.{$PID}.{$BID}?keywords={$smarty.get.keywords|urlencode}&username={$smarty.get.username|urlencode}&onlyReview={$smarty.get.onlyReview|urlencode}&order=mtime">回复时间</a>
                {else}
                    <a href="{$CID}.{$PID}.{$BID}?keywords={$smarty.get.keywords|urlencode}&username={$smarty.get.username|urlencode}&onlyReview={$smarty.get.onlyReview|urlencode}&order=ctime">发布时间</a> | 回复时间
                {/if}
                </div>
            {/if}
        {/if}
    </div>
    {if $topicList}
        <!--帖子列表-->
        <div class="search-list">
            {include file='tpl:bbs.list'}
            <div class="widget-page">
                {$url="{$CID}.{$PID}.{$BID}?keywords={$smarty.get.keywords|urlencode}&username={$smarty.get.username|urlencode}&onlyReview={$smarty.get.onlyReview|urlencode}&order={$order}&p=##"}
                {jhinfunc::Pager($p,$maxP,$url)}
            </div>
        </div>
    {/if}
{/block}
