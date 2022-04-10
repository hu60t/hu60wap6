{JsonPage::start()}

{JsonPage::selUbbP($ubbs)}

{if $preview}
    {*放在最前面，防止下面的setUbbOpt产生有害干扰*}
    {$preview = $ubbs->display($preview, false)}
{/if}

{foreach $list as $k=>$v}
    {$list.$k.uinfo = ['name'=>$v.uname]}
    {JsonPage::_unset($list.$k, 'uname')}
    {$tmp = $uinfo->uid($v.uid)}
    {$tmp = $uinfo->setUbbOpt($ubbs)}
    {$list.$k.content = $ubbs->display($v.content,true)}
    {$list.$k.canDel = !$v.hidden && $chat->canDel($v.uid,true)}
{/foreach}

{$jsonData=[
    'chatRomName'=>$roomname,
    'isLogin'=>$USER->islogin,
    'chatCount'=>$count,
    'currPage'=>$p,
    'maxPage'=>$maxP,
    'chatList'=>$list,
    'blockedReply'=>$blockedReply,
    'onlyReview'=>$onlyReview
]}

{if $err_msg}
    {$jsonData.success = false}
    {$jsonData.notice = $err_msg}
{/if}

{if $preview}
    {$jsonData.preview = $preview}
{/if}

{if $USER->islogin}
	{$jsonData['token'] = $token->token()}
{/if}

{JsonPage::output($jsonData)}
