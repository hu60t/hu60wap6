{JsonPage::start()}

{config_load file="conf:site.info"}
{if $fid == 0}
	{$fName=#BBS_INDEX_NAME#}
{else}
	{$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}

{$jsonData=['fName'=>$fName, 'fIndex'=>$fIndex, 'childForum'=>$forumInfo, 'topicList'=>NULL, 'countReview'=>$countReview]}
{JsonPage::output($jsonData)}