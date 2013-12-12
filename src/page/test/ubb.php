<?php
header('Content-Type: text/plain; charset=utf-8');
//header('Content-Type: text/html; charset=utf-8');
$serialize = false;
$ubb=new ubbParser;
/*var_dump*/($data=$ubb->parse(
<<<UBBTEXT
大地母亲[url=g.cn]g.cn[img]g.png[/img][/url]大地母亲《链接：m.php，啊》大地母亲
hdfffffffffffffffffhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhyyyyyyyyyyyyyyyyyyyyyyyyyttttttttttttttttttttttttttttttttttttttttttttttttt
《图片：g.cn/a.jpg》
[img=a.jpg]b[/img]
《缩略图：240*320，g.cn》
UBBTEXT
, $serialize));

$dis=new ubbDisplay();
print_r($dis->display($data, 0, 100, $_GET['p']));
echo "\n用时:\n",microtime(true)-$_SERVER['REQUEST_TIME_FLOAT'];