<?php
$tpl = $PAGE->start();

$t_forum = DB_A.'bbs_forum_meta';
$t_topic = DB_A.'bbs_topic_meta';
$t_topic_content = DB_A.'bbs_topic_content';
$t_user = DB_A.'user';
$t_24h = time()-24*3600;
$USER->start($tpl);
if (!$USER->isSiteAdmin())
    die('403 Forbidden');
$db = new db;


$tpl->assign("site",$db->query("SELECT (SELECT COUNT(*) FROM $t_user) AS user_sum,
(SELECT COUNT(*) FROM $t_topic) AS topic_sum,
(SELECT COUNT(*) FROM $t_user WHERE acctime > $t_24h) AS user_24h,
(SELECT COUNT(*) FROM $t_topic_content WHERE reply_id != 0 AND ctime > $t_24h) AS reply_24h,
(SELECT COUNT(*) FROM $t_topic WHERE ctime > $t_24h) AS topic_24h")->fetch());

$tpl->display('tpl:index');
