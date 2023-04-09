<?php
jsonpage::start();

if (!isset($_GET['addr'])) {
    $ip = $_SERVER['REMOTE_ADDR'];
} else {
    $ip = preg_replace('/[^0-9.a-f:]/i', '', $_GET['addr']);
}

$data = [
    'ip' => $ip,
    'location' => quip($ip),
];

jsonpage::output($data);

function quip($ip) {
    $ipLocation = new IpLocation();
    return $ipLocation->getLocationString($ip);
}
