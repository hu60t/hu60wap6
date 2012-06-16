{if !$base}<div class="tp">
[<a href="{$page->geturl(["bid"=>"wml"])|code}">WAP1.0版</a>][<a href="#top">{$foot_tip=array("按3回顶","按6到底")}{$foot_tip.{rand(0,1)}}</a>]<br/>
{date("Y-m-d H:i:s")}<br/>
执行:{round(microtime(true)-$smarty.server.REQUEST_TIME_FLOAT,3)}秒(压缩:{if $page.gzip}开{else}关{/if})
</div><a id="bottom" href="#top" accesskey="3"></a>{/if}
</body>
</html>