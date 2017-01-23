{JsonPage::start()}

{JsonPage::selUbbP($ubbs)}

{$isSender=$USER.uid == $msg.byuid}

{$msg.toUinfo = ['name'=>$msg.toname]}
{$msg.byUinfo = ['name'=>$msg.byname]}

{JsonPage::_unset($msg, 'toname')}
{JsonPage::_unset($msg, 'byname')}

{$msg.content=$ubbs->display($msg.content,true)}

{$jsonData = ['isSender'=>$isSender, 'msg'=>$msg]}
{JsonPage::output($jsonData)}
