{extends file='tpl:comm.default'}
{$base=true}
{block name='title'}
	ASICBoost兼容性测试
{/block}
{block name='body'}
<div class="widget">
    <div class="bar">ASICBoost兼容性测试</div>
    <div class="content-box">
        <div id="server">
            <form action="{$CID}.{$PID}.{$BID}" method="get">
                <p>　服务器: <input name="server" value="{$smarty.get.server|code}"></p>
                <p>子账户名: <input name="user" value="{$smarty.get.user|code}"></p>
                <p></p>
                <p><input type="submit" name="action" value="测试"></p>
            </form>
        </div>
        <hr>
        <div id="result">
            <p class="{if $result}text-success{else}text-failure{/if}">{if $result}√{else}×{/if} {$stat}</p>
            <p style="padding-left: 1em">{$info}</p>
        </div>
    </div>
</div>
{/block}
