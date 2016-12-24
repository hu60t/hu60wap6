{JsonPage::start()}

{if is_object($USER)}
	{if $USER->uid}
		{if $USER->islogin}
			{$MSG=msg::getInstance($USER)}
			{$newMSG=$MSG->newMsg()}
			{$newATINFO=$MSG->newAtInfo()}
			{$uinfo=['uid'=>$USER->uid, 'name'=>$USER->name, 'isLogin'=>$USER->islogin, 'newMsg'=>$newMSG, 'newAtInfo'=>$newATINFO]}
		{else}
			{$uinfo=['uid'=>$USER->uid, 'name'=>$USER->name, 'isLogin'=>$USER->islogin]}
		{/if}
	{else}
		{$uinfo=['uid'=>NULL, 'name'=>NULL, 'isLogin'=>$USER->islogin]}
	{/if}
{else}
	{$uinfo=NULL}
{/if}

{JsonPage::output($uinfo)}