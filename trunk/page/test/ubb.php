<?php
header('Content-Type: text/plain; charset=utf-8');
$ubb=new ubbParser;
var_dump($data=$ubb->parse(
<<<UBBTEXT
大地母亲[url]g.cn[/url]大地母亲《链接：m.php，啊》大地母亲
UBBTEXT
));

$dis=new ubbDisplay();
var_dump($dis->display($data));
echo "\n用时:\n",microtime(true)-$_SERVER['REQUEST_TIME_FLOAT'];