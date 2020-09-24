<?php
jsonpage::start();

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!isset($data['data']) || !isset($data['data']['extra']) || !isset($data['data']['uid'])) {
        throw new Exception('回调数据错误', 400);
    }

    $data = $data['data'];
    $tokenStr = explode(':', $data['extra']);

    $token = new token();
    if (!$token->check($tokenStr[1])) {
        throw new Exception('token不存在或已过期', 404);
    }

    if ($token->data() != $tokenStr[0]) {
        throw new Exception('token与用户不匹配', 403);
    }

    $uid = (int)$token->data();
    $user = new user();
    $user->uid($uid, true);
    $user->virtualLogin();

    $user->setinfo('wechat', $data);
    $token->delete();

    jsonpage::output([
		'success'=>true
	]);
}
catch (Exception $e) {
	jsonpage::output([
		'success'=>false,
		'errmsg'=>$e->getMessage()
	]);
}
