<?php
$url = code::b64d($_GET['url64']);
//echo $url;
header('Location: '.$url);
