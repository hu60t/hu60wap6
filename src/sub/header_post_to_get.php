<?php
function header_to_get($header, $get) {
    if (!isset($_GET[$get]) && isset($_SERVER[$header])) {
        $_GET[$get] = $_SERVER[$header];
    }
}
function post_to_get($name) {
    if (!isset($_GET[$name]) && isset($_POST[$name])) {
        $_GET[$name] = $_POST[$name];
    }
}

header_to_get('HTTP_X_ORIGIN', '_origin');
post_to_get('_origin');

header_to_get('HTTP_X_UINFO', '_uinfo');
post_to_get('_uinfo');

header_to_get('HTTP_X_JSON', '_json');
post_to_get('_json');

header_to_get('HTTP_X_CONTENT', '_content');
post_to_get('_content');

header_to_get('HTTP_X_MYSELF', '_myself');
post_to_get('_myself');

header_to_get('HTTP_X_SID', '_sid');
post_to_get('_sid');

header_to_get('HTTP_X_TOPIC_SUMMARY', '_topic_summary');
post_to_get('_topic_summary');
