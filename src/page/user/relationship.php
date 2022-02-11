<?php
/**
 * Created by PhpStorm.
 * User: banto
 * Date: 2018/12/21
 * Time: 22:44
 */

$tpl = $PAGE->start();
$USER->start($tpl);

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $USER->gotoLogin(true);
    $availableTypeList = [
        'follow' => [UserRelationshipService::RELATIONSHIP_TYPE_FOLLOW, '我关注的', [
            'unfollow' => '取消关注',
        ]],
        'block' => [UserRelationshipService::RELATIONSHIP_TYPE_BLOCK, '我屏蔽的', [
            'unblock' => '取消屏蔽',
        ]],
        'follow_me' => [UserRelationshipService::RELATIONSHIP_TYPE_FOLLOW_ME, '关注我的', [
            'follow' => '也关注Ta',
        ], [
            'unfollow' => '已互相关注',
        ]],
        'block_me' => [UserRelationshipService::RELATIONSHIP_TYPE_BLOCK_ME, '屏蔽我的', [
            'block' => '也屏蔽Ta',
        ], [
            'unblock' => '已互相屏蔽',
        ]],
    ];

    $type = $PAGE->ext[0];
    $page = $_GET['page'] ? $_GET['page'] : 1;
    // type不合法时自动跳转到关注列表
    if(! array_key_exists($type, $availableTypeList)) {
        header('Location: user.relationship.follow.'. $PAGE->bid);
        exit;
    }
    $meta = $availableTypeList[$type];

    $userRelationshipService = new UserRelationshipService($USER);
    $count = $userRelationshipService->countTargetUidByType($meta[0]);
    $pageSize = page::pageSize(1, 20, 1000);
    $totalPage = ceil($count / $pageSize);

    // 修正错误的页码
    if($page < 1 || ($totalPage > 0 && $page > $totalPage)) {
        header('Location: user.relationship.'. $type .'.'. $PAGE->bid);
        exit;
    }

    $offset = ($page - 1) * $pageSize;
    $targetUidList = $userRelationshipService->getTargetUidByType($meta[0], $offset, $pageSize);

    $userList = [];
    foreach ($targetUidList as $uid)  {
        $userInfo = new userinfo();
        if($userInfo->uid($uid)) {
            $userList[] = $userInfo;
        }
    }

    if ($meta[0] > 10) {
        $inverseRelationship = [];
        foreach ($targetUidList as $uid)  {
            $inverseRelationship[$uid] = $userRelationshipService->checkRelationship($USER->uid, $uid, $meta[0] - 10);
        }
        $tpl->assign('inverseRelationship', $inverseRelationship);
        $tpl->assign('inverseActions', $meta[3]);
    }

    $tpl->assign('title', $meta[1]);
    $tpl->assign('actions', $meta[2]);
    $tpl->assign('type', $type);
    $tpl->assign('userList', $userList);
    $tpl->assign('currentPage', $page);
    $tpl->assign('totalPage', $totalPage);
    $tpl->display('tpl:relationship');
}


if($_SERVER['REQUEST_METHOD'] == 'POST') {

    /**
     * 返回操作结果
     * @param $isSuccess
     * @param $message
     */
    function sendApiResponse($isSuccess, $message)
    {
        exit(json_encode([
            'success' => $isSuccess,
            'message' => $message
        ]));
    }

    $action = $_POST['action'];
    $targetUid = $_POST['targetUid'];

    if ($USER->uid == null) {
        sendApiResponse(false, '请先登录后操作');
    }

    if ($targetUid == null || !is_numeric($targetUid)) {
        sendApiResponse(false, '请填写正确的用户ID');
    }

    $availableActionList = [
        'follow' => [
            'targetMethod' => 'follow',
            'actionName' => '关注'
        ],
        'unfollow' => [
            'targetMethod' => 'unfollow',
            'actionName' => '取消关注'
        ],
        'block' => [
            'targetMethod' => 'block',
            'actionName' => '屏蔽'
        ],
        'unblock' => [
            'targetMethod' => 'unblock',
            'actionName' => '取消屏蔽'
        ],
        'hideUserCSS' => [
            'targetMethod' => 'hideUserCSS',
            'actionName' => '屏蔽小尾巴'
        ],
        'showUserCSS' => [
            'targetMethod' => 'showUserCSS',
            'actionName' => '显示小尾巴'
        ],
    ];
    if (! array_key_exists($action, $availableActionList)) {
        sendApiResponse(false, 'action不可用');
    }

    $userRelationshipService = new UserRelationshipService($USER);
    $targetMethod = $availableActionList[$action]['targetMethod'];
    $actionName = $availableActionList[$action]['actionName'];

    if ($userRelationshipService->$targetMethod($targetUid)) {
        sendApiResponse(true, $actionName . '成功');
    } else {
        sendApiResponse(false, $actionName . '失败');
    }
}
