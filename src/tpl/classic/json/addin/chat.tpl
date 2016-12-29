{JsonPage::start()}

{JsonPage::selUbbP($ubbs)}

{foreach $list as $k=>$v}
    {$list.$k.uinfo = ['name'=>$v.uname]}
    {JsonPage::_unset($list.$k, 'uname')}

    {if $v.hidden}
        {$tmp = $uinfo->uid($v.hidden)}
        {*if $v.hidden == $v.uid}
            {$list.$k.content = "该楼层已被层主 <a href=\"#\" onclick=\"atAdd('{$uinfo->name|code}',this);return false\">@</a><a href=\"user.info.{$v.hidden}.{$BID}\">{$uinfo->name|code}</a> 自行删除。"}
        {else*}
            {$list.$k.content = "该楼层已被管理员 <a href=\"#\" onclick=\"atAdd('{$uinfo->name|code}',this);return false\">@</a><a href=\"user.info.{$v.hidden}.{$BID}\">{$uinfo->name|code}</a> 删除，层主：<a href=\"#\" onclick=\"atAdd('{$v.uname|code}',this);return false\">@</a><a href=\"user.info.{$v.uid}.{$BID}\">{$v.uname|code}</a>。"}
        {*/if*}
    {else}
        {$tmp = $uinfo->uid($v.uid)}
        {$tmp = $ubbs->setOpt('style.disable', $uinfo->hasPermission(UserInfo::PERMISSION_UBB_DISABLE_STYLE))}
        {$list.$k.content = $ubbs->display($v.content,true)}
        {$list.$k.canDel = $chat->canDel($v.uid,true)}
    {/if}
{/foreach}

{$jsonData=['chatRomName'=>$roomname, 'isLogin'=>$USER->islogin, 'chatCount'=>$count, 'maxPage'=>$maxP, 'chatList'=>$list]}
{JsonPage::output($jsonData)}
