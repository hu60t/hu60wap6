<?php
$tpl = $PAGE->start();
$tpl->assign('SITE_URL_PREFIX', SITE_URL_PREFIX);
$tpl->assign('SITE_REG_ENABLE', SITE_REG_ENABLE);
$tpl->display('tpl:info');
