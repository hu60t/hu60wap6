<?php
if ('cli' != php_sapi_name()) {
    die('run in shell: php import_user.php');
}

include '../config.inc.php';

$db = new db();

$size = 100;

for ($offset = 0; true; $offset += $size) {
    $rs = $db->query('select * from user order by uid asc limit '.$offset.','.$size);
    $users = $rs->fetchAll(db::ass);

    if (empty($users)) {break;}

    foreach ($users as $user) {
        $newUser = [
            $user['uid'],
            $user['name'],
            mkpass($user['pass']),
            $user['sid'],
            (int)$user['regtime'],
            0,
            0,
            mkinfo($user['qianm'],$user['lianx']),
            $user['regphone']
        ];

        $db->query('insert into hu60_user_tmp(uid,name,pass,sid,regtime,sidtime,acctime,info,regphone) values(?,?,?,?,?,?,?,?,?)', $newUser);
    }

    echo $offset."\n";
    //break;
}

function mkinfo($qianm, $lianx) {
    return serialize(['signature' => $qianm, 'contact' => $lianx]);
}

function mkpass($pass)
{
return md5(USER_PASS_KEY.$pass.USER_PASS_KEY);
}
