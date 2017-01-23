{extends file='tpl:admin.layout'}
{block name='title'}
	后台管理
{/block}
{block name='body'}
<div class="title">网站设置</div>
<a href="admin.site.index.{$bid}">基本设置</a>
<div class="title">论坛社区</div>
<a href="admin.bbs.createbk.{$bid}">创建板块</a>.<a href="admin.bbs.bk.{$bid}">版块管理</a><br/>
{/block}
