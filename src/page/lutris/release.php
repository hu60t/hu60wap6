<?php
ob_start();
jsonpage::start();

$project = strtolower(str::word($PAGE->ext[0]));

$db = new db;
$rs = $db->select('version,name,url,ctime,mtime', 'lutris_release', 'WHERE project=? ORDER BY ctime DESC', $project);
$rs = $rs->fetchAll(db::ass);

$data = [];
foreach ($rs as $v) {
    $ctime = date('c', $v['ctime']);
    $mtime = date('c', $v['mtime']);

    $data[] = [
        'tag_name' => $v['version'],
        'created_at' => $ctime,
        'published_at' => $mtime,
        'assets' => [[
            'name' => $v['name'],
            'browser_download_url' => $v['url'],
            'created_at' => $ctime,
            'updated_at' => $mtime,
        ]],
    ];
}

jsonpage::output($data);

// 修复 lutris 提示“Failed to read content length on response”的问题
$len = ob_get_length();
header('Content-Length: ' . $len);
ob_end_flush();
