{JsonPage::start()}

{$jsonData=['tMeta'=>$tMeta, 'isLogin'=>$USER->islogin]}

{if $USER->islogin}
    {$jsonData.token = $token->token()}
    {$jsonData.needReason = !$selfAct}
{/if}

{if is_object($err) && $err->getMessage()}
    {$jsonData.success=false}
    {$jsonData.notice=$err->getMessage()}
{/if}

{JsonPage::output($jsonData)}
