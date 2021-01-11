{JsonPage::start()}

{foreach $replyList as $key=>$v}
	{$replyList.$key.uinfo = ['name'=>$v.uinfo.name]}
    {JsonPage::selUbbP($v.ubb)}
    {$replyList.$key.content = $v.ubb->display($v.content,true)}
{/foreach}

{$jsonData=['success'=>true, 'uid'=>$uinfo.uid, 'replyCount'=>$count, 'maxPage'=> $maxP, 'replyList'=>$replyList]}
{JsonPage::output($jsonData)}
