<?php
if ('cli' != php_sapi_name()) {
    die('run in shell: php import_tz.php');
}
include '../config.inc.php';

ini_set('memory_limit', '512M');

$db = new db();

$size = 100;

$ubb = new ubbparser();

try {
for ($offset = 0; true; $offset += $size) {
    $rs = $db->query('select * from tz order by id asc limit '.$offset.','.$size);
    $datas = $rs->fetchAll(db::ass);

    if (empty($datas)) {break;}

    foreach ($datas as $data) {
        #$content = $ubb->parse($data['nr'],true);

        $newData = [
            $data['id']+500,
            $data['id']+2000,
            $data['title'],
            $data['rdcount'],
            $data['uid'],
            $data['fttime'],
            $data['hftime']
        ];

        //var_dump($newData);

        $db->query('insert into hu60_bbs_topic_meta_tmp values(?,?,?,?,?,?,?)', $newData);

        $newData = [
            $data['id']+2000,
            $data['id']+500,
            $data['fttime'],
            $data['hftime'],
            $ubb->parse($data['nr'],true),
            $data['uid'],
            0,
            0
        ];

        //var_dump($newData);

        $db->query('insert into hu60_bbs_topic_content_tmp values(?,?,?,?,?,?,?,?)', $newData);

        $newData = [
            $data['id'] + 500,
            $data['bkid']+50,
            $data['id']+500,
            $data['fttime'],
            $data['hftime']
        ];

        $db->query('insert into hu60_bbs_forum_topic_tmp values(?,?,?,?,?)', $newData);

        //die;
    }

    echo $offset."(".memory_get_usage().")\n";
    //break;
}
} catch (Exception $ex) {
    var_dump($data);
    echo "id: $data[id]\nException: ".$ex->getMessage()."\n";
}

