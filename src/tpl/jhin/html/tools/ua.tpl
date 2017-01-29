{extends file='tpl:comm.default'}
{block name='title'}
	查看HTTP请求
{/block}
{block name='body'}
<div id="remote">
    <p>请求该网页的IP：{$remote|code}</p>
    <p>代理服务器：{if null == $proxy}未使用代理服务器或使用了高度匿名代理。{else}<br/>{$proxy|code:"<br/>"}{/if}</p>
</div>
<hr>
<div id="header">
    <p>不完整的HTTP请求行（sid和PHPSESSID被删除）：</p>
    <hr>
    <p>{$header|code:"<br/>"}</p>
</div>
{/block}
