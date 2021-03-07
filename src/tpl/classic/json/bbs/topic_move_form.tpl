{JsonPage::start()}

{$jsonData=['isLogin'=>$USER->islogin, 'tMeta'=>$tMeta, 'forums'=>$forums]}

{if is_object($err) && $err->getMessage()}
    {$jsonData.success=false}
    {$jsonData.notice=$err->getMessage()}
{/if}

{JsonPage::output($jsonData)}
