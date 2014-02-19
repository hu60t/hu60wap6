<?php
$tpl = $PAGE->start();
$USER->start($tpl);
$bbs = new bbs($USER);
var_dump($bbs->newTopic('1,3,5', '德鲁伊试炼', '正在进行中……'));