<?php
header('Content-Type: text/html; charset=utf-8');
$ubb=new ubbParser;
var_dump($data=$ubb->parse(
<<<UBBTEXT
大地母亲[url]g.cn[/url]大地母亲《链接：m.php，啊》大地母亲

《图片：g.cn/a.jpg》
[img=a.jpg]b[/img]
《缩略图：240*320，g.cn》
UBBTEXT
, true));

$dis=new ubbDisplay();
var_dump($dis->display($data, true));
echo "\n用时:\n",microtime(true)-$_SERVER['REQUEST_TIME_FLOAT'];