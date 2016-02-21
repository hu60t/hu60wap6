<?php
$url = trim(code::b64d($_GET['url64']));
$css = str::word($PAGE->ext[0]);
$cssPath = $PAGE->getTplUrl("css/$css.css");

if (false !== $cssPath) {
	$PAGE->setCookie('css_'.$PAGE->tpl, $css, 3600*24*3650);
}

if ($url == '') {
	$url = 'index.index.'.$PAGE->bid;
}

header('Location: '.$url);
