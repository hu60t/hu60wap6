{JsonPage::start()}
{$jsonData = [
    'page' => 'activePage',
	'step' => 3,
    'success' => true
]}
{JsonPage::output($jsonData)}