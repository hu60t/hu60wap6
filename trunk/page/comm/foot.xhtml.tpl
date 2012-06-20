{if !$base}<div class="tp">
彩版|<a href="{$page->geturl(["bid"=>"wml"])|code}">简版</a>|<a href="#top">回顶</a><br/>
{date("n月j日 H:i")} 星期{call_user_func_array("str::星期",array(date("w")))}<br/>效率:{round(microtime(true)-$smarty.server.REQUEST_TIME_FLOAT,3)}秒(压缩:{if $page.gzip}开{else}关{/if})
</div><a id="bottom" href="#top" accesskey="3"></a>{/if}
</body>
</html>