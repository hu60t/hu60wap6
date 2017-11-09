{config_load file="conf:site.info"}
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1">
  <meta http-equiv="content-type" content="{$page.mime};charset=utf-8"/>
  {if $css === null}{$css=$PAGE->getTplUrl("css/{$PAGE->getCookie("css_{$PAGE->tpl}", "default")}.css")}{/if}
  <link rel="stylesheet" type="text/css" href="{$css|code}"/>
  <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl('css/new.css')}?r=5"/>
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
                    <a href="/"><img src="http://m1.local/tpl/jhin/img/hulvlin2.gif"></a>
                </div>
                <ul class="header-nav">
                    {if $user->uid && $user->islogin}
                    <li>
                        <a href="addin.webplug.{$BID}">个性化</a>
                    </li>
                    <li>
                        <a href="bbs.search.{$BID}?username={$USER->name|urlencode}">帖子</a>
                    </li>
                    <li>
                        <a href="msg.index.html">信息</a>
                    </li>
                    <li><a href="msg.index.@.{$bid}">消息</a></li>
                    <li>
                        <a href="user.exit.{$bid}?u={urlencode($page->geturl())}">退出</a>
                    </li>
                    <li>
                        <a href="user.index.{$bid}">资料</a>
                    </li>
                    <li>
                        <a href="user.avatar.{$bid}"><img src="http://m1.local/tpl/jhin/img/48_avatar_middle.jpg.gif" class="userAvatar"></a>

                    </li>
                    {else}
                    <li><a href="user.login.{$bid}?u={urlencode($page->geturl())}" title="登录" style="margin-right:10px">登录</a></li>
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
  </div>
    <footer class="layout-footer">
        <div class="case">
            <div>
                Power By <a href="https://github.com/hu60t/hu60wap6">hu60wap6</a> . <a href="link.tpl.classic.{$BID}?url64={code::b64e($page->geturl())}">Classic Theme</a>
            </div>
            <div>
                <ul class="footer-link">
                    <li><a href="http://enart.cn/">英语文化</a></li>
                    <li><a href="http://2cbk.com/">二超博客</a></li>
                    <li><a href="http://www.mhcf.net/">梦幻辰风</a></li>
                    <li></li><a href="http://blog.isoyu.com/">长信博客</a>
                    <li><a href="https://morz.org/">喵萌博客</a></li>
                    <li><a href="http://lehuidc.cn/">乐虎IDC</a></li>
                    <li><a href="https://www.5izzz.cn/">中转站博客</a></li>
                    <li><a href="http://www.jianzhanwen.com/">建站问</a></li>
                    <li><a href="http://www.aggregations.cn/">聚合体资讯</a> </li>
                    <li><a href="https://q18idc.com/">18IDC</a> </li>
                    <li><a href="http://bbs.wygcf.cf/">耀国论坛</a> </li>
                    <li><a href="http://mlapp.cn/">美丽应用</a></li>
                </ul>
            </div>
        </div>
    </footer>
  {block name='script'}{/block}
  <!--css前缀自动补全-->
  <script src="{$PAGE->getTplUrl("js/prefixfree/prefixfree.min.js")}"></script>
</body>
</html>
