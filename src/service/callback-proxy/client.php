<?php
use Amp\Websocket;
use Amp\Websocket\Client;

if ('cli' != php_sapi_name() || $argc < 3) {
    die('run in shell: php callback-proxy/client.php ws://hu60.cn/callback-proxy/client/<token> http://127.0.0.1:8080/q.php/api.wechat.json');
}

require_once __DIR__ . '/../../composer/vendor/autoload.php';

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// 禁用防CC模块，避免产生警告
$ENABLE_CC_BLOCKING = false;

// 远程websocket服务路径
$remotePath = trim($argv[1]);

// 转发到的本地路径
$localPath = trim($argv[2]);

// websocket客户端
Amp\Loop::run(function () use ($remotePath, $localPath) {
    /** @var Client\Connection $connection */
    $connection = yield Client\connect($remotePath);
    yield $connection->send('Hello!');

    /** @var Websocket\Message $message */
    while ($message = yield $connection->receive()) {
        $payload = yield $message->buffer();

        printf("Received: %s\n", $payload);
    }
});
