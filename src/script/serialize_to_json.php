<?php
// 把数据库里现有的 PHP serialize 数据转换为 JSON 格式
if ('cli' != php_sapi_name()) {
    die('run in shell: php serialize_to_json.php');
}

ini_set('max_execution_time', 0);

include '../config.inc.php';
$ENABLE_CC_BLOCKING = false;

convert_table(DB_A.'user', 'uid', 'info');
convert_table(DB_A.'user', 'uid', 'safety');
convert_table(DB_A.'addin_chat_data', 'id', 'content');
convert_table(DB_A.'msg', 'id', 'content');
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
    $query = $db->query("SELECT `$idField`,`$dataField` FROM `$table` ORDER BY `$idField` LIMIT $offset,$stepSize");
    $update = $db->prepare("UPDATE `$table` SET `$dataField`=? WHERE `$idField`=?");

    $num = 0;
    while (FALSE !== ($arr = $query->fetch(db::num))) {
        if (empty($arr[1]) || data::isJSON($arr[1])) {
            $counter['skip']++;
        } else {
            $counter['convert']++;
            $arr[1] = data::serialize(data::unserialize($arr[1]));
            $ok = $update->execute([$arr[1], $arr[0]]);
            if ($ok) {
                $counter['success']++;
            }
        }
        $num++;
        $counter['all']++;
    }
    return $num;
}
