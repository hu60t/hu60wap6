<?php
$USER->start();

if (!$USER->islogin) {
    $USER->gotoLogin(true);
}

$type = str::word($PAGE->ext[0]); // 消息类型，暂无作用
$id = (int)$PAGE->ext[1]; // 消息id

$msg = new msg($USER);
$data = $msg->get_msg($USER->uid, $id);
if (!$data) {
    throw new Exception("消息 id='$id' 不存在！");
}

switch ($data['type']) {
    case msg::TYPE_MSG:
        $url = "msg.index.view.$id.$PAGE[bid]";
        break;
    case msg::TYPE_AT_INFO:
        $content = data::unserialize($data['content']);
        if (isset($content[0]) && isset($content[0]['url'])) {
            $url = str_replace('{$BID}', $PAGE->bid, $content[0]['url']);
            if (!$data['isread']) {
                $msg->update_msg($USER->uid, $id);
            }
        } else {
            $url = "msg.index.@.no.$PAGE[bid]";
        }
        break;
    default:
        throw new Exception("未知消息类型：$data[type]");
        break;
}

header("Location: $url");
