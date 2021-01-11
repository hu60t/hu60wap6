<?php
$USER->start();
JsonPage::start();

$data = [
    'uid' => $USER->uid,
    'name' => $USER->name,
    'isLogin' => $USER->islogin,
    'newMsg' => 0,
    'newAtInfo' => 0,
    'newChats' => [],
];

// 新内信和新@消息条数
if ($USER->islogin) {
    $msg = msg::getInstance($USER);
    $data['newMsg'] = $msg->newMsg();
    $data['newAtInfo'] = $msg->newAtInfo();
}

// 聊天室新消息
$chat = new chat;
if (is_object($USER) && $USER->getinfo('chat.newchat_num') > 0) {
    $newChatNum = $USER->getinfo('chat.newchat_num');
} else {
    $newChatNum = 1;
}

$newChats = $chat->newChats($newChatNum);

$uinfo = new UserInfo;
foreach ($newChats as &$v) {
    $uinfo->uid($v['uid']);
    $ubb = new UbbDisplay;
    $uinfo->setUbbOpt($ubb);
    JsonPage::selUbbP($ubb);
    $v['content'] = $ubb->display($v['content'], true);
}

$data['newChats'] = $newChats;

JsonPage::output($data);
