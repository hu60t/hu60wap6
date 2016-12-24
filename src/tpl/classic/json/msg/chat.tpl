{JsonPage::start()}

{if $chatList}
{foreach $chatList as $k=>$v}
    {$chatList.$k.content = $ubb->display($v.content,true)}
    {$ok=$uinfo->uid($v.byuid)}
    {$chatList.$k.byUinfo = ['name'=>$uinfo->name]}
    {$ok=$uinfo->uid($v.touid)}
    {$chatList.$k.toUinfo = ['name'=>$uinfo->name]}
{/foreach}
{/if}

{$jsonData=['msgCount'=>$chatCount, 'maxPage'=> $maxP, 'msgList'=>$chatList]}
{JsonPage::output($jsonData)}
