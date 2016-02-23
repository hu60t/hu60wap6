{include file="tpl:comm.head" title="网页插件"}
{config_load file="conf:site.info"}
<div class="tp">
	<a href="index.index.{$BID}">首页</a> &gt; 网页插件
</div>

<hr>

<div>
	<p>网页插件是一段插入{#SITE_SIMPLE_NAME#}网页首部&lt;head&gt;标签内的代码，可以在其中添加&lt;script&gt;、&lt;style&gt;等标记来扩展虎绿林网页的功能。</p>
	<p style="color:red">警告：从他人处复制的代码可能含有恶意程序，造成版面错乱、帐户被盗、数据损坏，甚至计算机感染病毒等严重后果！</p>
	<p style="color:red">请仅从信任的人处复制代码，并且仔细检查，避免使用不知用途的代码。</p>
</div>

<hr>

<div>
    <form method="post" action="{$CID}.{$PID}.{$BID}">
		<p>插件代码：</p>
		<p>
			<textarea name="webplug" style="width:80%;height:100px;">{$webplug|code}</textarea>
		<p>
		<p>
			<input type="submit" name="go" value="保存" />
		</p>
	</form>
</div>

{include file="tpl:comm.foot"}