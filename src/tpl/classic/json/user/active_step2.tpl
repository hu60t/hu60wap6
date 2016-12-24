{JsonPage::start()}
{$jsonData = ['page'=>'activeStep2']}

{if $errMsg}
    {$jsonData.success = false}
    {$jsonData.notice = $errMsg}
{/if}

{JsonPage::output($jsonData)}