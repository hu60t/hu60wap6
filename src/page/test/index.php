<?php
$tpl=$PAGE->start();
$id=str::word($PAGE->ext[0],true);
$tpl->display("tpl:$id");
