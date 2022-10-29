<?php
if ('cli' != php_sapi_name()) {
    die('run in shell: php content_security_test.php');
}

include '../config.inc.php';
$ENABLE_CC_BLOCKING = false;

var_dump(
    ContentSecurity::auditTextBatch(ContentSecurity::TYPE_TOPIC, [
        [
            'user' => UserInfo::getInstanceByUid(1),
            'text' => '测试内容1',
            'contentTag' => 'test1',
        ],
        [
            'user' => UserInfo::getInstanceByName('虎符图腾'),
            'text' => '测试内容2',
            'contentTag' => 'test2',
        ],
        [
            'user' => null,
            'text' => '测试内容3',
            'contentTag' => 'test3',
        ],
    ])
);
