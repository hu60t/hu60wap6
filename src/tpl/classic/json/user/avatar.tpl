{JsonPage::start()}
{$jsonData = [
    'page'=>'uploadAvatar',
    'uid'=>$USER->uid,
    'avatar'=>$USER->avatar(),
    'isLogin'=>$USER->islogin
]}
{JsonPage::output($jsonData)}