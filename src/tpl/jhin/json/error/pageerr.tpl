{JsonPage::start()}
{$jsonData = ['error'=>'true', 'errInfo'=> ['code'=>$err->getcode(), 'message'=>$err->getmessage(), 'file'=>$err->getfile(), 'line'=>$err->getline(), 'trace'=>$err->getTraceAsString()]]}
{JsonPage::output($jsonData)}