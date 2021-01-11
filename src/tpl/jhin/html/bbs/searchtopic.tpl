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
            {if $USER->hasPermission(userinfo::PERMISSION_REVIEW_POST)}
                <label for="onlyReview" id="onlyReview-label">
                <input name="onlyReview" id="onlyReview" type="checkbox" value="1" {if $smarty.get.onlyReview}checked{/if} />仅看待审核
                </label>
            {/if}
        </form>
    </div>
    <div class="bar">
        {if $err}
            {$err->getMessage()|code}
        {else}
            找到{$count}个主题
            {if !empty($smarty.get.username)}
                (用户: <a href="user.info.{$BID}?name={$smarty.get.username|urlencode}">{$smarty.get.username|code}</a>)
            {/if}
        {/if}
    </div>
    {if $topicList}
        <!--帖子列表-->
        <div class="search-list">
            {include file='tpl:bbs.list'}
            <div class="widget-page">
                {$url="{$CID}.{$PID}.{$BID}?keywords={$smarty.get.keywords|urlencode}&username={$smarty.get.username|urlencode}&onlyReview={$smarty.get.onlyReview|urlencode}&p=##"}
                {jhinfunc::Pager($p,$maxP,$url)}
            </div>
        </div>
    {/if}
{/block}
