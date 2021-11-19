<?php
$tpl = $PAGE->start();
$USER->start($tpl);
if (!$USER->islogin || $USER->uid != 1) {
    die('403 Forbidden');
}

$db = new db;

if (isset($_POST['urls'])) {
    $query = $db->prepare('SELECT url FROM '.DB_A.'lutris_release WHERE project=? AND version=?');
    $update = $db->prepare('INSERT INTO '.DB_A.'lutris_release(project,version,name,url,ctime,mtime) VALUES(?,?,?,?,?,?) ON DUPLICATE KEY UPDATE name=VALUES(name), url=VALUES(url), mtime=VALUES(mtime)');

    $urls = explode("\n", $_POST['urls']);

    $time = time();
    foreach ($urls as $url) {
        updateLutrisUrl($url, $query, $update, $time);
        $time += $_POST['reverse'] ? -1 : 1;
    }
}

$rs = $db->select('url', 'lutris_release', 'ORDER BY project DESC, ctime DESC');
$urls = $rs->fetchAll(PDO::FETCH_COLUMN, 0);

$tpl->assign('urls', $urls);
$tpl->display('tpl:release-edit');

function updateLutrisUrl($url, $query, $update, $time) {
    $url = trim($url);

    if (empty($url)) {
        return;
    }

    // example:
    //   https://example.com/path/to/dxvk-1.9.2L-3e64e1b.tar.gz
    //   https://example.com/path/to/dxvk-nvapi-v0.5.tar.xz
    preg_match('#/(([a-z0-9._-]+)-(v[a-z0-9._-]+)\.tar\.[a-z0-9]+)$#is', $url, $arr) || preg_match('#/(([a-z0-9._]+)-([a-z0-9._-]+)(?:\.tar)?\.[a-z0-9]+)$#is', $url, $arr);;
    $name = $arr[1];
    $project = $arr[2];
    $version = $arr[3];

    if ($version[0] !== 'v') {
        $version = 'v'.$version;
    }

    $query->execute([$project, $version]);
    $oldUrl = $query->fetch(PDO::FETCH_COLUMN, 0);

    if ($oldUrl === $url) {
        return;
    }

    $update->execute([$project, $version, $name, $url, $time, $time]);
}
