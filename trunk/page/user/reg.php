<?php
try {
$user=new user;
$user->reg("atj","jtmwtpgamdjtg",array(array('好朋友','天去洗洗'),array('好友情','是你真'),array('好朋情','我们的。'),));
 } catch(exception $e) {
 var_dump($e);
 }