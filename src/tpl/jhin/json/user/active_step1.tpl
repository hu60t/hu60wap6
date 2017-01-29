{JsonPage::start()}
{$jsonData = ['page'=>'activeStep1']}

{if $errMsg}
    {$jsonData.success = false}
    {$jsonData.notice = $errMsg}
{/if}

{JsonPage::output($jsonData)}