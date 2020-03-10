<?php
foreach ($_SERVER as $k => $v) {
    if (substr($k, 0, 5) == "HTTP_") {
        $k = substr($k, 5);
        echo htmlspecialchars("$k:$v") . "<hr/>";
    }
}
echo "你的IP地址是" . $_SERVER['REMOTE_ADDR'];
?>