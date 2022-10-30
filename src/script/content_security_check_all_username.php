<?php
if ('cli' != php_sapi_name()) {
    die('run in shell: php content_security_check_all_username.php [min-user-id]');
}

$minUserId = (int)$argv[1];

include '../config.inc.php';
$ENABLE_CC_BLOCKING = false;

$db = new db;
db::$DEFAULT_ERRMODE = PDO::ERRMODE_EXCEPTION;

$userList = $db->query('SELECT uid,name FROM '.DB_A.'user WHERE uid >= ?', $minUserId)->fetchAll(db::ass);

binarySearch($userList);

// 二分法查找有问题的用户名
function binarySearch($userList) {
    $all = count($userList);
    $half = round($all / 2);

    // 左支
    $checked = [];
    $content = "";
    for ($i=0; $i<$half; $i++) {
        $uinfo = $userList[$i];
        $newContent = "${content}${uinfo['name']}\n";

        if (mb_strlen($newContent) > 10000) {
            auditUsers($checked, $content);
            $checked = [];
            $newContent = "${uinfo['name']}\n";
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
        $uinfo = $userList[$i];
        $newContent = "${content}${uinfo['name']}\n";

        if (mb_strlen($newContent) > 10000) {
            auditUsers($checked, $content);
            $checked = [];
            $newContent = "${uinfo['name']}\n";
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
        ContentSecurity::TYPE_NAME,
        $content,
        "user/name"
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

    if ($result['stat'] === ContentSecurity::STAT_PASS || count($userList) == 1) {
        // 全部没问题或只有一个用户有问题
        foreach ($userList as $uinfo) {
            echo "$uinfo[uid]\t$uinfo[name]\t$result[reason]\t$result[rate]%\n";
        }
    } else {
        // 多个用户有问题，再次二分查找
        binarySearch($userList);
    }
}
