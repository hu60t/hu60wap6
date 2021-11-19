<?php
$project = strtolower(str::word($PAGE->ext[0]));

$db = new db;
$rs = $db->select('url', 'lutris_release', 'WHERE project=? ORDER BY ctime DESC LIMIT 1', $project);
$url = $rs->fetch(PDO::FETCH_COLUMN, 0);

if (empty($url)) {
    header('HTTP/1.1 404 Not Found');
} else {
    Header('Location: '.$url);
}
