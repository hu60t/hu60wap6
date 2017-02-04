{JsonPage::start()}

{$jsonData=['isLogin'=>$USER->islogin, 'forums'=>$forums]}

{JsonPage::output($jsonData)}
