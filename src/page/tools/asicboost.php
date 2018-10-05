<?php
$tpl = $PAGE->START();

$result = NULL;
$stat = NULL;
$info = NULL;

if (isset($_GET['action'])) {
    $server = $_GET['server'] ?? null;
    $user = $_GET['user'] ?? null;
    $result = checkAsicBoost($server, $user, $stat, $info);
}

$tpl->assign('result', $result);
$tpl->assign('stat', $stat);
$tpl->assign('info', $info);
$tpl->display('tpl:asicboost');


function checkAsicBoost($server, $user, &$stat, &$info) {
    $server = trim($server);
    $user = trim($user);
    
    if (empty($server)) {
        $stat ="服务器不能为空";
        return false;
    }
    if (empty($user)) {
        $stat ="子账户名不能为空";
        return false;
    }
    
    
    // 添加端口
    if (!preg_match('/:\d+$/', $server)) {
        $server .= ':1800';
    }
    
    $fp = stream_socket_client("tcp://$server", $errno, $error, 5);

    if (!$fp) {
        $stat ="连接服务器失败";
        return false;
    }
    
    stream_set_timeout($fp, 5);
    
    // ---------------- mining.configure ----------------
    $stratumMessage = '{"id":3,"method":"mining.configure","params":[["version-rolling"],{"version-rolling.mask":"1fffe000","version-rolling.min-bit-count":2}]}';
    fwrite($fp, "$stratumMessage\n");
    $info = stream_get_meta_data($fp);
    if ($info['timed_out']) {
        $stat ="向服务器发送数据超时";
        $info = "错误阶段：mining.configure";
        return false;
    }
    
    $versionRolling = stream_get_line($fp, 65535, "\n");
    $info = stream_get_meta_data($fp);
    if ($info['timed_out']) {
        $stat ="服务器不支持ASICBoost";
        $info = "原因：对ASICBoost请求无响应";
        return false;
    }
    
    $versionRollingArr = json_decode($versionRolling, true);
    if (!$versionRollingArr || !isset($versionRollingArr['result']) || !isset($versionRollingArr['result']['version-rolling.mask'])) {
        $stat ="服务器不支持ASICBoost";
        $info = "原因：缺少 version-rolling 响应";
        return false;
    }
    
    // ---------------- mining.subscribe ----------------
    $stratumMessage = '{"id":1,"method":"mining.subscribe","params":[]}';
    fwrite($fp, "$stratumMessage\n");
    $info = stream_get_meta_data($fp);
    if ($info['timed_out']) {
        $stat ="向服务器发送数据超时";
        $info = "错误阶段：mining.subscribe";
        return false;
    }
    
    $subscribe = stream_get_line($fp, 65535, "\n");
    $info = stream_get_meta_data($fp);
    if ($info['timed_out']) {
        $stat ="接收服务器响应超时";
        $info = "错误阶段：mining.subscribe";
        return false;
    }
    
    $subscribeArr = json_decode($subscribe, true);
    if (!$subscribeArr || null !== $subscribeArr['error']) {
        $stat ="向服务器订阅任务失败";
        $info = "错误信息：".getStratumError($subscribe);
        return false;
    }
    
    // ---------------- mining.authorize ----------------
    $stratumMessage = '{"id":2,"method":"mining.authorize","params":['.json_encode($user).']}';
    fwrite($fp, "$stratumMessage\n");
    $info = stream_get_meta_data($fp);
    if ($info['timed_out']) {
        $stat ="向服务器发送数据超时";
        $info = "错误阶段：mining.authorize";
        return false;
    }
    
    $authorize = stream_get_line($fp, 65535, "\n");
    $info = stream_get_meta_data($fp);
    if ($info['timed_out']) {
        $stat ="接收服务器响应超时";
        $info = "错误阶段：mining.authorize";
        return false;
    }
    
    $authorizeArr = json_decode($authorize, true);
    if (!$authorizeArr || null !== $authorizeArr['error']) {
        $stat ="认证失败";
        $info = "错误信息：".getStratumError($authorize);
        return false;
    }
    
    $versionRolling = stream_get_line($fp, 65535, "\n");
    $info = stream_get_meta_data($fp);
    $versionRollingArr = json_decode($versionRolling, true);
    if (!$versionRollingArr || 'mining.set_version_mask' != $versionRollingArr['method']) {
        $stat ="服务器不支持ASICBoost";
        $info = "原因：缺少 mining.set_version_mask 通知";
        return false;
    }
    
    $versionMask = $versionRollingArr['params'][0];
    
    if (hexdec($versionMask) == 0) {
        $stat ="服务器不支持ASICBoost";
        $info = "错误信息：允许的 version mask 为空 ($versionMask)";
        return false;
    }
    
    $stat ="服务器支持ASICBoost";
    $info = "允许的 version mask 为 $versionMask";
    return true;
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
        'Invalid Sub-account Name' => '子账户名错误'
    ];
    return $L[$str] ?? $str;
}
