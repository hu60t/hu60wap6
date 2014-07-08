{if !$base}
<div class="ft">
<h1>    
<a href="/">首页</a>    <a href="/" class="xw0  a">触屏版</a>    <a href="{$page->geturl(["bid"=>"html"])|code}">电脑版</a>    <a href="bbs.forum.{$BID}">{#BBS_NAME#}</a>
</h1><a id="bottom" href="#top" accesskey="3"></a>
<p class="ft_pw">Powered by <strong><a href="http://lanru.tk" target="_blank">虎绿林-兰导</a></strong> 2014版权所有<a href="#top">回顶↑</a><br/>
{#CLOCK_NAME#} {date("n月j日 H:i")} 星期{call_user_func_array("str::星期",array(date("w")))}<br/>
效率:{round(microtime(true)-$smarty.server.REQUEST_TIME_FLOAT,3)}秒(压缩:{if $page.gzip}开{else}关{/if})</p>
</div></div>
{/if}
</body>
</html>