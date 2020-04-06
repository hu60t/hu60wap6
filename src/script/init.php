<?php
if ('cli' != php_sapi_name()) {
    die('run in shell: php initialization.php [-f]');
}

$forceCopy = isset($argv[1]) && $argv[1]=='-f';
if ($forceCopy) {
    echo "force copy enabled\n";
}

# php
chdir(dirname(__DIR__));
copyConf('config.inc', 'php');

chdir(dirname(__DIR__).'/config');
copyConf('db',       'php');
copyConf('security', 'php');
copyConf('site',     'php');
copyConf('system',   'php');

# tpl
chdir(dirname(__DIR__).'/config/tpl');
copyConf('site_info',         'conf');

# 拷贝配置文件
function copyConf($name, $type) {
    global $forceCopy;

    $ori = "$name.default.$type";
    $new = "$name.$type";
    
    if (!$forceCopy && file_exists($new)) {
        echo "skip $new\t\t(file exists)\n";
        return true;
    }
    
    echo "copy $new\n";
    return copy($ori, $new);
}

