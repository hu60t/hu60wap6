{extends file='tpl:comm.default'}

{block name='title'}
  跳转提示
{/block}

{block name='body'}
<div class="breadcrumb">网站外部链接跳转提示</div>
    <p class="text-notice">
      您点击了一个由用户发布的链接，点击下面的链接可能使您离开本站。
    </p>
    <p class="text-notice">
      本站不保证链接的安全性，请谨慎访问，防止感染病毒或上当受骗。
    </p>
    <p class="content">您访问的链接是：<a href="{$url|code}">{$url|code}</a></p>
    <hr>
    <p class="tp">我不想访问了，<a href="#" onclick="history.back()">返回上级页面</a>。</p>
</div>
{/block}
