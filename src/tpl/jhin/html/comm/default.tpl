{config_load file="conf:site.info"}
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1">
  <meta http-equiv="content-type" content="{$page.mime};charset=utf-8"/>
  {if $css === null}{$css=$PAGE->getTplUrl("css/{$PAGE->getCookie("css_{$PAGE->tpl}", "default")}.css")}{/if}
  <link rel="stylesheet" type="text/css" href="{$css|code}?r=5"/>
  <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl('css/new.css')}?r=4"/>
  <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl('css/github-markdown.css')}"/>
  <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl("css/animate.css")|code}"/>
  <script src="{$PAGE->getTplUrl("js/jquery-3.1.1.min.js")}"></script>
  <title>{block name='title'}{/block}</title>
</head>
<body>
  <!-- 引入用户自定义代码 -->
  {if !$no_webplug && $USER && $USER->islogin}
    {$USER->getinfo('addin.webplug')}
  {/if}
  <!-- 用户自定义代码结束 -->

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
<!--css前缀自动补全-->
<script src="{$PAGE->getTplUrl("js/prefixfree/prefixfree.min.js")}"></script>
</body>
</html>
