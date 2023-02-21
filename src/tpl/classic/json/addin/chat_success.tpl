{JsonPage::start()}
{$jsonData = [
    'success'=>true,
    'url'=>$url
]}
{JsonPage::output($jsonData)}