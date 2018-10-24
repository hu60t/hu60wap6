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
            <form action="{$CID}.{$PID}.{$BID}" method="get" style="padding-top: 2px">
                <p>　服务器: <input name="server" value="{$smarty.get.server|code}"></p>
                <p>子账户名: <input name="user" value="{$smarty.get.user|code}"></p>
                <p></p>
                <p><input type="submit" name="action" value="测试"></p>
            </form>
        </div>
        {if $stat != null}
        <hr>
        <div id="result">
            {foreach $stat as $s}
                {$result=$s[0]}
                {$text=$s[1]}
                {if $result === null}
                    <p style="padding-left: 1em">{$text}</p>
                {else}
                    <p class="{if $result}text-success{else}text-failure{/if}">{if $result}√{else}×{/if} {$text}</p>
                {/if}
            {/foreach}
        </div>
        {/if}
    </div>
</div>
{/block}
