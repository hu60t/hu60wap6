{JsonPage::start()}

{if $list}
{foreach $list as $k=>$v}
    {$list.$k.content = $ubbs->display($v.content,true)}
    {$ok=$uinfo->uid($v.touid)}
    {$list.$k.toUinfo = ['name'=>$uinfo->name]}
{/foreach}
{/if}

{$jsonData=['msgCount'=>$msgCount, 'maxPage'=> $maxP, 'msgList'=>$list]}
{JsonPage::output($jsonData)}
