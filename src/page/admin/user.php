<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-5-2
 * Time: 下午10:16
 */

$tpl = $PAGE->start();
$USER->start($tpl);
if (!$USER->islogin || $USER->uid != 1)
    die('403 Forbidden');
//TODO: 由于对于类不是很熟悉 先把SQL写在这里
$db = new db;
$t_user = DB_A.'user';
$t_topic = DB_A.'bbs_topic_meta';
$p = (isset($_GET["p"]) && intval($_GET["p"]) > 0 ? intval($_GET["p"]):1) - 1;
$pagesize = 100;
$start = (int)($p*$pagesize);
$sum = $db->query("SELECT COUNT(*) AS `sum` FROM $t_user");
$n = $sum->fetch()["sum"];
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

$res = $db->query(
    "SELECT (SELECT COUNT(*) FROM `$t_topic` WHERE `$t_topic`.uid = `$t_user`.uid ) AS topic_sum,`$t_user`.* FROM `$t_user` ORDER BY $sql_order LIMIT $start, $pagesize"
);

$tpl->assign("users",$res->fetchAll());
$tpl->assign("order",$order);
$tpl->assign("page",jhinfunc::PagerBulma($p + 1,ceil($n/$pagesize),"admin.user.{$bid}?p=##"));
$tpl->display('tpl:user');
