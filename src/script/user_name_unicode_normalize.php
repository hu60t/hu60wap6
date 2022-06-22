<?php
// 对用户名进行Unicode规范化
if ('cli' != php_sapi_name()) {
    die('run in shell: php user_name_unicode_normalize.php');
}
include '../config.inc.php';
$ENABLE_CC_BLOCKING = false;

$db = (new db())->conn();
$db->setAttribute(PDO::ERRMODE_EXCEPTION, true);

try {
    $rs = $db->query('SELECT uid,name FROM hu60_user');
    $update = $db->query('UPDATE hu60_user SET name=? WHERE uid=?');
    $users = $rs->fetchAll(db::ass);

    foreach ($users as $uinfo) {
        $name = str::normalize($uinfo['name']);
        if ($name !== $uinfo['name']) {
            echo "#", $uinfo['uid'], ' ', $name, ": ", json_encode($uinfo['name']), ' -> ', json_encode($name), "\n";
            $update->execute([$uinfo['uid'], $name]);
        }
    }
} catch (Exception $ex) {
    echo "uid: $uinfo[uid], name: $uinfo[name]\nException: " . $ex->getMessage() . "\n";
}
