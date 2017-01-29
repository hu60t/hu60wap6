{config_load file="conf:site.info"}
<!DOCTYPE html>
<html><head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1">
  <meta http-equiv="content-type" content="{$page.mime};charset=utf-8"/>
  <link rel="stylesheet" type="text/css" href="{$css|code}?r=4"/>
  <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl('css/admin.css')}?r=4"/>
  <script src="{$PAGE->getTplUrl("js/jquery/dist/jquery.min.js")}"></script>
  <title>{block name='title'}后台管理{/block}</title>
</head>
<body>
  <div class="container">
    <div class="layout-inner">
      <div class="layout-head">
      </div>
      <div class="layout-body">
        <div class="layout-sidebar">
        </div>
        <div class="layout-content">
          {block name='body'}{/block}
        </div>
      </div>
      <div class="layout-foot">
        <div class="layout-foot-inner">
          这是管理员后台，采用独立的布局。
        </div>
      </div>
    </div>
  </div>


</body></html>
