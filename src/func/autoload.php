<?php
//加载类文件
function autoload_file($classname)
{
    $path = CLASS_DIR . '/' . strtolower($classname) . '.php';
    if (is_file($path)) require_once($path);
}
