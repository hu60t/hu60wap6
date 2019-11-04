<?php
// 防止URL中的sid泄露给外链站点
header('Referrer-Policy: origin-when-cross-origin');

// 作者：幻阳化翼
// 来自：https://hu60.net/q.php/bbs.topic.47473.html
// 修改：老虎会游泳

$w = (int)$PAGE->ext[0];
$h = (int)$PAGE->ext[1];
$img = hex2bin($PAGE->ext[2]);

if (!preg_match('#^https?://#is', $img)) {
    header('HTTP/1.1 403 Forbidden');
    die('<h1>403 Forbidden</h1>');
}

$size = getimagesize($img);
$src_w = $size[0];
$src_h = $size[1];
$type  = $size[2];

$w = $w < $src_w ? $w : $src_w;
$h = $h < $src_h ? $h : $src_h;

if ($h <= 0) {
    $h = $src_h * ($w / $src_w);
}
elseif ($w <= 0) {
    $w = $src_w * ($h / $src_h);
}

$src_image = imagecreatefromstring(file_get_contents($img));
$new_image = imagecreatetruecolor($w, $h);
imagecopyresampled($new_image, $src_image, 0, 0, 0, 0, $w, $h, $src_w, $src_h);

if ($type == IMAGETYPE_GIF) {
    header('Content-Type: image/gif');
    imagegif($new_image, null);
}
elseif ($type == IMAGETYPE_JPEG) {
    header('Content-Type: image/jpeg');
    imagejpeg($new_image, null, 80);
}
else {
    header('Content-type: image/png');
    imagepng($new_image, null);
}

imagedestroy($new_image);
imagedestroy($src_image);

