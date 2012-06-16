{if !$base}<p>
[<a href="{$page->geturl(["bid"=>"xhtml"])|code}">WAP2.0版</a>][<a href="#main">回到顶部</a>]<br/>
{date("Y-m-d H:i:s")}<br/>
执行:{round(microtime(true)-$smarty.server.REQUEST_TIME_FLOAT,3)}秒(压缩:{if $page.gzip}开{else}关{/if})
</p>{/if}
</card>
</wml>