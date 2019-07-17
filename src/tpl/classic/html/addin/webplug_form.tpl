{include file="tpl:comm.head" title="网页插件" no_webplug=true}
{config_load file="conf:site.info"}
<div class="tp">
	<a href="index.index.{$BID}">首页</a> &gt; 网页插件 | <a href="bbs.forum.140.html">论坛：网页插件专版</a>
</div>

<hr>

<div>
	<p>网页插件是一段插入{#SITE_SIMPLE_NAME#}网页首部&lt;body&gt;标签内的代码，可以在其中添加&lt;script&gt;、&lt;style&gt;等任何html标签来扩展虎绿林网页的功能。</p>
	<p style="color:red">警告：从他人处复制的代码可能含有恶意程序，造成版面错乱、帐户被盗、数据损坏，甚至计算机感染病毒等严重后果！</p>
	<p style="color:green">请仅从信任的人处复制代码，并且仔细检查，避免使用不知用途的代码。</p>
</div>

<hr>

<div>
    <form method="post" action="{$CID}.{$PID}.{$BID}">
		<p>插件代码：</p>
		<p>
			<textarea name="webplug" style="width:80%;height:100px;">{$webplug|code:false:true}</textarea>
		<p>
		<p style="color:green">保存前请先将本页存为书签，如果插件代码发生意外还能从书签进入本页删除。</p>
		<p>
			<input type="submit" name="go" value="保存" />
		</p>
	</form>
</div>
<hr>
<div>
	<p>插件存储的自定义数据：{$plugDataCount}条（{str::filesize($plugDataSize)}）</p>
	<p>
		<a href="api.webplug-data.json?onlylen=1">查看数据大小</a> |
		<a href="api.webplug-data.json">查看完整数据</a> |
		<a href="api.webplug-file.json?mime=application/octet-stream">下载数据</a> |
		<a href="bbs.topic.83603.html">数据存储API说明</a>
	</p>
	<p>
		<input name="delete" value="删除所有自定义数据" type="button" onclick="deleteAllData()" />（强烈建议您在删除数据前先下载数据进行备份。）
	</p>
	<script>
		function deleteAllData() {
			if (prompt("确定删除所有自定义数据？\n由插件存储的所有自定义数据都将永久丢失。\n强烈建议您先下载数据进行备份。\n\n输入yes确定删除。") == 'yes') {
				$.post('api.webplug-data.json?key=&value=&prefix=1', {
					key: '',
					value: '',
					prefix: 1
				}, function(result) {
					if (result.success) {
						alert('删除成功');
						location.reload();
					} else {
						alert('删除失败' + (result.notice || result.errInfo.message));
					}
				})
			} else {
				alert('操作已取消。');
			}
		}
	</script>
</div>

{include file="tpl:comm.foot"}