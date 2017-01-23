{JsonPage::start()}

{$jsonData=['tMeta'=>$tMeta, 'isLogin'=>$USER->islogin]}

{if $USER->islogin}
    {$jsonData.token = $token->token()}

    {if $smarty.post.go && $err}
        {$jsonData.success=false}
        {$jsonData.notice=$err->getMessage()}
    {/if}
{/if}

{JsonPage::output($jsonData)}