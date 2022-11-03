<?php
if ('cli' != php_sapi_name()) {
    die('run in shell: php content_security_check_all_signature_contact.php [min-user-id]');
}

$minUserId = (int)$argv[1];

include '../config.inc.php';
$ENABLE_CC_BLOCKING = false;

$db = new db;
db::$DEFAULT_ERRMODE = PDO::ERRMODE_EXCEPTION;

$userList = $db->query('SELECT uid FROM '.DB_A.'user WHERE uid >= ?', $minUserId)->fetchAll(db::ass);

binarySearch($userList);

// 二分法查找有问题的用户名
function binarySearch($userList) {
    $all = count($userList);
    $half = round($all / 2);

    // 左支
    $checked = [];
    $content = "";
    for ($i=0; $i<$half; $i++) {
        $uid = $userList[$i]['uid'];
        $uinfo = new UserInfo;
        $uinfo->uid($uid);

        if ($uinfo->getinfo('signature') == '' && $uinfo->getinfo('contact') == '') {
            continue;
        }

        $newContent = "${content}".$uinfo->getinfo('signature')."\n".$uinfo->getinfo('contact')."\n\n";

        if (mb_strlen($newContent) > 10000) {
            auditUsers($checked, $content);
            $checked = [];
            $newContent = $uinfo->getinfo('signature')."\n".$uinfo->getinfo('contact')."\n\n";
        }

        $checked[] = $uinfo;
        $content = $newContent;
    }
    if (!empty($checked)) {
        auditUsers($checked, $content);
    }

    // 右支
    $checked = [];
    $content = "";
    for ($i=$half; $i<$all; $i++) {
        $uid = $userList[$i]['uid'];
        $uinfo = new UserInfo;
        $uinfo->uid($uid);

        if ($uinfo->getinfo('signature') == '' && $uinfo->getinfo('contact') == '') {
            continue;
        }

        $newContent = "${content}".$uinfo->getinfo('signature')."\n".$uinfo->getinfo('contact')."\n\n";

        if (mb_strlen($newContent) > 10000) {
            auditUsers($checked, $content);
            $checked = [];
            $newContent = $uinfo->getinfo('signature')."\n".$uinfo->getinfo('contact')."\n\n";
        }

        $checked[] = $uinfo;
        $content = $newContent;
    }
    if (!empty($checked)) {
        auditUsers($checked, $content);
    }
}

function auditUsers($userList, $content) {
    //static $rateCount = 0;

    $result = ContentSecurity::auditText(
        null,
        ContentSecurity::TYPE_SIGNATURE,
        $content,
        "user/signature"
    );

    // 限速
    // 为什么调用内容安全API返回错误码588（EXCEED_QUOTA）？
    // 报错原因：请求频率超出并发配额。默认并发：文本检测100条/秒。 
    /*$rateCount++;
    if ($rateCount >= 50) {
        sleep(1);
        $rateCount = 0;
    }*/

    set_time_limit(0);
    ini_set('max_execution_time', 0);

    if ($result['stat'] === ContentSecurity::STAT_PASS) {
        foreach ($userList as $uinfo) {
            echo "$uinfo[uid]\t$uinfo[name]\t$result[reason]\t$result[rate]%\n";
        }
    } elseif (count($userList) == 1) {
        // 个性签名有问题，清空
        foreach ($userList as $uinfo) {
            echo "$uinfo[uid]\t$uinfo[name]\t$result[reason]\t$result[rate]%\n";
            $uid = $uinfo['uid'];
            $user = new User;
            $user->uid($uid);
            $user->virtualLogin();
            $user->setinfo('signature', '');
            $user->setinfo('contact', '');
        }
    } else {
        // 多个用户有问题，再次二分查找
        binarySearch($userList);
    }
}
