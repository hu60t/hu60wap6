{JsonPage::start()}

{foreach $topicList as $key=>$v}
	{$topicList.$key.uinfo = ['name'=>$v.uinfo.name]}
{/foreach}

{$jsonData=['topicCount'=>$count, 'maxPage'=> $maxP, 'topicList'=>$topicList]}
{JsonPage::output($jsonData)}