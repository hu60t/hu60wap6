{JsonPage::start()}

{foreach $topicList as $key=>$v}
	{$topicList.$key.uinfo = ['name'=>$v.uinfo.name]}
{/foreach}

{if !$err}
	{$jsonData=['success'=>true, 'topicCount'=>$count, 'maxPage'=> $maxP, 'topicList'=>$topicList]}
{else}
	{$jsonData=['success'=>false, 'notice'=>$err->getMessage()]}
{/if}
{JsonPage::output($jsonData)}