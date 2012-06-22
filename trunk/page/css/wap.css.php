<?php
try {
$tpl=$PAGE->start();
@ini_set('default_charset',NULL);
header('Content-type: text/css');
$css=str::word($PAGE->ext[0],true);
if($css=='') $css='default';
$tpl->display($x='tpl:css.wap_'.$css.'.css');
} catch(exception $ERR) {
throw $ERR;
 }
