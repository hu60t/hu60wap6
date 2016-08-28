{$url="index.index.$BID"}
{include file="tpl:comm.head" title="网页插件" time=3 url=$url no_webplug=true}
<div class="tp">
	<a href="index.index.{$BID}">首页</a> &gt; 网页插件
</div>

<hr>

<div>
    保存成功，3秒后返回首页。<br/>
    <a href="{$url|code}">点击立即进入</a>
</div>
{include file="tpl:comm.foot"}
