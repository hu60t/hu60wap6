{JsonPage::start()}

{$jsonData = [
    'uid' => $USER->uid,
    'name' => $USER->name,
    'signature' => $USER->getinfo('signature'),
    'contact' => $USER->getinfo('contact')
    ]}

{if $errMsg}
    {$jsonData.success = false}
    {$jsonData.notice = $errMsg}
{/if}

{JsonPage::output($jsonData)}