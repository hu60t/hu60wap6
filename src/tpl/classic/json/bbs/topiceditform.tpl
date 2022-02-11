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

{if is_object($err) && $err->getMessage()}
    {$jsonData.success=false}
    {$jsonData.notice=$err->getMessage()}
{/if}

{if $preview}
    {JsonPage::selUbbP($ubb)}
    {$jsonData.preview = $ubb->display($preview, false)}
{/if}

{JsonPage::output($jsonData)}
