{JsonPage::start()}

{$jsonData = ['page'=>'regPage']}

{$jsonData.success = false}
{$jsonData.notice = '注册功能被关闭'}
{$jsonData.reason = #SITE_REG_CLOSE_REASON#}

{JsonPage::output($jsonData)}