{JsonPage::start()}
{$jsonData = ['page'=>'regStep1']}

{if $msg}
    {$jsonData.success = false}
    {$jsonData.notice = $msg}
{/if}

{JsonPage::output($jsonData)}