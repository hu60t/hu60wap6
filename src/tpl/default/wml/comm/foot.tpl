{if !$base}<p>
{date("n月j日 H:i")} 星期{call_user_func_array("str::星期",array(date("w")))}
</p>
<p>
切换:<a href="{$page->geturl(["bid"=>"xhtml"])|code}">彩版</a>|简版|<a href="#main">回顶</a><br/>
效率:{round(microtime(true)-$smarty.server.REQUEST_TIME_FLOAT,3)}秒(压缩:{if $page.gzip}开{else}关{/if})
</p>
{/if}
</card>
</wml>