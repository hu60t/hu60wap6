<?php
$PAGE->START();
$USER->START();
$u=$USER;
$a=5;
$b=&$a;
var_dump($a==$b,$a===$b);
if(!$u->islogin) $u->loginbysid('UuGYvGMQc179wjisdSMp8IAQAAAA');
var_dump(
$_COOKIE,
$PAGE->sid,
date('Y-m-d H:i:s',$u->regtime),
$u->getinfo('input.istextarea'),
$u->name,
$u->uid,
$u->sid,
$u->setcookie()
);
