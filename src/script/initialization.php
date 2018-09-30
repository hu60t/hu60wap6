<?php
if ('cli' != php_sapi_name()) {
    die('run in shell: php import_bk.php');
}
chdir(dirname(__DIR__));
copy('config/db.default.php','config/db.php');
copy('config/security.default.php','config/security.php');
copy('config/site.default.php','config/site.php');
copy('config/system.default.php','config/system.php');

