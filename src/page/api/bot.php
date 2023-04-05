<?php
jsonpage::start();

$db = new db;
$rs = $db->select('uid,name,info', 'user', 'WHERE uid <= -50 AND uid != -100 ORDER BY uid DESC');
$list = $rs->fetchAll(db::ass);

foreach ($list as &$item) {
    $info = json_decode($item['info'], true);
    unset($item['info']);
    $item['avatar'] = $info['avatar']['url'];
    $item['signature'] = $info['signature'];
    $item['contact'] = $info['contact'];
}

jsonpage::output([
    'botList' => $list,
]);
