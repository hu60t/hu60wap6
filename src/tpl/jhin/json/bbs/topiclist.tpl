{JsonPage::start()}

{config_load file="conf:site.info"}
{if $fid == 0}
	{$fName=#BBS_INDEX_NAME#}
{else}
	{$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}

{foreach $topicList as $key=>$v}
	{$topicList.$key.uinfo = ['name'=>$v.uinfo.name]}
{/foreach}

{$jsonData=['fName'=>$fName, 'fIndex'=>$fIndex, 'topicCount'=>$topicCount, 'maxPage'=>$pMax, 'childForum'=>$forumInfo, 'topicList'=>$topicList]}
{JsonPage::output($jsonData)}