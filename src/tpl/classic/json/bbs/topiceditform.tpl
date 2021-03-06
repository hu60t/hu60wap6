{JsonPage::start()}

{if isset($tContent.content)}
    {JsonPage::_unset($tContent, 'content')}
{/if}

{$jsonData=['tMeta'=>$tMeta, 'floorMeta'=>$tContent, 'isLogin'=>$USER->islogin]}

{if $USER->islogin}
    {$jsonData.token = $token->token()}

    {$jsonData.needReason = $isAdminEdit}
    {$jsonData.editTitle = $editTitle}
    {$jsonData.content = $content}

    {if $editTitle}
        {$jsonData.title = $title}
    {/if}
{/if}

{if $err}
    {$jsonData.success=false}
    {$jsonData.notice=$err->getMessage()}
{/if}

{JsonPage::output($jsonData)}
