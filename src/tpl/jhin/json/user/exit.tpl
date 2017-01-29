{JsonPage::start()}
{$jsonData = ['page'=>'loginPage']}

{if $smarty.post.exit}
    {$jsonData.success = true}
{/if}

{JsonPage::output($jsonData)}