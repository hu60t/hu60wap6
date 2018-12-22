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
        'follow' => UserRelationshipService::RELATIONSHIP_TYPE_FOLLOW ,
        'block' => UserRelationshipService::RELATIONSHIP_TYPE_BLOCK
    ];

    $type = $PAGE->ext[0];
    $page = $_GET['page'] ? $_GET['page'] : 1;
    // type不合法时自动跳转到关注列表
    if(! array_key_exists($type, $availableTypeList)) {
        header('Location: user.relationship.follow.'. $PAGE->bid);
        exit;
    }

    $userRelationshipService = new UserRelationshipService($USER);
    $count = $userRelationshipService->countTargetUidByType($availableTypeList[$type]);
    $pageSize = 10;
    $totalPage = ceil($count / $pageSize);

    // 修正错误的页码
    if($page < 1 || ($totalPage > 0 && $page > $totalPage)) {
        header('Location: user.relationship.'. $type .'.'. $PAGE->bid);
        exit;
    }

    $offset = ($page - 1) * $pageSize;
    $targetUidList = $userRelationshipService->getTargetUidByType($availableTypeList[$type], $offset, $pageSize);
    $userList = [];

    foreach ($targetUidList as $item)  {
        $userInfo = new userinfo();
        if($userInfo->uid($item['target_uid'])) {
            $userList[] = $userInfo;
        }
    }

    $title = $type == 'follow' ? '关注列表' : '黑名单列表';
    $tpl->assign('title', $title);
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
        ]
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





