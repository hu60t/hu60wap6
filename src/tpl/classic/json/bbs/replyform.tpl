{JsonPage::start()}

{$jsonData=['tMeta'=>$tMeta, 'isLogin'=>$USER->islogin]}

{if $USER->islogin}
    {$jsonData.token = $token->token()}

    {if $smarty.post.go && $err}
        {$jsonData.success=false}
        {$jsonData.notice=$err->getMessage()}
    {/if}
{/if}

{if $preview}
    {JsonPage::selUbbP($ubb)}
    {$jsonData.preview = $ubb->display($preview, false)}
{/if}

{JsonPage::output($jsonData)}
