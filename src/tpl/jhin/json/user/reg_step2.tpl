{JsonPage::start()}
{$jsonData = ['page'=>'regStep2']}

{if $msg}
    {$jsonData.success = false}
    {$jsonData.notice = $msg}
{/if}

{JsonPage::output($jsonData)}