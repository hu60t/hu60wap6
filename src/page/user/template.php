<?php
$tpl = $PAGE->start();
$USER->start($tpl);

//若未登录，跳转到登录页
$USER->gotoLogin(true);
switch ($_GET['name']) {
  case 'jhin':
    setcookie(COOKIE_A."tpl","jhin", time()+3600*24*7);
    break;

  default:
    setcookie(COOKIE_A."tpl","classic", time()+3600*24*7);
    break;
}
header('Location: '.$_SERVER['HTTP_REFERER']);
