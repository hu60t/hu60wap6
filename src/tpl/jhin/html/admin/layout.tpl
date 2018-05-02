{config_load file="conf:site.info"}
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1">
    <meta http-equiv="content-type" content="{$page.mime};charset=utf-8"/>
    <link rel="stylesheet" type="text/css" href="{$css|code}?r=4"/>
    <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl('css/new.css')}?r=4"/>
    <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl('css/admin.css')}?r=4"/>
    <script src="{$PAGE->getTplUrl("js/jquery-3.1.1.min.js")}"></script>
    <title>{block name='title'}后台管理{/block}</title>
</head>
<body>
<div class="container">
    <div class="layout-inner" style="padding: 10px">
        <div class="layout-head">
        </div>
        <div class="layout-body">
            {*<div class="layout-sidebar">*}
            {*</div>*}
            <div class="layout-content">
                <div id="nav">
                    <a href="admin.index.{$bid}">报表</a>
                    <a href="admin.site.index.{$bid}">基本设置</a>
                    <a href="admin.bbs.createbk.{$bid}">创建板块</a>
                    <a href="admin.bbs.forum.{$bid}">版块管理</a>
                    <a href="admin.user.{$bid}">用户管理</a>
                </div>
                {block name='body'}{/block}
            </div>
        </div>
        {*<div class="layout-foot">
            <div class="layout-foot-inner">
                这是管理员后台，采用独立的布局。
            </div>
        </div>*}
    </div>
</div>
<style>
    #nav{
        display: flex;
        height: 40px;
        border-top: 1px solid #CCC;
        border-bottom: 1px solid #CCC;
        margin-bottom: 20px;
    }
    #nav>a{
        display: block;
        height: 40px;
        padding-left: 10px;
        padding-right: 10px;
        line-height: 40px;
        border-right: 1px solid #CCC;
    }
    #nav>a:hover{
        box-shadow: 0 0 5px #AAA;
    }
</style>
</body>
</html>
