{JsonPage::start()}
{$jsonData = ['page'=>'chpwdStep2']}

{if $errMsg}
    {$jsonData.success = false}
    {$jsonData.notice = $errMsg}
{/if}

{JsonPage::output($jsonData)}