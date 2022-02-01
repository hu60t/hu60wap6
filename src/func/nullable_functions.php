<?php
// 修复 PHP 8.1 中的 Passing null to parameter #1 ($string) of type string is deprecated 问题

function urlenc($url) {
    return urlencode((string)$url);
}

function urldec($url) {
    return urldecode((string)$url);
}
