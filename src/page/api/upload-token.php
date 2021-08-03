<?php
header('Content-Type: application/json');

$key = null;
if (isset($_GET['key'])) {
    $key = $_GET['key'];
} elseif (isset($_POST['key'])) {
    $key = $_POST['key'];
}

echo json_encode(CloudStorage::getInstance()->getUploadToken($key));
