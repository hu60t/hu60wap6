{JsonPage::start()}
{$jsonData = [
    'uid' => $USER->uid,
    'name' => $USER->name,
	'mail' => $USER->mail,
    'signature' => $USER->getinfo('signature'),
    'contact' => $USER->getinfo('contact'),
    'regtime' => $USER->regtime,
	'hasRegPhone' => $hasRegPhone,
	'floorReverse' => $floorReverse,
	'siteAdmin' => 'admin'===$mmbt
    ]}

{JsonPage::output($jsonData)}