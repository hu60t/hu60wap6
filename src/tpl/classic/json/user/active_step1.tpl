{JsonPage::start()}
{$jsonData = [
    'page' => 'activePage',
	'step' => 1,
    'captchaImg' => "{$CID}.active_captcha.php?_origin={$smarty.get._origin|urlenc}&sid={$smarty.get.sid|urlenc}&r={time()}"
]}
{if $errMsg}
    {$jsonData.success = false}
    {$jsonData.notice = $errMsg}
{/if}
{JsonPage::output($jsonData)}