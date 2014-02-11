<?php
$tpl = $PAGE->start();
$USER->start($tpl);
$bbs = new bbs($USER);
var_dump($bbs->newTopic('6,7,8', '德鲁伊试炼', '正在进行中……'));