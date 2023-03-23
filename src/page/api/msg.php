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
$isSet = ($action == 'set');

if (isset($_POST['data'])) {
    $data = $_POST['data'];
} else {
    $data = file_get_contents('php://input');
}

$data = json_decode($data, true);
if (!is_array($data)) {
    throw new Exception('请求体应该是一个JSON对象');
}
if (isset($data['ids']) && !is_array($data['ids'])) {
    throw new Exception('ids字段必须是数组');
}
if (isset($data['type']) && !is_int($data['type'])) {
    throw new Exception('type字段必须是整数：0为内信，1为@消息');
}
if (isset($data['ids']) && isset($data['type'])) {
    throw new Exception('ids和type字段不能同时存在');
}

$msg = new msg($USER);

$result = [];

if (isset($data['type'])) {
    if ($isSet) {
        $rs = $msg->readAll($data['type']);
        $result['update'] = $rs ? $rs->rowCount() : false;
    } else {
        $offset = max((int)$data['offset'], 0);
        $size = min((int)$data['size'], 1000);
        if ($size < 1) {
            $size = 1000;
        }

        $rs = $msg->msgList($data['type'], $offset, $size, 0, 'id');
        foreach ($rs as $item) {
            $result[$item['id']] = false;
        }
    }
} else {
    foreach ($data['ids'] as $id) {
        $rs = $msg->get_msg($USER->uid, $id, 'isread');
        if (is_array($rs)) {
            $isread = $rs['isread'];
            if ($isSet && !$isread) {
                $result[$id] = $msg->update_msg($USER->uid, $id);
            } else {
                $result[$id] = (bool)$isread;
            }
        } else {
            $result[$id] = null;
        }
    }
}

header('Content-Type: application/json');
JsonPage::output([
    'result' => $result
]);
