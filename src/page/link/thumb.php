<?php
// 作者：幻阳化翼
// 来自：https://hu60.net/q.php/bbs.topic.47473.html

$img=$_GET['img'];
$size=getimagesize($img);
$src_w=$size[0];
$src_h=$size[1];
$w=($_GET['w']<$src_w)?$_GET['w']:$src_w;
$h=($_GET['h']<$src_h)?$_GET['h']:$src_h;
if($h<=0)
	$h=$src_h*($w/$src_w);
elseif($w<=0)
	$w=$src_w*($h/$src_h);
	$new_image=imagecreatetruecolor($w,$h);
if($size[2]==1)
{
	$src_image=imagecreatefromgif($img);
	imagecopyresampled($new_image,$src_image,0,0,0,0,$w,$h,$src_w,$src_h);
    header('Content-Type: image/gif');
	imagegif($new_image,null);
}
elseif($size[2]==2)
{
	$src_image=imagecreatefromjpeg($img);
	imagecopyresampled($new_image,$src_image,0,0,0,0,$w,$h,$src_w,$src_h);
    header('Content-Type: image/jpeg');
	imagejpeg($new_image,null,80);
}
else
{
	$src_image=imagecreatefrompng($img);
	imagecopyresampled($new_image,$src_image,0,0,0,0,$w,$h,$src_w,$src_h);
    header('Content-type: image/png'); 
	imagepng($new_image,null);
}
imagedestroy($new_image);
imagedestroy($src_image);

