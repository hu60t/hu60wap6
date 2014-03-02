<?php
$tpl = $PAGE->start();
$USER->start($tpl);
$bbs = new bbs($USER);
var_dump($bbs->newTopic('1,3,5', '停服倒计时', '10、9、8、7、6、5、4、3、2、2、2、2、2、2、2、2、2……'));