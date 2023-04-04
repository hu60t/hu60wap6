{JsonPage::start()}
{$jsonData = [
    'page' => 'resetPwdPage',
	'step' => 2
]}
{if $msg}
    {$jsonData.success = false}
    {$jsonData.notice = $msg}
{/if}
{JsonPage::output($jsonData)}