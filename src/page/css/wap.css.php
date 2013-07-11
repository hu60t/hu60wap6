<?php
try {
$tpl=$PAGE->start();
@ini_set('default_charset',NULL);
header('Content-type: text/css');
$css=str::word($PAGE->ext[0],true);
if($css!='') {
 setCookie(COOKIE_A.'page_css',$css,$_SERVER['REQUEST_TIME']+DEFAULT_LOGIN_TIMEOUT,COOKIE_PATH,COOKIE_DOMAIN);
 $USER->start($tpl);
 if($USER->uid && $USER->islogin) {
   $USER->setinfo('page.css',$css);
  }
} else {
 $css=$_COOKIE[COOKIE_A.'page_css'];
 if($css=='') {
  $USER->start($tpl);
  if($USER->uid) $css=$USER->getinfo('page.css');
 }
}
 if($css=='') $css='default';
$tpl->display($x='tpl:css.wap_'.$css.'.css');
} catch(exception $ERR) {
throw $ERR;
 }
