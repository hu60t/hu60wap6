<?php
try {
  $tpl = $PAGE->start();
  $USER->start($tpl);
  $bbs = new bbs($USER);

  if (!$USER->islogin) {
    $USER->gotoLogin(true);
  }

  //获取页码
  $p = (int)$_GET['p'];
  if ($p < 1) $p = 1;
  $tpl->assign('p', $p);

  $size = 20;
  $offset = ($p - 1) * $size;

  //获取帖子列表
  $result = $bbs->favoriteTopicList($offset, $size, $count);
  $maxP = ceil($count / $size);

  foreach ($result as $v) {
    $topic = $bbs->topicMeta($v['id'], '*');

    // 偶尔会有回复内容存在但是主题帖丢失的情况
    if (empty($topic)) {
      continue;
    }

    $forum = $bbs->forumMeta($topic['forum_id'], 'name');
    $topic['forum_name'] = $forum['name'];
    $topic['reply_count'] = $bbs->topicContentCount($v['id']) - 1;

    $topic['uinfo'] = new userinfo();
    $topic['uinfo']->uid($topic['uid']);

    $topicList[] = $topic;
  }

  // 列表整个为空时跳转到上一页或最大页
  // 避免搜索结果为空时循环重定向
  if (empty($topicList) && $p > 1) {
    $u = '?p='.min($p-1, $maxP);
    header('Location: '.$u);
    die;
  }

  $tpl->assign('topicList', $topicList);
  $tpl->assign('count', $count);
  $tpl->assign('maxP', $maxP);
  $tpl->display('tpl:myfavorite');

} catch (Exception $err) {
    $tpl->assign('err', $err);
    $tpl->display('tpl:myfavorite');
}

/*
if ($keywords != '' || $username != '') {

    //获取帖子列表
    $result = $search->searchTopic($keywords, $username, $offset, $size, $count);
    $maxP = ceil($count / $size);
    $topicList = [];

    foreach ($result as $v) {
        $topic = $bbs->topicMeta($v['tid'], '*');

        // 偶尔会有回复内容存在但是主题帖丢失的情况
        if (empty($topic)) {
            continue;
        }

        $forum = $bbs->forumMeta($topic['forum_id'], 'name');
        $topic['forum_name'] = $forum['name'];
        $topic['reply_count'] = $bbs->topicContentCount($v['tid']) - 1;

        $topic['uinfo'] = new userinfo();
        $topic['uinfo']->uid($topic['uid']);

        $topicList[] = $topic;
    }

    // 列表整个为空时跳转到上一页或最大页
    // 避免搜索结果为空时循环重定向
    if (empty($topicList) && $p > 1) {
        $u = '?keywords='.urlencode($keywords).'&username='.urlencode($username).'&p='.min($p-1, $maxP);
        header('Location: '.$u);
        die;
    }

    $tpl->assign('topicList', $topicList);
    $tpl->assign('count', $count);
    $tpl->assign('maxP', $maxP);
}
else {
    $tpl->assign('count', 0);
}
 */

