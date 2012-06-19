<?php
/*系统设置*/
  
//默认时区设置，asia/shanghai是北京时间（其实是“上海时间”，一样）
date_default_timezone_set('asia/shanghai');
//用户自动掉线时间，单位：秒
define('DEFAULT_LOGIN_TIMEOUT',2592000);
  
//默认页面cid
define('DEFAULT_PAGE_CID','index');
//默认页面pid
define('DEFAULT_PAGE_PID','index');
//默认页面bid
define('DEFAULT_PAGE_BID','xhtml');
//默认页面mime
define('DEFAULT_PAGE_MIME','text/html');
  
//cookie作用路径
define('COOKIE_PATH','/');
//cookie作用域名
define('COOKIE_DOMAIN',$_SERVER['HTTP_HOST']);
//Cookie前缀
define('COOKIE_A','hu60_');
  
//网页gzip压缩等级，9为最高，0关闭

define('PAGE_GZIP',9);
  
/*文件和数据库目录的路径*/
  
//用户文件存放目录绝对路径
define('USERFILE_DIR',ROOT_DIR.'/userfile');
  
//SQLite数据库目录
define('DB_DIR',ROOT_DIR.'/db');
  
//用户密码加密的密钥。第一次使用前建议修改，以后禁止修改（否则所有用户都会密码错误）！
define('USER_PASS_KEY',"M*了\r，a\x02T§\x03天\0¥€没|什\ntj凼=p");
