{include file="tpl:comm.head" title="JHTML" no_webplug=true}
{config_load file="conf:site.info"}
<div class="tp">
	<a href="index.index.{$BID}">首页</a> &gt; JHTML | <a href="bbs.forum.140.html">论坛：网页插件专版</a>
</div>

<hr>

<div>
	<p>JHTML 是用 HTML + JavaScript 对虎绿林的界面进行完全重新排版的技术。</p>
	<p style="color:red">警告：从他人处复制的代码可能含有恶意程序，造成版面错乱、帐户被盗、数据损坏，甚至计算机感染病毒等严重后果！</p>
	<p style="color:green">请仅从信任的人处复制代码，并且仔细检查，避免使用不知用途的代码。</p>
</div>

<hr>

<div>
    <form method="post" action="{$CID}.{$PID}.{$BID}">
		<p>JHTML代码：</p>
		<p>
			<textarea name="jhtml" style="width:80%;height:100px;">{$jhtml|code:false:true}</textarea>
		<p>
		<p style="color:green">如果 JHTML 内容无法正常显示，请输入域名访问 html 版进行重置。</p>
		<p>
			<input type="submit" name="go" value="保存" />
		</p>
	</form>
</div>

{include file="tpl:comm.foot"}