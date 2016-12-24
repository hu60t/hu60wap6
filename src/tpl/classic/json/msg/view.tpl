{JsonPage::start()}

{$isSender=$USER.uid == $msg.byuid}

{$msg.toUinfo = ['name'=>$msg.toname]}
{$msg.byUinfo = ['name'=>$msg.byname]}

{JsonPage::unset($msg, 'toname')}
{JsonPage::unset($msg, 'byname')}

{$jsonData = ['isSender'=>$isSender, 'msg'=>$msg]}
{JsonPage::output($jsonData)}
