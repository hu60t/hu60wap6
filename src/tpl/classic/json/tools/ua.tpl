{JsonPage::start()}
{$data=[
    'ip' => $ip,
    'location' => $location,
    'proxy' => $proxyArray,
    'header' => $header
]}
{JsonPage::output($data)}