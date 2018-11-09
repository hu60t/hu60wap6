<?php
$tpl = $PAGE->START();

$result = NULL;
$stat = NULL;
$info = NULL;

if (isset($_GET['action'])) {
    $server = $_GET['server'] ?? null;
    $user = $_GET['user'] ?? null;
    checkAsicBoost($server, $user, $stat, $info);
}

$tpl->assign('stat', $stat);
$tpl->display('tpl:asicboost');


function checkAsicBoost($server, $user, &$stat) {
    $stat = [];

    $server = trim($server);
    $user = trim($user);

    if (empty($server)) {
        $stat[] =[false, "服务器不能为空"];
        return;
    }
    /*if (empty($user)) {
        $stat[] =[false, "子账户名不能为空"];
        return;
    }*/


    // 添加端口
    if (!preg_match('/:\d+$/', $server)) {
        $server .= ':1800';
    }

    $fp = stream_socket_client("tcp://$server", $errno, $error, 5);

    if (!$fp) {
        $stat[] =[false, "连接服务器失败"];
        return;
    }

    stream_set_timeout($fp, 5);

    // ---------------- mining.configure ----------------
    $stratumMessage = '{"id":3,"method":"mining.configure","params":[["version-rolling"],{"version-rolling.mask":"1fffe000","version-rolling.min-bit-count":2}]}';
    fwrite($fp, "$stratumMessage\n");
    $info = stream_get_meta_data($fp);
    if ($info['timed_out']) {
        $stat[] =[false, "向服务器发送数据超时"];
        $stat[] =[null, "错误阶段：mining.configure"];
        return;
    }

    $versionRolling = stream_get_line($fp, 65535, "\n");
    $info = stream_get_meta_data($fp);
    if ($info['timed_out']) {
        $stat[] =[false, "服务器不支持ASICBoost"];
        $stat[] =[null, "原因：对ASICBoost请求无响应"];
        return;
    }

    $versionRollingArr = json_decode($versionRolling, true);
    if (!$versionRollingArr || !isset($versionRollingArr['result']) || !isset($versionRollingArr['result']['version-rolling.mask'])) {
        $stat[] =[false, "服务器不支持ASICBoost"];
        $stat[] =[null, "原因：缺少 version-rolling 响应"];
        return;
    }

    // ---------------- mining.subscribe ----------------
    $stratumMessage = '{"id":1,"method":"mining.subscribe","params":[]}';
    fwrite($fp, "$stratumMessage\n");
    $info = stream_get_meta_data($fp);
    if ($info['timed_out']) {
        $stat[] =[false, "向服务器发送数据超时"];
        $stat[] =[null, "错误阶段：mining.subscribe"];
        return;
    }

    $subscribe = stream_get_line($fp, 65535, "\n");
    $info = stream_get_meta_data($fp);
    if ($info['timed_out']) {
        $stat[] =[false, "接收服务器响应超时"];
        $stat[] =[null, "错误阶段：mining.subscribe"];
        return;
    }

    $subscribeArr = json_decode($subscribe, true);
    if (!$subscribeArr || null !== $subscribeArr['error']) {
        $stat[] =[false, "向服务器订阅任务失败"];
        $stat[] =[null, "错误信息：".getStratumError($subscribe)];
        return;
    }

    // 某些服务器会在mining.configure完成后立即发送mining.set_version_mask
    if ('mining.set_version_mask' == $subscribeArr['method']) {
        $versionMask = $subscribeArr['params'][0];
        if (hexdec($versionMask) == 0) {
            $stat[] =[false, "服务器不支持ASICBoost"];
            $stat[] =[null, "错误信息：允许的 version mask 为空 ($versionMask)"];
            return;
        }
        $stat[] =[true, "服务器支持ASICBoost"];
        $stat[] =[null, "允许的 version mask 为 $versionMask"];
        return true;
    }

    // ---------------- mining.authorize ----------------
    $stratumMessage = '{"id":2,"method":"mining.authorize","params":['.json_encode($user).']}';
    $stratumMessage .= "\n".'{"id":5,"method":"agent.get_capabilities","params":[["verrol"]]}';
    fwrite($fp, "$stratumMessage\n");
    $info = stream_get_meta_data($fp);
    if ($info['timed_out']) {
        $stat[] =[false, "向服务器发送数据超时"];
        $stat[] =[null, "错误阶段：mining.authorize"];
        return;
    }

    $authorize = stream_get_line($fp, 65535, "\n");
    $info = stream_get_meta_data($fp);
    if ($info['timed_out']) {
        $stat[] =[false, "接收服务器响应超时"];
        $stat[] =[null, "错误阶段：mining.authorize"];
        return;
    }

    $authorizeArr = json_decode($authorize, true);
    if (!$authorizeArr || null !== $authorizeArr['error']) {
        $stat[] =[false, "认证失败"];
        $stat[] =[null, "错误信息：".getStratumError($authorize)];
        return;
    }

    $versionRolling = stream_get_line($fp, 65535, "\n");
    $info = stream_get_meta_data($fp);
    $versionRollingArr = json_decode($versionRolling, true);
    if (!$versionRollingArr || 'mining.set_version_mask' != $versionRollingArr['method']) {
        $stat[] =[false, "服务器不支持ASICBoost"];
        $stat[] =[null, "原因：缺少 mining.set_version_mask 通知"];
        return;
    }

    $versionMask = $versionRollingArr['params'][0];

    if (hexdec($versionMask) == 0) {
        $stat[] =[false, "服务器不支持ASICBoost"];
        $stat[] =[null, "错误信息：允许的 version mask 为空 ($versionMask)"];
        return;
    }

    $stat[] =[true, "服务器支持ASICBoost"];
    $stat[] =[null, "允许的 version mask 为 $versionMask"];

    $setDiff = stream_get_line($fp, 65535, "\n");
    $notify = stream_get_line($fp, 65535, "\n");
    $serverCaps = stream_get_line($fp, 65535, "\n");
    $info = stream_get_meta_data($fp);
    if ($info['timed_out']) {
        $stat[] = [false, "服务器不支持智能代理的ASICBoost"];
    }
    else {
        $serverCapsArr = json_decode($serverCaps, true);
        if ($serverCaps && isset($serverCapsArr['result']['capabilities']) && in_array('verrol', $serverCapsArr['result']['capabilities'])) {
            $stat[] = [true, "服务器支持智能代理的ASICBoost"];
        }
        else {
            $stat[] = [false, "服务器不支持智能代理的ASICBoost"];
        }
    }

    return;
}

function getStratumError($json) {
    static $L = [
        'Invalid Sub-account Name' => '子账户名错误'
    ];

    if (empty($json)) {
        return "服务器未响应";
    }

    $arr = json_decode($json, true);
    if (isset($arr['error'][1])) {
        return L($arr['error'][1]);
    }

    return $json;
}

function L($str) {
    static $L = [
        'Invalid Sub-account Name' => '子账户名错误',
        "Worker Name Cannot Start with '.'" => '子账户名不能为空',
    ];
    return $L[$str] ?? $str;
}
