<?php
if ('cli' != php_sapi_name()) {
    die('run in shell: php import_msg.php');
}
include '../config.inc.php';
$ENABLE_CC_BLOCKING = false;

$db = new db();

$size = 100;

$ubb = new ubbparser();

try {
    for ($offset = 0; true; $offset += $size) {
        $rs = $db->query('SELECT * FROM msg ORDER BY id ASC LIMIT ' . $offset . ',' . $size);
        $datas = $rs->fetchAll(db::ass);

        if (empty($datas)) {
            break;
        }

        foreach ($datas as $data) {
            $content = $ubb->parse($data['nr'], true);

            $newData = [
                $data['id'],
                $data['uid'],
                $data['byuid'],
                0,
                $data['read'],
                $content,
                $data['time'],
                $data['time']
            ];

            $db->query('INSERT INTO hu60_msg_tmp VALUES(?,?,?,?,?,?,?,?)', $newData);
        }

        echo $offset . "\n";
        flush();
        //break;
    }
} catch (Exception $ex) {
    echo "id: $data[id]\nException: " . $ex->getMessage() . "\n";
    var_dump($data);

}

