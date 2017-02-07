{config_load file="conf:site.info"}
<!DOCTYPE html>
<html><head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1">
  <meta http-equiv="content-type" content="{$page.mime};charset=utf-8"/>
  {if $time !== null}<meta http-equiv="refresh" content="{$time};url={if $url === null}{page::geturl()|code}{else}{$url|code}{/if}"/>{/if}
  {if $css === null}{$css=$PAGE->getTplUrl("css/{$PAGE->getCookie("css_{$PAGE->tpl}", "default")}.css")}{/if}
  <link rel="stylesheet" type="text/css" href="{$css|code}?r=4"/>
  <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl('css/new.css')}?r=4"/>
  <script src="{$PAGE->getTplUrl("js/jquery/dist/jquery.min.js")}"></script>
  <title>{block name='title'}{/block}</title>
</head>
<body>
  <!-- 引入用户自定义代码 -->
  {if !$no_webplug && $USER && $USER->islogin}{$USER->getinfo('addin.webplug')}{/if}
  <div class="container">
    <div class="layout-inner">
      <div class="layout-head">
        {include file='tpl:comm.header'}
      </div>
      <div class="layout-body">
        <div class="layout-sidebar">
          {include file='tpl:comm.sidebar'}
        </div>
        <div class="layout-content">
          {block name='body'}{/block}
        </div>
      </div>
      <div class="layout-foot">
        {include file='tpl:comm.footer'}
      </div>
    </div>
  </div>


</body></html>
