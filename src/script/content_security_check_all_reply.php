<?php
if ('cli' != php_sapi_name()) {
    die('run in shell: php content_security_check_all_reply.php [min-topic-id]');
}

$minTopicId = (int)$argv[1];

include '../config.inc.php';
$ENABLE_CC_BLOCKING = false;

$db = new db;
db::$DEFAULT_ERRMODE = PDO::ERRMODE_EXCEPTION;

$topicList = $db->query('SELECT * FROM '.DB_A.'bbs_topic_meta WHERE id >= ?', $minTopicId);

$ubb = new UbbEdit;

$rateCounter = 0;
while (FALSE !== ($topic = $topicList->fetch(db::ass))) {
    $topic['title'] = strtr($topic['title'], "\t\n\r\0\x0B", "     ");

    $replyList = $db->query(
        "SELECT * FROM ".DB_A."bbs_topic_content WHERE topic_id=? ORDER BY floor",
        $topic['id']
    )->fetchAll(db::ass);
    echo "# $topic[id]\t《$topic[title]》\t".count($replyList)." 回复\n";

    $checkedReply =[];
    $tasks = [];
    foreach ($replyList as $reply) {
        $reply['uinfo'] = UserInfo::getInstanceByUid($reply['uid']);
        $reply['content'] = $ubb->display($reply['content'], true);
        if ($reply['floor'] == 0) {
            $reply['content'] = "$topic[title]\n\n$reply[content]";
        }

        // 处理超长内容
        if (mb_strlen($reply['content']) > 10000) {
            $reply['content'] = preg_replace('/\t+/', ' ', $reply['content']);
        }
        if (mb_strlen($reply['content']) > 10000) {
            $reply['contentTooLong'] = true;
            $reply['content'] = mb_substr($reply['content'], 0, 10000);
        }

        // 处理空白内容（不处理接口会报错）
        if (empty($reply['content'])) {
            $reply['content'] = '[empty]';
        }

        $task = [
            'user' => $reply['uinfo'],
            'text' => $reply['content'],
            'contentTag' => "reply/$topic[id]/$reply[id]",
        ];

        // 达到50条
        if (count($checkedReply) >= 50) {
            auditReply($topic, $checkedReply, $tasks);
            $checkedReply = [];
            $tasks = [];
        }

        $checkedReply[] = $reply;
        $tasks[] = $task;

        // 限速
        // 为什么调用内容安全API返回错误码588（EXCEED_QUOTA）？
        // 报错原因：请求频率超出并发配额。默认并发：文本检测100条/秒。 
        $rateCounter++;
        if ($rateCounter >= 50) {
            sleep(1);
            $rateCounter = 0;
        }
    }

    if (!empty($checkedReply)) {
        auditReply($topic, $checkedReply, $tasks);
    }
}

function getIdList($array) {
    $ids = [];
    foreach ($array as $item) {
        $ids[] = (int)$item['id'];
    }
    return implode(',', $ids);
}

function auditReply($topic, $checkedReply, $tasks) {
    global $db;

    $results = ContentSecurity::auditTextBatch(
        ContentSecurity::TYPE_TOPIC,
        $tasks
    );

    set_time_limit(0);
    ini_set('max_execution_time', 0);

    for ($i=0; $i<count($checkedReply); $i++) {
        $reply = $checkedReply[$i];
        $result = $results['results'][$i];
        echo "$topic[id]\t$reply[id]\t$result[reason]\t$result[rate]%\n";

        $review = ($result['stat'] === ContentSecurity::STAT_PASS && !$reply['contentTooLong']) ? 0 : 1;

        $reviewLog = ContentSecurity::getReviewLog($result);
        if ($reviewLog !== null) {
            $reviewLog = json_encode($reviewLog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $sql = "UPDATE ".DB_A."bbs_topic_content SET review=?,review_log=
            CONCAT(
                IF(
                    `review_log` IS NULL,
                    '[',
                    CONCAT(SUBSTR(`review_log`, 1, CHAR_LENGTH(`review_log`) - 1), ',')
                ),
                ?,
                ']'
            ) WHERE id=?";
        $db->query($sql, $review, (string)$reviewLog, $reply['id']);

        if ($reply['floor'] == 0) {
            $db->query("UPDATE ".DB_A."bbs_topic_meta SET review=? WHERE id=?", $review, $topic['id']);
        }
    }
}
