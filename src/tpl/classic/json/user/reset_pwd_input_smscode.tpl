{JsonPage::start()}
{$jsonData = [
    'page' => 'resetPwdPage',
	'step' => 2
]}
{if $msg}
    {$jsonData.success = false}
    {$jsonData.notice = $msg}
{else}
    {$jsonData.success = true}
    {$jsonData.notice = "验证码已发送"}
{/if}
{JsonPage::output($jsonData)}