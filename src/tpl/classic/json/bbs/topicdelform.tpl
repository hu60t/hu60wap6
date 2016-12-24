{JsonPage::start()}

{JsonPage::unset($tContent, 'content')}

{$jsonData=['tMeta'=>$tMeta, 'floorMeta'=>$tContent, 'isLogin'=>$USER->islogin]}

{if $USER->islogin}
    {$jsonData.token = $token->token()}

    {$jsonData.needReason = !$selfDel}

    {if $smarty.post.go && $err}
        {$jsonData.success=false}
        {$jsonData.notice=$err->getMessage()}
    {/if}
{/if}

{JsonPage::output($jsonData)}
