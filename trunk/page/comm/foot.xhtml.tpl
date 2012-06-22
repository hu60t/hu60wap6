{if !$base}<div class="tip">
<span class="linktext">彩版</span>|<a href="{$page->geturl(["bid"=>"wml"])|code}">简版</a>|<a href="#top">回顶</a></div>
<div class="content">
{date("n月j日 H:i")} 星期{call_user_func_array("str::星期",array(date("w")))}<br/>
效率:{round(microtime(true)-$smarty.server.REQUEST_TIME_FLOAT,3)}秒(压缩:{if $page.gzip}开{else}关{/if})
<a id="bottom" href="#top" accesskey="3"></a>
</div>{/if}
</body>
</html>