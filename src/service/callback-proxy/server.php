<?php
use Amp\Http\Status;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Server\Router;
use Amp\Http\Server\RequestHandler\CallableRequestHandler;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Amp\Loop;
use Amp\Promise;
use Amp\Socket\Server;
use Amp\Success;
use Amp\Websocket\Client;
use Amp\Websocket\Message;
use Amp\Websocket\Server\ClientHandler;
use Amp\Websocket\Server\Gateway;
use Amp\Websocket\Server\Websocket;
use Monolog\Logger;
use function Amp\ByteStream\getStdout;
use function Amp\call;

if ('cli' != php_sapi_name() || $argc < 2) {
    die('run in shell: php callback-proxy/server.php <listen-addr>:<listen-port>');
}

require_once __DIR__ . '/../../composer/vendor/autoload.php';

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// 禁用防CC模块，避免产生警告
$ENABLE_CC_BLOCKING = false;

// 监听IP:端口
$listen = trim($argv[1]);

// 日志
$logHandler = new StreamHandler(getStdout());
$logHandler->setFormatter(new ConsoleFormatter);
$logger = new Logger('server');
$logger->pushHandler($logHandler);

// websocket服务器
$websocket = new Websocket(new class implements ClientHandler {
    private const ALLOWED_ORIGINS = [
        // 留空不限制
        /*'http://localhost:1337',
        'http://127.0.0.1:1337',
        'http://[::1]:1337'*/
    ];

    public function handleHandshake(Gateway $gateway, Request $request, Response $response): Promise
    {
        if (!empty(self::ALLOWED_ORIGINS) &&
            !\in_array($request->getHeader('origin'), self::ALLOWED_ORIGINS, true)
        ) {
            return $gateway->getErrorHandler()->handleError(403);
        }
        return new Success($response);
    }

    public function handleClient(Gateway $gateway, Client $client, Request $request, Response $response): Promise
    {
        return call(function () use ($gateway, $client): \Generator {
            while ($message = yield $client->receive()) {
                \assert($message instanceof Message);
                $gateway->broadcast(\sprintf(
                    '%d: %s',
                    $client->getId(),
                    yield $message->buffer()
                ));
            }
        });
    }
});

// http服务器
Loop::run(function () use ($listen, $websocket, $logger): Promise {
    $sockets = [
        Server::listen($listen),
    ];

    $router = new Router;
    $router->addRoute('GET', '/callback-proxy/client/{token}', $websocket);
    $router->addRoute('GET', '/callback-proxy/server/{token}', new CallableRequestHandler(function ($request) {
        //var_dump($request);
        return new Response(Status::OK, ['content-type' => 'text/plain'], 'Hello, world!');
    }));

    $server = new HttpServer($sockets, $router, $logger);

    return $server->start();
});
