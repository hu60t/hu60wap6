{JsonPage::start()}

{$jsonData=[
    'tMeta' => $tMeta,
    'isLogin' => $USER->islogin,
    'replyLocked' => $tMeta->locked != 0
]}

{if $USER->islogin}
    {$jsonData.token = $token->token()}
    {$jsonData.needReason = false}
{/if}

{if is_object($err) && $err->getMessage()}
    {$jsonData.success=false}
    {$jsonData.notice=$err->getMessage()}
{/if}

{JsonPage::output($jsonData)}
