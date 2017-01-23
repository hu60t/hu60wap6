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
  		</p>
  		<p>
  			线路：
  			{$host=$smarty.server.HTTP_HOST}
  			{if $host == "cmcc.cdn.hu60.cn"}腾讯云{else}<a href="https://cmcc.cdn.hu60.cn{$smarty.server.REQUEST_URI|code}">腾讯云</a>{/if} |
  			{if $host == "hu60.cn"}半ssl{else}<a href="https://hu60.cn{$smarty.server.REQUEST_URI|code}">半ssl</a>{/if} |
  			{if $host == "ssl.hu60.cn"}全ssl{else}<a href="https://ssl.hu60.cn{$smarty.server.REQUEST_URI|code}">全ssl</a>{/if} |
  			{if $host == "yd.cdn.hu60.cn"}云盾{else}<a href="http://yd.cdn.hu60.cn{$smarty.server.REQUEST_URI|code}">云盾</a>{/if} |
  			{if $host == "baidu.cdn.hu60.cn"}百度{else}<a href="http://baidu.cdn.hu60.cn{$smarty.server.REQUEST_URI|code}">百度</a>{/if} |
  			{if $host == "ipv6.hu60.cn"}IPv6{else}<a href="https://ipv6.hu60.cn{$smarty.server.REQUEST_URI|code}">IPv6</a>{/if}
  		</p>
  		<p>
  			本站由 <a href="https://github.com/hu60t/hu60wap6">hu60wap6</a> 驱动
  		</p>
  	</div>
    {if !$no_chat}
      {$chat=chat::getInstance()}
      {$newChat=$chat->newChat()}
      {if !empty($newChat)}
        {$ubb=ubbEdit::getInstance()}
        {$content=$ubb->display($newChat.content, true)}
        <div class="chat-new">
          [<a href="addin.chat.{$newChat.room|code}.{$BID}">聊天-{$newChat.room|code}</a>]{$newChat.uname|code}:{str::cut($content,0,10,'…')}
        </div>
      {/if}
    {/if}
  {/if}
</div>
