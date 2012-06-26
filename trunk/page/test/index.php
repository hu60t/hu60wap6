<?php
$tpl=$PAGE->start();
$USER->start($tpl);
$id=str::word($PAGE->ext[0],true);
$tpl->display("tpl:$id");
