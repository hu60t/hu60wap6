{extends file='tpl:comm.default'}

{block name='title'}
  网页插件
{/block}

{block name='body'}
<div class="breadcrumb">
	<a href="index.index.{$BID}">首页</a> &gt; 网页插件
</div>

<hr>

<div>
    保存成功，
    <a href="index.index.{$BID|code}">点击立即进入</a>。
</div>
{/block}
