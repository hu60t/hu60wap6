{JsonPage::start()}
{$jsonData = [
    'uid' => $uinfo->uid,
    'name' => $uinfo->name,
    'signature' => $uinfo->getinfo('signature'),
    'contact' => $uinfo->getinfo('contact'),
    'regtime' => $uinfo->regtime
    ]}

{JsonPage::output($jsonData)}