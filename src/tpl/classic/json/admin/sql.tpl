{JsonPage::start()}
{$jsonData = [
    'success' => $ok,
    'error' => $msg,
    'data' => $db
]}
{JsonPage::output($jsonData)}