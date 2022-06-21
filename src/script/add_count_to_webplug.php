<?php
// 为现有的网页插件帖子添加统计功能
if ('cli' != php_sapi_name()) {
    die('run in shell: php add_count_to_webplug.php');
}

ini_set('max_execution_time', 0);

include '../config.inc.php';
$ENABLE_CC_BLOCKING = false;

convert_table(DB_A.'addin_chat_data', 'id', 'content');
convert_table(DB_A.'bbs_topic_content', 'id', 'content');

function convert_table($table, $idField, $dataField, $stepSize = 1000) {
    $counter = [
        'all' => 0,
        'skip' => 0,
        'convert' => 0,
        'success' => 0,
    ];
    echo "converting table $table($idField, $dataField)\n";
    for (
        $offset = 0;
        convert_table_step(
            $table, $idField, $dataField,
            $offset, $stepSize, $counter
        ) > 0;
        $offset += $stepSize
    ) {
        echo "\tall: $counter[all], skip: $counter[skip], convert: $counter[convert], success: $counter[success]\r";
    }
    echo "\tall: $counter[all], skip: $counter[skip], convert: $counter[convert], success: $counter[success]\n";
}

function convert_table_step($table, $idField, $dataField, $offset, $stepSize, &$counter) {
    $db = db::conn();
    $query = $db->query("SELECT `$idField`,`$dataField` FROM `$table` WHERE `$dataField` like '%\"lang\":\"网页插件%' OR `$dataField` like '%\"lang\":\"webplug%' ORDER BY `$idField` LIMIT $offset,$stepSize");
    $update = $db->prepare("UPDATE `$table` SET `$dataField`=? WHERE `$idField`=?");

    $num = 0;
    while (FALSE !== ($arr = $query->fetch(db::num))) {
        $counter['convert']++;
        $arr[1] = data::serialize(add_webplug_id(data::unserialize($arr[1])));
        $ok = $update->execute([$arr[1], $arr[0]]);
        if ($ok) {
            $counter['success']++;
        }
        $num++;
        $counter['all']++;
    }
    return $num;
}

function add_webplug_id($arr) {
    foreach ($arr as &$data) {
        if (isset($data['lang'])) {
            ubbparser::parseWebPlug($data['lang'], $data);
        }
    }
    return $arr;
}
