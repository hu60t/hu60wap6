<?php
if ('cli' != php_sapi_name()) {
    die('run in shell: php import_bk.php');
}
include '../config.inc.php';
$db = (new db())->conn();
$db->setAttribute(PDO::ERRMODE_EXCEPTION, true);

try {
    $rs = $db->query('SELECT * FROM qu');
    $datas = $rs->fetchAll(db::ass);
    $rs = $db->prepare('INSERT INTO hu60_bbs_forum_meta_tmp(id,parent_id,name,mtime,notopic) VALUES(?,0,?,?,1)');

    foreach ($datas as $data) {
        $newData = [
            $data['id'] + 30,
            $data['name'],
            time()
        ];

        var_dump($rs->execute($newData));
    }

    $rs = $db->query('SELECT * FROM bk');
    $datas = $rs->fetchAll(db::ass);
    $rs = $db->prepare('INSERT INTO hu60_bbs_forum_meta_tmp(id,parent_id,name,mtime,notopic) VALUES(?,?,?,?,0)');

    foreach ($datas as $data) {
        $newData = [
            $data['id'] + 50,
            $data['quid'] == 0 ? 0 : $data['quid'] + 30,
            $data['name'],
            time()
        ];

        var_dump($rs->execute($newData));
    }
} catch (Exception $ex) {
    echo "id: $data[id]\nException: " . $ex->getMessage() . "\n";
    var_dump($data);
}
