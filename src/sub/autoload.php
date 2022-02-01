<?php
//自动加载类的函数
function autoload_file($classname)
{
    $path = CLASS_DIR . '/' . strtolower($classname) . '.php';
    if (is_file($path)) {
       require_once($path);
       return ;
    }
    // FIXME: 我认为考虑到可读性，不应该强制使用小写文件名，在调用类时区分大小写是一种很好的习惯
    $path = CLASS_DIR . '/' . $classname . '.php';
    if (is_file($path)) require_once($path);
}

/*注册自动加载类的函数*/
spl_autoload_register('autoload_file');


/*处理GET、POST、COOKIE等被加上的反斜杠*/
//PHP5.4+不再需要
//require_once SUB_DIR . '/strip_quotes_gpc.php';

/*更正$_SERVER[PHP_SELF]*/
require_once SUB_DIR . '/correct_php_self.php';

/*让 PHP<5.5 支持 array_column 函数*/
//虎绿林已放弃PHP5兼容性，不再需要
//require_once FUNC_DIR . '/array_column.php';


/*载入其他配置文件*/
require_once CONFIG_DIR . '/system.php';
require_once CONFIG_DIR . '/db.php';
require_once CONFIG_DIR . '/security.php';
require_once CONFIG_DIR . '/site.php';
