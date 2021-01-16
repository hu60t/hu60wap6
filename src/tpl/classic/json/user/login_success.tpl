{JsonPage::start()}
{$jsonData = ['success'=>true, 'uid'=>$user->uid, 'sid'=>$user->sid]}
{JsonPage::output($jsonData)}
