<?php
// 用于批量获取或设置消息的已读状态

$USER->start();

if (!$USER->islogin) {
    $USER->gotoLogin(true);
}

$field = str::word($PAGE->ext[0]);
if (!in_array($field, ['isread'])) {
    throw new Exception("未知字段 $field");
}

$action = str::word($PAGE->ext[1]);
if (!in_array($action, ['get', 'set'])) {
    throw new Exception("未知操作 $action");
}
$isSet = ($action == 'get');

if (isset($_GET['data'])) {
    $data = $_GET['data'];
} else {
    $data = file_get_contents('php://input');
}

$data = json_decode($data, true);
if (!is_array($data)) {
    throw new Exception('请求体应该是一个JSON对象');
}
if (!is_array($data['ids'])) {
    throw new Exception('ids字段必须是数组');
}

$msg = new msg;

$result = [];
foreach ($data['ids'] as $id) {
    $rs = $msg->get_msg($USER->uid, $id, 'isread');
    if (is_array($rs)) {
        $isread = $rs['isread'];
        if ($action == 'set' && !$isread) {
            $result[$id] = $msg->update_msg($USER->uid, $id);
        } else {
            $result[$id] = (bool)$isread;
        }
    } else {
        $result[$id] = null;
    }
}

header('Content-Type: application/json');
JsonPage::output([
    'result' => $result
]);
