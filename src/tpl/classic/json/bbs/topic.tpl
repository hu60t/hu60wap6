{JsonPage::start()}

{JsonPage::selUbbP($ubb)}

{config_load file="conf:site.info"}

{if $fid == 0}
	{$fName=#BBS_INDEX_NAME#}
{else}
	{$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}

{$ok=$ubb->setOpt('at.jsFunc', 'atAdd')}

{foreach $tContents as $key=>$v}
    {$tmp = $v.uinfo->setUbbOpt($ubb)}
	{$tContents.$key.content = $ubb->display($v.content,true)}
	{$tContents.$key.uinfo = ['name'=>$v.uinfo.name]}
	{$tContents.$key.canEdit = !$v.locked && $bbs->canEdit($v.uinfo.uid, true)}
	{$tContents.$key.canDel = !$v.locked && $bbs->canDel($v.uinfo.uid, true)}
	{if $v.floor == 0}
		{$tContents.$key.canSink = !$v.locked && $bbs->canSink($v.uinfo.uid,true)}
		{$tContents.$key.canSetEssence = !$v.locked && $bbs->canSetEssence($v.uinfo.uid,true)}
		{$tContents.$key.canMove = !$v.locked && $bbs->canMove($v.uinfo.uid,true)}
	{/if}
{/foreach}

{$jsonData=[
	'fName'=>$fName,
	'fIndex'=>$fIndex,
	'tMeta'=>$tMeta,
	'floorCount'=>$contentCount,
    'currPage'=>$p,
	'maxPage'=>$maxPage,
	'isLogin'=>$USER->islogin,
	'tContents'=>$tContents,
	'blockedReply'=>$blockedReply,
	'floorReverse'=>$floorReverse
]}

{if $USER->islogin}
	{$jsonData['token'] = $token->token()}
{/if}

{JsonPage::output($jsonData)}
