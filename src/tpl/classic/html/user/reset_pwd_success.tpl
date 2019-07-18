{extends file='tpl:comm.default'}

{block name='title'}找回密码{/block}

{block name='body'}
<div id="active_success">
	<p>密码修改成功，请<a href="{$CID}.login.{$BID}">重新登录</a>。</p>
	<p><a href="index.index.{$BID}">返回首页</a></p>
</div>
{/block}
