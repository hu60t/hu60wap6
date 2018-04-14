{if !$base}
	<hr>
	<div class="breadcrumb">
		<p>
			{date("n月j日 H:i")} 星期{call_user_func_array("str::星期",array(date("w")))}
		</p>
		<p>
			效率: {round(microtime(true)-$smarty.server.REQUEST_TIME_FLOAT,3)}秒<!--(压缩:{if $page.gzip}开{else}关{/if})-->
		</p>
		<p>
			[<a href="index.index.{$BID}">首页</a>]
			[<a href="#top">回顶</a>]
		</p>
		<p>
			本站由 <a href="https://github.com/hu60t/hu60wap6">hu60wap6</a> 驱动
		</p>
		{if !$no_chat}
			{$chat=chat::getInstance()}
			{$newChat=$chat->newChat()}
			{if !empty($newChat)}
				{$ubb=ubbEdit::getInstance()}
				{$content=$ubb->display($newChat.content, true)}
				<p>[<a href="addin.chat.{$newChat.room|code}.{$BID}">聊天-{$newChat.room|code}</a>]{$newChat.uname|code}:{str::cut($content,0,10,'…')}</p>
			{/if}
		{/if}
	</div>
{/if}
<!--css前缀自动补全-->
<script src="{$PAGE->getTplUrl("js/prefixfree/prefixfree.min.js")}"></script>
</body>
</html>
