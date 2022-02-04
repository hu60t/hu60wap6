<?php
jsonpage::start();
$USER->start();

try {
    if (!$USER->islogin) {
        throw new Exception('请先登录');
    }

    if (!isset($_FILES['file'])) {
        throw new Exception('未选择文件');
    }

    $json = json_decode((string)file_get_contents($_FILES['file']['tmp_name']), true);

    if ($json === null) {
        throw new Exception('JSON文件解析失败');
    }
    if (!is_array($json)) {
        throw new Exception('文件内容不是JSON对象');
    }

    $all = 0;
    $success = 0;
    foreach ($json as $key => $value) {
        $key = substr(str::word((string)$key), 0, 255);
        $value = substr((string)$value, 0, 16777216);
        $ok = $USER->setdata($key, $value);
        if ($ok) { $success++; }
        $all++;
    }

    jsonpage::output([
		'success'=>true,
		'islogin'=>$USER->islogin,
        'count'=>[
            'all'=>$all,
            'success'=>$success
        ]
	]);
}
catch (Exception $e) {
	jsonpage::output([
		'success'=>false,
		'islogin'=>$USER->islogin,
		'errmsg'=>$e->getMessage()
	]);
}
