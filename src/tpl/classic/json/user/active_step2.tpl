{JsonPage::start()}
{$jsonData = [
    'page' => 'activePage',
	'step' => 2
]}
{if $errMsg}
    {$jsonData.success = false}
    {$jsonData.notice = $errMsg}
{else}
    {$jsonData.success = true}
    {$jsonData.notice = "验证码已发送"}
{/if}
{JsonPage::output($jsonData)}