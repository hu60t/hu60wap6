<div class="layout-foot-inner">
  {if !$base}
  	<div class="tp">
  		<p>
  			{date("n月j日 H:i")} 星期{call_user_func_array("str::星期",array(date("w")))}
  		</p>
  		<p>
  			效率: {round(microtime(true)-$smarty.server.REQUEST_TIME_FLOAT,3)}秒<!--(压缩:{if $page.gzip}开{else}关{/if})-->
  		</p>
  		<p>
  			[<a href="index.index.{$BID}">首页</a>]
  			[<a href="#top">回顶</a>]
			[<a href="link.tpl.classic.{$BID}?url64={code::b64e($page->geturl())}">经典主题</a>]
  		</p>
  		<p>
  			线路：
			{include file="tpl:comm.endpoints"}
  		</p>
  		<p>
  			本站由 <a href="https://github.com/hu60t/hu60wap6">hu60wap6</a> 驱动
  		</p>
  	</div>
    {if !$no_chat}
      {$chat=chat::getInstance()}
      {$newChat=$chat->newChat()}
      {if !empty($newChat)}
        {$ubb=ubbDisplay::getInstance()}
        {$content=strip_tags($ubb->display($newChat.content, true))}
        <div class="chat-new">
          [<a href="addin.chat.{$newChat.room|code}.{$BID}">聊天-{$newChat.room|code}</a>]{$newChat.uname|code}:{str::cut($content,0,20,'…')}
        </div>
      {/if}
    {/if}
  {/if}
</div>
