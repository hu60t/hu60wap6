{JsonPage::start()}
{$jsonData = [
    'page' => 'resetPwdPage',
	'step' => 1,
	'captchaImg' => "{$CID}.reset_pwd_captcha.php?_origin={$smarty.get._origin|urlenc}&r={time()}"
]}
{if $msg}
    {$jsonData.success = false}
    {$jsonData.notice = $msg}
{/if}
{JsonPage::output($jsonData)}