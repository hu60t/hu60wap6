<?php
jsonpage::start();

try {
    $faces = glob(ROOT_DIR.'/img/face/*.gif');
	$faceList = [];
	foreach ($faces as $f) {
		$url = page::getFileUrl($f);
		$name = explode('.', basename($f));
		$faceList[hex2bin($name[0])] = $url;
	}
    jsonpage::output([
	    'faceList' => $faceList,
		'success'=>true
	]);
}
catch (Exception $e) {
	jsonpage::output([
		'success'=>false,
		'errmsg'=>$e->getMessage()
	]);
}
