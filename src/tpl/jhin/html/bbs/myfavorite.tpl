{extends file='tpl:comm.default'}
{config_load file="conf:site.info"}
{block name='title'}
    我收藏的帖子
{/block}
{block name='body'}

    <!--导航栏-->
    <div class="pt breadcrumb">
        <a href="index.index.{$bid}" title="首页" class="pt_z">回首页</a>
        <a href="{$CID}.forum.{$BID}">{#BBS_NAME#}</a>
    </div>

    {if $topicList}
        <!--帖子列表-->
        <div class="search-list">
            {include file='tpl:bbs.list'}
            <div class="widget-page">
                {$url="{$CID}.{$PID}.{$BID}?p=##"}
                {jhinfunc::Pager($p,$maxP,$url)}
            </div>
        </div>
    {else}
      <p>你还没有收藏任何帖子！</p>
    {/if}
    <script>
        function unsetFavoriteTopic(tid) {
            $.getJSON("{$CID}.unsetfavoritetopic."+tid+".json", function(r) {
                location.href = "{$CID}.{$PID}.{$BID}?r={time()}";
            });
        }
    </script>
{/block}
