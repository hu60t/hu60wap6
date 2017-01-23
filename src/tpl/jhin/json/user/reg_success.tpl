{JsonPage::start()}
{$jsonData = ['success'=>true, 'sid'=>$USER->sid]}
{JsonPage::output($jsonData)}