{JsonPage::start()}

{$jsonData=['tMeta'=>$tMeta, 'isLogin'=>$USER->islogin]}

{if $USER->islogin}
    {$jsonData.token = $token->token()}
    {$jsonData.needReason = !$selfAct}
{/if}

{if $err}
    {$jsonData.success=false}
    {$jsonData.notice=$err->getMessage()}
{/if}

{JsonPage::output($jsonData)}
