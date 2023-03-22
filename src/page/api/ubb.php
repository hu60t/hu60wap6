<?php
$ubbParser = new UbbParser();

$ubbDisplay = new UbbDisplay();
$ubbDisplay->skipUnknown(TRUE);

if (isset($_GET['data'])) {
    $data = $_GET['data'];
} else {
    $data = trim(file_get_contents('php://input'));
}

$data = json_decode($data, true);
if (!is_array($data)) {
    throw new Exception('输入不是JSON');
}
if (!is_array($data['values'])) {
    throw new Exception('values字段必须是数组或对象');
}

// 兼容不同的参数传递方式
if (isset($data['input'])) {
    $_GET['_input'] = $data['input'];
}
if (isset($data['output'])) {
    $_GET['_output'] = $data['output'];
}
if (isset($_GET['_output'])) {
    $_GET['_content'] = $_GET['_output'];
}

// 根据 _content=[html|ubb|text|json] 选择输出类型
JsonPage::selUbbP($ubbDisplay);

$inputIsJson = ($_GET['_input'] == 'json');

foreach ($data['values'] as $key => &$value) {
    if ($inputIsJson) {
        if (!is_array($value)) {
            $value = json_decode($value, true);
            if (!is_array($value)) {
                throw new Exception('待解析内容必须是JSON数组或JSON数组字符串');
            }
        }
    } else {
        $value = $ubbParser->parse($value);
    }
    $value = $ubbDisplay->display($value);
}

header('Content-Type: application/json');
JsonPage::output($data);
