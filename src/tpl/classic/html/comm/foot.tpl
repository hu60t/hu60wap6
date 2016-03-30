{if !$base}
	<hr>
	<div class="tp">
		<p>
			{date("n月j日 H:i")} 星期{call_user_func_array("str::星期",array(date("w")))}
		</p>
		<p>
			效率: {round(microtime(true)-$smarty.server.REQUEST_TIME_FLOAT,3)}秒(压缩:{if $page.gzip}开{else}关{/if})
		</p>
		<p>
			[<a href="index.index.{$BID}">首页</a>]
			[无触屏版]
			[<a href="#top">回顶</a>]
		</p>
		<p>
			Powered by <a href="https://github.com/hu60t/hu60wap6">hu60wap6</a>
		</p>
		{$chat=chat::getInstance()}
		{$newChat=$chat->newChat()}
		{if !empty($newChat)}
			{$ubb=ubbEdit::getInstance()}
			{$content=$ubb->display($newChat.content, true)}
			<p>[<a href="addin.chat.{$newChat.room|code}.{$BID}">聊天-{$newChat.room|code}</a>]{$newChat.uname|code}:{str::cut($content,0,10,'…')}</p>
		{/if}
{/if}
<a id="bottom" href="#top" accesskey="3"></a>
</body>
</html>
