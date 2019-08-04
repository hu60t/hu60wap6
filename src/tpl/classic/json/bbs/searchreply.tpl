{JsonPage::start()}
{JsonPage::selUbbP($ubb)}

{foreach $replyList as $key=>$v}
	{$replyList.$key.uinfo = ['name'=>$v.uinfo.name]}
    {$replyList.$key.content = $ubb->display($v.content,true)}
{/foreach}

{$jsonData=['replyCount'=>$count, 'maxPage'=> $maxP, 'replyList'=>$replyList]}
{JsonPage::output($jsonData)}
