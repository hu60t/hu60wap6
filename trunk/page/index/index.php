<?php
$tpl=$PAGE->start();
$USER->start($tpl);
//$tpl->force_compile=true;
$tpl->display('tpl:index');
