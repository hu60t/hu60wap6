{JsonPage::start()}

{foreach $topicList as $key=>$v}
	{$topicList.$key.uinfo = ['name'=>$v.uinfo.name]}
{/foreach}

{if !$err}
	{$jsonData=['success'=>true, 'uid'=>$uinfo.uid, 'topicCount'=>$count, 'maxPage'=> $maxP, 'topicList'=>$topicList]}
{else}
	{$jsonData=['success'=>false, 'notice'=>$err->getMessage()]}
{/if}
{JsonPage::output($jsonData)}