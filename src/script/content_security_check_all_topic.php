<?php
if ('cli' != php_sapi_name()) {
    die('run in shell: php content_security_check_all_topic.php [min-topic-id]');
}

$minTopicId = (int)$argv[1];

include '../config.inc.php';
$ENABLE_CC_BLOCKING = false;

$db = new db;
db::$DEFAULT_ERRMODE = PDO::ERRMODE_EXCEPTION;

$topicList = $db->query('SELECT * FROM '.DB_A.'bbs_topic_meta WHERE id >= ?', $minTopicId);

$ubb = new UbbEdit;

while (FALSE !== ($topic = $topicList->fetch(db::ass))) {
    $topic['title'] = strtr($topic['title'], "\t\n\r\0\x0B", "     ");

    $replyList = $db->query(
        "SELECT * FROM ".DB_A."bbs_topic_content WHERE topic_id=?",
        $topic['id']
    )->fetchAll(db::ass);
    echo "$topic[id]\t《$topic[title]》\t".count($replyList)." 回复\t";

    $checkedReply =[];
    $content = "$topic[title]\n\n";
    foreach ($replyList as $reply) {
        $reply['uinfo'] = UserInfo::getInstanceByUid($reply['uid']);
        $reply['content'] = $ubb->display($reply['content'], true);
        $newContent = $content."\n\n".$reply['uinfo']->name."：".$reply['content'];

        // 内容超长
        if (mb_strlen($newContent, 'utf-8') > 10000 && !empty($checkedReply)) {
            auditTopic($topic, $checkedReply, $content);
            $checkedReply = [];
            $newContent = $reply['uinfo']->name."：".$reply['content'];
        }

        $checkedReply[] = $reply;
        $content = $newContent;
    }

    if (!empty($checkedReply)) {
        auditTopic($topic, $checkedReply, $content);
    }

    echo "\n";
}

function getIdList($array) {
    $ids = [];
    foreach ($array as $item) {
        $ids[] = (int)$item['id'];
    }
    return implode(',', $ids);
}

function auditTopic($topic, $checkedReply, $content) {
    global $db;

    $result = ContentSecurity::auditText(
        UserInfo::getInstanceByUid($topic['uid']),
        ContentSecurity::TYPE_TOPIC,
        $content,
        "topic/$topic[id]/replies"
    );

    set_time_limit(0);
    ini_set('max_execution_time', 0);

    echo "$result[reason]（$result[rate]%）\t";

    if ($result['stat'] === ContentSecurity::STAT_PASS) {
        $reviewLog = ContentSecurity::getReviewLog($result);
        if ($reviewLog !== null) {
            $reviewLog = json_encode($reviewLog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $ids = getIdList($checkedReply);

        $sql = "UPDATE ".DB_A."bbs_topic_content SET review=0,review_log=
            CONCAT(
                IF(
                    `review_log` IS NULL,
                    '[',
                    CONCAT(SUBSTR(`review_log`, 1, CHAR_LENGTH(`review_log`) - 1), ',')
                ),
                ?,
                ']'
            ) WHERE id IN ($ids)";
        $db->query($sql, (string)$reviewLog);

        $db->query("UPDATE ".DB_A."bbs_topic_meta SET review=0 WHERE id=?", $topic['id']);
    }
}
