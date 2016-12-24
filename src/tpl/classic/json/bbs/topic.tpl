{JsonPage::start()}

{config_load file="conf:site.info"}

{if $fid == 0}
	{$fName=#BBS_INDEX_NAME#}
{else}
	{$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}

{$ok=$ubb->setOpt('at.jsFunc', 'atAdd')}

{foreach $tContents as $key=>$v}
	{$tmp = $ubb->setOpt('style.disable', $v.uinfo->hasPermission(UserInfo::PERMISSION_UBB_DISABLE_STYLE))}
	{$tContents.$key.content = $ubb->display($v.content,true)}
	{$tContents.$key.uinfo = ['name'=>$v.uinfo.name]}
	{$tContents.$key.canEdit = $bbs->canEdit($v.uinfo.uid, true)}
	{$tContents.$key.canDel = $bbs->canDel($v.uinfo.uid, true)}
	{$tContents.$key.canSink = $bbs->canSink($v.uinfo.uid,true)}
{/foreach}

{$jsonData=['fName'=>$fName, 'fIndex'=>$fIndex, 'tMeta'=>$tMeta, 'floorCount'=>$contentCount, 'maxPage'=>$maxPage, 'isLogin'=>$isLogin, 'tContents'=>$tContents]}
{JsonPage::output($jsonData)}