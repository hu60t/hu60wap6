{extends file='tpl:comm.default'}

{block name='title'}
登录成功
{/block}

{block name='body'}
<div class="forum_login">
  <div class="breadcrumb">
    嗨，<span class="text-notice">{$user.name|code}</span>，欢迎回到{#SITE_NAME#}。
  </div>
  <div class='title'>返回来源页：</div>
  <div class="">
    &nbsp;&nbsp;&nbsp;&nbsp;你可以<a href="{$smarty.server.PHP_SELF|code}/{$u|code}">点击这里返回来源页</a>继续访问。
  </div>
    <div class='title'>返回首页：</div>
    <div class="">
      &nbsp;&nbsp;&nbsp;&nbsp;你还可以<a href="{$smarty.server.PHP_SELF|code}/index.index.{$bid}">点击这里返回首页</a>。
    </div>
  </div>
{/block}
