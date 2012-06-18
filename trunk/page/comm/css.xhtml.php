<?php
try {
$tpl=$PAGE->start();
@ini_set('default_charset',NULL);
header('Content-type: text/css');
$tpl->display('tpl:comm.css.xhtml');
} catch(exception $ERR) {
throw new $ERR;
 }
