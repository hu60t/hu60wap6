<?php
//加载类文件
function autoload_file($classname)
{
    $path = CLASS_DIR . '/' . strtolower($classname) . '.php';
    if (is_file($path)){
       require_once($path);
       return ;
    }
    // FIXME: 我认为考虑到阅读性，不应该强制使用小写文件名，在调用类是区分大小写是一种很好的习惯
    $path = CLASS_DIR . '/' . $classname . '.php';
    if (is_file($path)) require_once($path);
}
