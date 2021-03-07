{JsonPage::start()}
{foreach $userList as $k=>$user}
    {$userList[$k] = [
        'uid' => $user->uid,
        'name' => $user->name,
        'avatar' => $user->avatar()
    ]}
{/foreach}
{$jsonData = [
    'userList' => $userList
]}
{JsonPage::output($jsonData)}