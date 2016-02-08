<?php
if ('cli' != php_sapi_name()) {
    die('run in shell: php import_hf.php');
}
include '../config.inc.php';

ini_set('memory_limit', '512M');

$db = new db();

$size = 100;

$ubb = new ubbparser();
$floor = [];

try {
for ($offset = 0; true; $offset += $size) {
    $rs = $db->query('select * from hf order by id asc limit '.$offset.','.$size);
    $datas = $rs->fetchAll(db::ass);

    if (empty($datas)) {break;}

    foreach ($datas as $data) {
        #$content = $ubb->parse($data['nr'],true);



        $newData = [
            $data['id']+81000,
            $data['tzid']+500,
            $data['hftime'],
            $data['hftime'],
            $ubb->parse($data['nr'],true),
            $data['uid'],
            $data['tzid']+2000,
            ++$floor[$data['tzid']]
        ];

        //var_dump($newData);

        $db->query('insert into hu60_bbs_topic_content_tmp2 values(?,?,?,?,?,?,?,?)', $newData);

        //die;
    }

    echo $offset."(".memory_get_usage().")\n";
    //break;
}
} catch (Exception $ex) {
    var_dump($data);
    echo "id: $data[id]\nException: ".$ex->getMessage()."\n";
}

