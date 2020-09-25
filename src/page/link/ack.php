<?php
$USER->start();

if (!$USER->islogin) {
    $USER->gotoLogin(true);
}

$type = $PAGE->ext[0];
$id = (int)$PAGE->ext[1];
$url = trim(code::b64d($_GET['url64']));

if ($type == 'at') {
    $msg = new msg($USER);
    $msg->read_msg($USER->uid, $id);
}

header("Location: $url");
