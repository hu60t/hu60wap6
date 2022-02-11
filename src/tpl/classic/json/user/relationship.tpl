{JsonPage::start()}
{foreach $userList as $k=>$user}
    {$userList[$k] = [
        'uid' => $user->uid,
        'name' => $user->name,
        'avatar' => $user->avatar()
    ]}
{/foreach}
{$jsonData = [
    'type' => $type,
    'title' => $title,
    'actions' => $actions,
    'currPage' => $currentPage,
    'maxPage' => $totalPage,
    'userList' => $userList
]}
{JsonPage::output($jsonData)}