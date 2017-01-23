{JsonPage::start()}

{JsonPage::selUbbP($ubbs)}

{if $list}
{foreach $list as $k=>$v}
    {$list.$k.content = $ubbs->display($v.content,true)}
    {$ok=$uinfo->uid($v.byuid)}
    {$list.$k.byUinfo = ['name'=>$uinfo->name]}
{/foreach}
{/if}

{$jsonData=['msgCount'=>$msgCount, 'maxPage'=> $maxP, 'msgList'=>$list]}
{JsonPage::output($jsonData)}
