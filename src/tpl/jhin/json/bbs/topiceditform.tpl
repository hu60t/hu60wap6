{JsonPage::start()}

{JsonPage::_unset($tContent, 'content')}

{$jsonData=['tMeta'=>$tMeta, 'floorMeta'=>$tContent, 'isLogin'=>$USER->islogin]}

{if $USER->islogin}
    {$jsonData.token = $token->token()}

    {$jsonData.needReason = $isAdminEdit}
    {$jsonData.editTitle = $editTitle}
    {$jsonData.content = $content}

    {if $editTitle}
        {$jsonData.title = $title}
    {/if}

    {if $smarty.post.go && $err}
        {$jsonData.success=false}
        {$jsonData.notice=$err->getMessage()}
    {/if}
{/if}

{JsonPage::output($jsonData)}
