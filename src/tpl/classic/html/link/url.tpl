{include file="tpl:comm.head" title="网页链接"}
<div class="url_jump">
    <p>您点击了一个由用户发布的链接，点击下面的链接可能使您离开本站。</p>
    <p class="notice">本站不保证链接的安全性，请谨慎访问，防止感染病毒或上当受骗。</p>
    <p class="content">
		您访问的链接是：<a href="{$url|code}">{$url|code}</a>
		{if !strpos($url, '://') && !preg_match('#^/#', $url)}
			<br>
			<br>也可能是：<a href="http://{$url|code}">http://{$url|code}</a>
			<br>还可能是：<a href="https://{$url|code}">https://{$url|code}</a>
			<br>请自行尝试哪个能打开。
		{/if}
	</p>
    <hr>
    <p class="tp">我不想访问了，<a href="#" onclick="history.back()">返回上级页面</a>。</p>
</div>
{include file="tpl:comm.foot"}
