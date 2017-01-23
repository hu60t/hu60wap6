{JsonPage::start()}
{$jsonData = ['page'=>'chpwdStep1']}

{if $errMsg}
    {$jsonData.success = false}
    {$jsonData.notice = $errMsg}
{/if}

{JsonPage::output($jsonData)}