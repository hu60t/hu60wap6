{JsonPage::start()}

{$jsonData = [
    'uid' => $USER->uid,
    'name' => $USER->name
    ]}

{if $errMsg}
    {$jsonData.success = false}
    {$jsonData.notice = $errMsg}
{/if}

{JsonPage::output($jsonData)}