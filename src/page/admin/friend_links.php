<?php
$tpl = $PAGE->start();
$USER->start($tpl);
$db = new db;

$uinfo = new UserInfo;
$tpl->assign('uinfo', $uinfo);

if (!$USER->islogin || $USER->uid != 1) {
    die('403 Forbidden');
}

switch ($PAGE->ext[0]) {
    case 'edit':
        if (isset($_POST['save'])) {
            $oldId = (int)$_GET['id'];
            $newId = (int)$_POST['new_id'];
            $uid = (int)$_POST['uid'];
            $url = trim($_POST['url']);
            $name = trim($_POST['name']);

            $db->query(
                "UPDATE ".DB_A."friend_links SET id=?, url=?, name=?, uid=? WHERE id=?",
                $newId, $url, $name, $uid, $oldId
            );
            header("Location: admin.friend_links.$bid");
        } else {
            $array = $db->query(
                "SELECT * FROM ".DB_A."friend_links WHERE id=?", (int)$_GET['id']
            )->fetch();
            $tpl->assign('link', $array);
            $tpl->display('tpl:friend_links_edit');
        }
    break;

    case 'add':
        $uid = (int)$_POST['uid'];
        $url = trim($_POST['url']);
        $name = trim($_POST['name']);

        $db->query(
            "INSERT INTO ".DB_A."friend_links(url, name, uid) VALUES(?, ?, ?)",
            $url, $name, $uid
        );
        header("Location: admin.friend_links.$bid");
    break;

    case 'del':
        if (isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $db->query(
                "DELETE FROM ".DB_A."friend_links WHERE id=?",
                $id
            );
        }
        header("Location: admin.friend_links.$bid");
    break;

    default:
        $array = $db->query(
            "SELECT * FROM ".DB_A."friend_links"
        )->fetchAll();
        $tpl->assign('friend_links', $array);
        $tpl->display('tpl:friend_links');
    break;
}
