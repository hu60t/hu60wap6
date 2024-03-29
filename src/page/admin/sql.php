<?php
$tpl = $PAGE->start();
$USER->start($tpl);
if (!$USER->isSiteAdmin())
    die('403 Forbidden');
$sql = trim(str_replace(array("\x0C", "\xC2\xA0"), array("\n", ' '), (string)$_POST['sql']));
if ($sql != '') {
    $db2 = $db = db::conn(false);
    $db = $db->query($sql);
    if (!$db) {
        $tpl->assign('msg', $db2->errorinfo());
        $db = array();
        $ok = false;
    } else {
        $ok = true;
        $db = $db->fetchall(db::ass);
    }
    $tpl->assign('sql', $sql);
    $tpl->assign('db', $db);
    $tpl->assign('ok', $ok);
} else {
    $tpl->assign('sql', '');
    $tpl->assign('db', array());
    $tpl->assign('ok', null);
}
$tpl->display('tpl:sql');
