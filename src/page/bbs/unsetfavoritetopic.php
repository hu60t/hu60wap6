<?php
try {
    $USER->start($tpl);
    $bbs = new bbs($USER);

    //获取帖子id
    $tid = (int)$PAGE->ext[0];

    //获取论坛id
    $fid = $bbs->findTopicForum($tid)[0];

    //读取帖子元信息
    $tMeta = $bbs->topicMeta($tid, 'uid', 'WHERE id=?', $fid);

    if (!$tMeta) {
        throw new bbsException('帖子 id=' . $tid . ' 不存在！', 2404);
    }

    if (!$bbs->isFavoriteTopic($tid)) {
        throw new bbsException('帖子 id=' . $tid . ' 还未收藏，无法取消！', 2405);
    }

    //帖子收藏权限检查
    $bbs->canFavorite($tMeta->uid);

    //取消收藏操作
    $bbs->unsetFavoriteTopic($tid);
    echo json_encode(array('success'=>true));
} catch (Exception $err) {
   echo json_encode(array('success'=>false,'notice'=>$err->getMessage()));
}
