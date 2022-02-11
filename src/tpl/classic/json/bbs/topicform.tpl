{JsonPage::start()}

{config_load file="conf:site.info"}
{if $fid == 0}
{$fName=#BBS_INDEX_NAME#}
{else}
{$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}

{$jsonData=['fName'=>$fName, 'fIndex'=>$fIndex, 'isLogin'=>$USER->islogin]}

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
