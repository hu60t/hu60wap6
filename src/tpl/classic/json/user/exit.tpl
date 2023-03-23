{JsonPage::start()}
{$jsonData = [
    'isLogin' => $USER.islogin,
    'page' => 'exitPage',
    'success' => false,
    'notice' => 'POST表单的exit字段不能为空'
]}

{if $smarty.post.exit}
    {$jsonData.success = true}
{/if}

{JsonPage::output($jsonData)}