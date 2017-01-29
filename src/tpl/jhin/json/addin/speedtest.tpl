{JsonPage::start()}

{$jsonData = [
    'testResult' => $testResults
    ]}

{JsonPage::output($jsonData)}