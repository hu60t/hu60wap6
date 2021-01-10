<?php
$tpl = $PAGE->start();
$USER->start($tpl);
$bbs = new bbs($USER);
$search = new search();

//获取页码
$p = (int)$_GET['p'];
if ($p < 1) $p = 1;
$tpl->assign('p', $p);

$size = 20;
$offset = ($p - 1) * $size;

//获取搜索词
$keywords = $_GET['keywords'];
$username = $_GET['username'];
$onlyReview = (bool)$_GET['onlyReview'];
$searchReply = ($_GET['searchType'] == 'reply') || $onlyReview;

if ($keywords == '' && $username == '' && !$onlyReview && !$searchReply) {
  $tpl->assign('count', 0);
  //显示版块列表
  $tpl->display('tpl:searchtopic');
  return;
}

try {
  if(!$searchReply) {
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
    //显示版块列表
    $tpl->display('tpl:searchtopic');
  }
  else {
    $result = $search->searchReply($keywords, $username, $offset, $size, $count, $onlyReview);

    $maxP = ceil($count / $size);
    foreach ($result as &$v) {
        // 回复用户
        $v['uinfo'] = new UserInfo();
        $v['uinfo']->uid($v['uid']);

        // 待审核
        if ($v['review']) {
          $vTid = ($v['floor'] == 0) ? $v['topic_id'] : 0;
          $v['content'] = UbbParser::createPostNeedReviewNotice($USER, $v['uinfo'], $v['id'], $v['content'], $vTid, true);
        }

        //加载 UBB 组件
        $v['ubb'] = new ubbdisplay();
        $v['uinfo']->setUbbOpt($v['ubb'], $onlyReview);

        $topic = $bbs->topicMeta($v['topic_id'], '*');
        // 偶尔会有回复内容存在但是主题帖丢失的情况
        if (empty($topic)) {
            continue;
        }
        $v['topic']=$topic;
        // 原帖用户
        $v['topicUinfo'] = new userinfo();
        $v['topicUinfo']->uid($topic['uid']);
    }

    $tpl->assign('replyList', $result);
    $tpl->assign('count', $count);
    $tpl->assign('maxP', $maxP);
    $tpl->display('tpl:searchreply');
  }

} catch (Exception $err) {
  $tpl->assign('count', 0);
  $tpl->assign('err', $err);
  //显示版块列表
  $tpl->display('tpl:searchtopic');
}
