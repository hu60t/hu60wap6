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
            <br/>
            搜索类型：
            <input name="searchType" type="radio" value="topic" {if $smarty.get.searchType!='reply'}checked{/if} />帖子
            <input name="searchType" type="radio" value="reply" {if $smarty.get.searchType=='reply'}checked{/if} />回复
            <br/>
            <input type="submit" class="search-form-submit" value="搜索"/>
        </form>
    </div>
    <div class="bar">
        找到{$count}个回复
    </div>
    {if $replyList}
        <!--回复列表-->
        <div class="search-list">
            <div class="reply-list">
              {foreach $replyList as $reply}
              <div class="reply-box">
                <p>回复了 <a href="user.info.{$reply.uinfo.uid}.{$BID}">{$reply.uinfo.name|code}</a> 创建的主题 > <a href="bbs.topic.{$reply.reply_id}.{$BID}">{$reply.topic.title|code}</a>  {str::ago($reply.mtime)} </p>
                <div class="comments-content">
                {$ubb->display($reply.content,true)}
                </div>
              </div>
              {/foreach}
            </div>
            <div class="widget-page">
                {$url="{$CID}.{$PID}.{$BID}?keywords={$smarty.get.keywords|urlencode}&username={$smarty.get.username|urlencode}&searchType={$smarty.get.searchType}&p=##"}
                {jhinfunc::Pager($p,$maxP,$url)}
            </div>
        </div>
    {/if}
{/block}
