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
	{$tContents.$key.canDel = !$v.locked && $bbs->canDel($v.uinfo.uid, true, $tMeta.uid)}
	{if $v.floor == 0}
		{$tContents.$key.canSink = $bbs->canSink($tMeta.uid,true)}
		{$tContents.$key.canSetEssence = $bbs->canSetEssence(true)}
		{$tContents.$key.canMove = $bbs->canMove($tMeta.uid,true)}
		{$tContents.$key.canLockReply = (!$tMeta.locked || $tMeta.locked==2) && $bbs->canEdit($v.uinfo.uid, true)}
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
	'blockedReply'=>$blockedReply,
	'floorReverse'=>$floorReverse,
	'canReply' => $USER->islogin && (
			!$tMeta.locked || ($tMeta.locked == 2 && $USER.uid == $tMeta.uid)
		) && (
			!$tMeta.review || $USER->hasPermission(userinfo::PERMISSION_REVIEW_POST)
		),
	'canLockReply' => (!$tMeta.locked || $tMeta.locked==2) && $bbs->canEdit($v.uinfo.uid, true),
	'canSink' => $bbs->canSink($tMeta.uid,true),
	'canSetEssence' => $bbs->canSetEssence(true),
	'canMove' => $bbs->canMove($tMeta.uid,true),
	'tContents'=>$tContents
]}

{if $USER->islogin}
	{$jsonData['token'] = $token->token()}
{/if}

{JsonPage::output($jsonData)}
