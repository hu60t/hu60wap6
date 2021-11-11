<?php
function header_to_get($header, $get) {
    if (!isset($_GET[$get]) && isset($_SERVER[$header])) {
        $_GET[$get] = $_SERVER[$header];
    }
}

header_to_get('HTTP_X_ORIGIN', '_origin');
header_to_get('HTTP_X_UINFO', '_uinfo');
header_to_get('HTTP_X_JSON', '_json');
header_to_get('HTTP_X_CONTENT', '_content');
header_to_get('HTTP_X_MYSELF', '_myself');
header_to_get('HTTP_X_SID', '_sid');
header_to_get('HTTP_X_TOPIC_SUMMARY', '_topic_summary');
header_to_get('HTTP_X_FLOOR_REVERSE', 'floorReverse');
header_to_get('HTTP_X_PAGE_SIZE', 'pageSize');

$_GET += $_POST;

if (isset($_GET['_cid']) && isset($_GET['_pid']) && isset($_GET['_bid'])) {
	$_arr = [$_GET['_cid'], $_GET['_pid']];
	if (isset($_GET['_ext'])) {
		if (is_array($_GET['_ext'])) {
			$_arr = array_merge($_arr, $_GET['_ext']);
		} else {
			$_arr[] = $_GET['_ext'];
		}
	}
	$_arr[] = $_GET['_bid'];
	$_SERVER['PATH_INFO'] = '/'.implode('.', $_arr);
	$_SERVER['DOCUMENT_URI'] = $_SERVER['SCRIPT_NAME'].$_SERVER['PATH_INFO'];
	$_SERVER['REQUEST_URI'] = $_SERVER['DOCUMENT_URI'];
	if (!empty($_SERVER['QUERY_STRING'])) {
		$_SERVER['REQUEST_URI'] .= '?'.$_SERVER['QUERY_STRING'];
	}
	unset($_arr);
}

