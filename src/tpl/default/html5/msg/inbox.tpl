收件箱：<hr />
{foreach $list.row as $k}
ID:{$k.id}<br />
TOUID:{$k.touid}<br />
BYUID:{$k.byuid}<br />
ISREAD:{$k.isread}<br />
CONTENT:{$k.content}<br />
CTIME:{$k.ctime}<br />
RTIME:{$k.rtime}<hr />
{/foreach}
{$list.px}