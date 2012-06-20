{if !$base}<div class="tp">
切换版本:彩版|<a href="{$page->geturl(["bid"=>"wml"])|code}">简版</a>|<a href="#top">回顶</a><br/>
现在时间:{date("y/m/d H:i:s")}<br/>
页面执行:{round(microtime(true)-$smarty.server.REQUEST_TIME_FLOAT,3)}秒(压缩:{if $page.gzip}开{else}关{/if})
</div><a id="bottom" href="#top" accesskey="3"></a>{/if}
</body>
</html>