{JsonPage::start()}
{$jsonData = ['page'=>'loginPage']}

{if $msg}
    {$jsonData.success = false}
    {$jsonData.notice = $msg}
    {$jsonData.active = $active}
{/if}

{JsonPage::output($jsonData)}