{extends file='tpl:comm.default'}
{block name='title'}
搜索 - {#BBS_NAME#}
{/block}
{block name='body'}

<!--导航栏-->
<div class="pt breadcrumb">
  <a  href="index.index.{$bid}" title="首页" class="pt_z">回首页</a>
  <a href="{$CID}.forum.{$BID}">{#BBS_NAME#}</a>
</div>
<div style="clear:left"></div>
<div style="padding-bottom:5px">
  <form method="get" action="{$CID}.{$PID}.{$BID}" class="search-form">
    <input name="keywords" value="{$smarty.get.keywords|code}" class="search-form-keyword" placeholder="搜索词" />
    <input name="username" value="{$smarty.get.username|code}" class="serch-form-user" placeholder="用户名" />
    <input type="submit" class="search-form-submit" value="搜索" />
  </form>
</div>
{if $topicList}
<div class="bar">
    一共{$count}主题
</div>
<!--帖子列表-->
<div class="search-list">
        {include file='tpl:bbs.list'}
        <div class="widget-page">
            {$url="{$CID}.{$PID}.{$BID}?keywords={$smarty.get.keywords|urlencode}&amp;username={$smarty.get.username|urlencode}&amp;p=##"}
            {jhinfunc::Pager($p,$maxP,$url)}
        </div>
</div>
{/if}
{/block}
