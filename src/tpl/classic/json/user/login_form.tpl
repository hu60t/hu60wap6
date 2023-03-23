{JsonPage::start()}
{$jsonData = [
    'isLogin' => false,
    'page' => 'loginPage',
    'success' => false,
    'notice' => '需要登录'
]}

{if $msg}
    {$jsonData.success = false}
    {$jsonData.notice = $msg}
    {$jsonData.active = $active}
{/if}

{JsonPage::output($jsonData)}