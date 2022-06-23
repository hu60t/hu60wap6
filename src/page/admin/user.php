<?php
$tpl = $PAGE->start();
$USER->start($tpl);
if (!$USER->isSiteAdmin())
    die('403 Forbidden');
//TODO: 由于对于类不是很熟悉 先把SQL写在这里
$db = new db;
$t_user = DB_A.'user';
$t_topic = DB_A.'bbs_topic_meta';
$p = (isset($_GET["p"]) && intval($_GET["p"]) > 0 ? intval($_GET["p"]):1) - 1;
$pagesize = 100;
$start = (int)($p*$pagesize);
$order = isset($_GET["order"]) && intval($_GET["order"]) < 6?intval($_GET["order"]):0;
switch ($order){
    case 0:
        $sql_order = "uid";
        break;
    case 1:
        $sql_order = "uid DESC";
        break;
    case 2:
        $sql_order = "acctime";
        break;

    case 3:
        $sql_order = "acctime DESC";
        break;

    case 4:
        $sql_order = "topic_sum";
        break;

    case 5:
        $sql_order = "topic_sum DESC";
        break;
    default:
        $sql_order = "uid";
}

$data = [];
$sql_where = '';

if (!empty($_GET['name'])) {
    $sql_where = 'WHERE name like ?';
    $data[] = "%$_GET[name]%";
}

$data[] = $start;
$data[] = $pagesize;

$sql = "SELECT SQL_CALC_FOUND_ROWS (SELECT COUNT(*) FROM `$t_topic` WHERE `$t_topic`.uid = `$t_user`.uid ) AS topic_sum,`$t_user`.* FROM `$t_user` $sql_where ORDER BY $sql_order LIMIT ?, ?";

$res = $db->query($sql, $data);
$sum = $db->query('SELECT FOUND_ROWS()');
$n = $sum->fetch(db::col, 0);

$tpl->assign("users",$res->fetchAll());
$tpl->assign("order",$order);
$tpl->assign("pager",jhinfunc::PagerBulma($p + 1,ceil($n/$pagesize),"admin.user.{$bid}?p=##"));
$tpl->display('tpl:user');
