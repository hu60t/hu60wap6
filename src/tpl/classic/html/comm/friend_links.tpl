{include file="tpl:site.friend_links_data" scope="parent"}
{*$currLinkKeys=array_rand($FRIEND_LINKS, min(count($FRIEND_LINKS), 12))}
{for $line = 0 to 2}
	<p>
	{for $row = 0 to 3}
		{$i = $line * 4 + $row}
		{if isset($currLinkKeys[$i])}
			{$link = $FRIEND_LINKS[$currLinkKeys[$i]]}
			<a href="{$link[1]|code}">{$link[0]|code}</a>
		{else}
			空位
		{/if}
		{if $row < 3}|{/if}
	{/for}
	<p>
{/for*}
{$rows = count($FRIEND_LINKS)/4 - 1}
{for $line = 0 to $rows}
    <p>
    {for $row = 0 to 3}
    {$i = $line * 4 + $row}
    {if isset($FRIEND_LINKS[$i])}
        {$link = $FRIEND_LINKS[$i]}
        <a href="{$link[1]|code}">{$link[0]|code}</a>
    {else}
            空位
        {/if}
        {if $row < 3}|{/if}
    {/for}
    <p>
{/for}

