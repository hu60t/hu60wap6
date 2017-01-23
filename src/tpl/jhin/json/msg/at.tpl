{JsonPage::start()}

{JsonPage::selUbbP($ubbs)}

{if $list}
{foreach $list as $k=>$v}
    {$list.$k.content = $ubbs->display($v.content,true)}
{/foreach}
{/if}

{$jsonData=['msgCount'=>$msgCount, 'maxPage'=> $maxP, 'msgList'=>$list]}
{JsonPage::output($jsonData)}
