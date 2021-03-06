{JsonPage::start()}

{$jsonData=['isLogin'=>$USER->islogin, 'tMeta'=>$tMeta, 'forums'=>$forums]}

{if $err}
    {$jsonData.success=false}
    {$jsonData.notice=$err->getMessage()}
{/if}

{JsonPage::output($jsonData)}
