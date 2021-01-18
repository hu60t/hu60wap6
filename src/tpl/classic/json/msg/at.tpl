{JsonPage::start()}

{JsonPage::selUbbP($ubbs)}

{if $list}
{foreach $list as $k=>$v}
    {$list.$k.content = $ubbs->display($v.content,true)}
{/foreach}
{/if}

{$jsonData=[
    'uid'=>$uinfo->uid,
    'msgCount'=>$msgCount,
    'currPage'=>$p,
    'maxPage'=>$maxP,
    'msgList'=>$list
]}
{JsonPage::output($jsonData)}
