<?php
die('run in cli: php import_msg.php #finished');
include '../../config.inc.php';
$db = new db();

$size = 100;

$ubb = new ubbdisplay();

for ($offset = 0; true; $offset += $size) {
    $rs = $db->query('select * from msg order by id asc limit '.$offset.','.$size);
    $datas = $rs->fetchAll(db::ass);

    if (empty($datas)) {break;}

    foreach ($datas as $data) {
        $newData = [
            $data['id'],
            $data['uid'],
            $data['byuid'],
            0,
            $data['read'],
            $ubb->parse($data['nr'],true),
            $data['time'],
            $data['time']
        ];

        $db->query('insert into hu60_msg_tmp values(?,?,?,?,?,?,?,?)', $newData);
    }

    echo $offset."\n";
    flush();
    //break;
}

function mkinfo($qianm, $lianx) {
    return serialize(['signature' => $qianm, 'contact' => $lianx]);
}

function mkpass($pass)
{
return md5(USER_PASS_KEY.$pass.USER_PASS_KEY);
}
