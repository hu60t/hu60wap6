{JsonPage::start()}
{$jsonData = [
    'lock' => $lock,
    'success' => true
]}
{JsonPage::output($jsonData)}