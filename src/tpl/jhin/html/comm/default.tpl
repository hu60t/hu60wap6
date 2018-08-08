{config_load file="conf:site.info"}
{header content_type="text/html" charset="utf-8"}
<!DOCTYPE html>
<html lang="zh-hans">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1">
    <meta http-equiv="content-type" content="{$page.mime};charset=utf-8"/>
    {if $css === null}{$css=$PAGE->getTplUrl("css/{$PAGE->getCookie("css_{$PAGE->tpl}", "default")}.css")}{/if}
    <link rel="stylesheet" type="text/css" href="{$css|code}"/>
    <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl('css/new.css')}"/>
    <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl('css/github-markdown.css')}"/>
    <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl("css/animate.css")|code}"/>
    {block name='style'}{/block}
    <script src="{$PAGE->getTplUrl("js/jquery-3.1.1.min.js")}"></script>
    <title>{block name='title'}{/block}</title>
</head>
<body>
<!-- 引入用户自定义代码 -->
{if !$no_webplug && $USER && $USER->islogin}
    {$USER->getinfo('addin.webplug')}
{/if}
<!-- 用户自定义代码结束 -->
<header class="layout-header">
    <div class="case">
        <div class="header-inner">
            <div class="header-logo">
                <a href="/"><img src="{$PAGE->getTplUrl("img/logo_u16392_5.png")}"></a>
            </div>
            <ul class="header-nav">
                {if $user->uid && $user->islogin}
                    {$MSG=msg::getInstance($USER)}
                    {$newMSG=$MSG->newMsg()}
                    {$newATINFO=$MSG->newAtInfo()}
                    <li>
                        <a href="bbs.search.{$BID}?username={$USER->name|urlencode}">帖子</a>
                    </li>
                    <li>
                        <a href="msg.index.inbox.no.{$bid}">内信{if $newMSG>0}({$newMSG}){/if}</a>
                    </li>
                    <li><a href="msg.index.@.no.{$bid}">提醒{if $newATINFO>0}({$newATINFO}){/if}</a></li>
                    <li>
                        <a href="user.index.{$bid}"><img src="{$USER->avatar()}" class="userAvatar"></a>
                    </li>
                {elseif !$base}
                    <li><a href="user.login.{$bid}?u={urlencode($page->geturl())}" title="登录" style="margin-right:10px">登录</a>
                    </li>
                    <li><a href="user.reg.{$bid}?u={urlencode($page->geturl())}" title="立即注册">立即注册</a></li>
                {/if}
            </ul>
        </div>
    </div>
</header>
<div class="container">

    <div class="layout-inner">

        <!-- 页眉开始 -->
        <div class="layout-head">
            {include file='tpl:comm.header'}
        </div>
        <!-- 页眉结束 -->

        <!-- 内容开始 -->
        <div class="layout-body">
            <div class="layout-sidebar">
                {include file='tpl:comm.sidebar'}
            </div>
            <div class="layout-content">
                {block name='body'}{/block}
            </div>
        </div>
        <!-- 内容结束 -->

        <!-- 页脚开始 -->
        <div class="layout-foot">
            {include file='tpl:comm.footer'}
        </div>
        <!-- 页脚结束 -->

    </div>
    <!-- end contain -->
</div>
<footer class="layout-footer">
    <div class="case">
        <div>
            Power By <a href="https://github.com/hu60t/hu60wap6">hu60wap6</a> .
            <a href="link.tpl.classic.{$BID}?url64={code::b64e($page->geturl())}">经典主题</a> .
            <a href="index.index.{$BID}">首页</a>
        </div>
    </div>
</footer>
{block name='script'}{/block}
<!--css前缀自动补全-->
{*<script src="{$PAGE->getTplUrl("js/prefixfree/prefixfree.min.js")}"></script>*}
</body>
</html>
