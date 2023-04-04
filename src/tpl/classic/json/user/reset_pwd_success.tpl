{JsonPage::start()}
{$jsonData = [
    'page' => 'resetPwdPage',
	'step' => 3,
    'success' => true
]}
{JsonPage::output($jsonData)}