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
			{#SITE_BOTTOM_NOTE#}
		</p>
		<p>
			{#SITE_RECORD_NUMBER#}
		</p>
		{if !$no_chat}
			{$chat=chat::getInstance($USER)}
			{if is_object($USER) && $USER->getinfo('chat.newchat_num') > 0}
				{$newChatNum=$USER->getinfo('chat.newchat_num')}
			{else}
				{$newChatNum=1}
			{/if}
			{$newChats=$chat->newChats($newChatNum)}
			{if !empty($newChats)}
				{$ubb=ubbText::getInstance()}
				{$tmp=$ubb->setOpt('text.noUrl', true)}
				<div class="chat-new content-box">
				{foreach $newChats as $newChat}
					{$content=$ubb->display($newChat.content, true)}
					<p class="user-content">[<a href="addin.chat.{$newChat.room|code}.{$BID}">聊天-{$newChat.room|code}</a>] {$newChat.uname|code}：{str::cut($content,0,50,'…')|code}</p>
				{/foreach}
				</div>
			{/if}
		{/if}
	</div>
{/if}
<script src="{$PAGE->getTplUrl("js/hu60/footer.js", true)|code}"></script>
{if !$no_webplug && $USER && $USER->islogin && !empty($USER->webplug())}
<div id="hu60_load_notice" style="display: none; position:absolute">
    <p>网页插件加载中。如果长时间无法加载，可以考虑<a href="addin.webplug.{$BID}">修改或删除网页插件代码</a>。</p>
    <p>公告：<a href="https://hu60.cn/q.php/bbs.topic.92900.html?_origin=*">如果网站很卡，请修改网页插件内的外链js</a>（为保证能打开，此页未登录）。</p>
</div>
{$USER->webplug()}
{/if}
</body>
</html>
