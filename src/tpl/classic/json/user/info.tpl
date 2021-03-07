{JsonPage::start()}
{$jsonData = [
    'uid' => $uinfo->uid,
    'name' => $uinfo->name,
    'signature' => $uinfo->getinfo('signature'),
    'contact' => $uinfo->getinfo('contact'),
    'regtime' => $uinfo->regtime,
    'blockPostStat' => $blockPostStat,
    'isFollow' => $isFollow,
    'isBlock' => $isBlock,
    'hideUserCSS' => $hideUserCSS,
    'permissions' => $uinfo->getPermissionArray()
    ]}

{JsonPage::output($jsonData)}