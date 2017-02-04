{JsonPage::start()}

{$jsonData=['isLogin'=>$USER->islogin, 'tMeta'=>$tMeta, 'forums'=>$forums]}

{if $smarty.post.go && $err}
    {$jsonData.success=false}
    {$jsonData.notice=$err->getMessage()}
{/if}

{JsonPage::output($jsonData)}
