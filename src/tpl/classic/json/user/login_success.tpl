{JsonPage::start()}
{$jsonData = ['success'=>true, 'sid'=>$user->sid]}
{JsonPage::output($jsonData)}
