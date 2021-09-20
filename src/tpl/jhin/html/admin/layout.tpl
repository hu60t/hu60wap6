{config_load file="conf:site.info"}
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1">
    <meta http-equiv="content-type" content="{$page.mime};charset=utf-8"/>
    {if isset($css)}<link rel="stylesheet" type="text/css" href="{$css|code}?r=4"/>{/if}
    <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl('css/bulma.css')}?r=4"/>
    <script src="{$PAGE->getTplUrl("js/jquery-3.1.1.min.js")}"></script>
    <title>{block name='title'}后台管理{/block}</title>
</head>
<body>
<div class="navbar is-dark">
    <div class="container">
        <div class="navbar-menu">
            <div class="navbar-end">
                <a class="navbar-item" href="/">网站首页</a>
                <a class="navbar-item {if $PID=='index'}is-active{/if}" href="admin.index.{$bid}">报表</a>
                <a class="navbar-item {if $PID=='site'}is-active{/if}" href="admin.site.index.{$bid}">基本设置</a>
                <a class="navbar-item {if $PID=='bbs' && $PAGE->ext[0] == 'createbk'}is-active{/if}" href="admin.bbs.createbk.{$bid}">创建板块</a>
                <a class="navbar-item {if $PID=='bbs' && $PAGE->ext[0] != 'createbk'}is-active{/if}" href="admin.bbs.forum.{$bid}">版块管理</a>
                <a class="navbar-item {if $PID=='user'}is-active{/if}" href="admin.user.{$bid}">用户管理</a>
                <a class="navbar-item {if $PID=='friend_links'}is-active{/if}" href="admin.friend_links">友链管理</a>
            </div>
        </div>
    </div>
</div>
<div class="section">
    <div class="container">
        <div class="box">
            {block name='body'}{/block}
        </div>
    </div>
</div>
<style>
    #nav {
        display: flex;
        height: 40px;
        border-top: 1px solid #CCC;
        border-bottom: 1px solid #CCC;
        margin-bottom: 20px;
    }

    #nav > a {
        display: block;
        height: 40px;
        padding-left: 10px;
        padding-right: 10px;
        line-height: 40px;
        border-right: 1px solid #CCC;
    }

    #nav > a:hover {
        box-shadow: 0 0 5px #AAA;
    }
</style>
</body>
</html>
