<?php
use xingwenge\canal_php\CanalClient;
use xingwenge\canal_php\CanalConnectorFactory;
use xingwenge\canal_php\Fmt;

use Com\Alibaba\Otter\Canal\Protocol\Column;
use Com\Alibaba\Otter\Canal\Protocol\Entry;
use Com\Alibaba\Otter\Canal\Protocol\EntryType;
use Com\Alibaba\Otter\Canal\Protocol\EventType;
use Com\Alibaba\Otter\Canal\Protocol\RowChange;
use Com\Alibaba\Otter\Canal\Protocol\RowData;

if ('cli' != php_sapi_name()) {
    die('run in shell: php wechat-push.php');
}

require_once __DIR__ . '/../config.inc.php';
require_once __DIR__ . '/../nonfree/class/canal/vendor/autoload.php';

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// 禁用防CC模块，避免产生警告
$ENABLE_CC_BLOCKING = false;

$client = CanalConnectorFactory::createClient(CanalClient::TYPE_SOCKET_CLUE);

$client->connect(CANAL_HOST, CANAL_PORT);
$client->checkValid();
$client->subscribe(CANAL_SUB_ID, CANAL_NAME, DB_NAME."\\.".DB_A."(msg|user)");

$wxpusher = new Wxpusher(WXPUSHER_APP_TOKEN);

$wechatBinds = [];

printlog("读取用户微信绑定信息...");

$db = db::conn(true);
$rs = $db->query("SELECT uid,name,info FROM `".DB_A."user`");
while (FALSE !== ($data = $rs->fetch(PDO::FETCH_ASSOC))) {
    if (empty($data['info'])) {
        continue;
    }
    updateUserInfo($data['uid'], $data['name'], $data['info']);
}

$rs = null;
$db = null;

while (true) {
    $message = $client->get(100);
    if ($entries = $message->getEntries()) {
        foreach ($entries as $entry) {
            switch ($entry->getEntryType()) {
                case EntryType::TRANSACTIONBEGIN:
                case EntryType::TRANSACTIONEND:
                    continue 2;
            }

            $rowChange = new RowChange();
            $rowChange->mergeFromString($entry->getStoreValue());
            $evenType = $rowChange->getEventType();
            $header = $entry->getHeader();

            foreach ($rowChange->getRowDatas() as $rowData) {
                switch ($evenType) {
                    case EventType::DELETE:
                        $data = $rowData->getBeforeColumns();
                        break;
                    case EventType::INSERT:
                        $data = $rowData->getAfterColumns();
                        break;
                    default:
                        $data = $rowData->getAfterColumns();
                        break;
                }
            }

            if ($header->getTableName() == DB_A.'user') {
                $uid = null;
                $name = null;
                $info = null;
                foreach ($data as $column) {
                    if ($column->getName() == 'uid') {
                        $uid = $column->getValue();
                    } elseif ($column->getName() == 'name') {
                        $name = $column->getValue();
                    } elseif ($column->getName() == 'info' && $column->getUpdated()) {
                        $info = $column->getValue();
                    }
                }
                if (!empty($uid) && !empty($info)) {
                    if ($evenType == EventType::DELETE) {
                        unset($wechatBinds[$uid]);
                        printlog("$name (uid: $uid) 已删号");
                    } else {
                        updateUserInfo($uid, $name, $info);
                    }
                }
            } elseif ($header->getTableName() == DB_A.'msg') {
                if ($evenType == EventType::INSERT) {
                    $arr = [];
                    foreach ($data as $column) {
                        $arr[$column->getName()] = $column->getValue();
                    }
                    
                    if (!isset($wechatBinds[$arr['touid']]) || $arr['isread'] != 0) {
                        continue;
                    }

                    $uinfo = new UserInfo();
                    @$uinfo->uid($arr['byuid']);

                    $ubb = new UbbText();
                    $ubb->setOpt('display.textWithoutUrl', true);
					$ubb->skipUnknown(TRUE);
                    @$text = $ubb->display($arr['content'], true);
                    $text = trim(preg_replace("#^<!--\s*markdown\s*-->\s+#s", '', $text));

                    if ($arr['type'] == msg::TYPE_MSG) {
                        $type = '内信';
                        $url = SITE_URL_BASE."msg.index.chat.$arr[byuid].html";
                        $text = <<<EOF
@{$uinfo->name} 给您发来内信：

$text
EOF;
                    } else {
                        $type = '@消息';
                        $url = SITE_URL_BASE."link.ack.at.$arr[id].html?url64=".code::b64e($ubb->getOpt('atMsg.Url'));
                    }

                    @$uinfo->uid($arr['touid']);
                    printlog("{$uinfo->name} (uid: $arr[touid]) 收到$type");

                    if (isset($arr['ctime']) && $arr['ctime'] + 3600 * 24 < time()) {
                        $ctime = date('Y-m-d H:i:s', $arr['ctime']);
                        printlog("发送时间 $ctime 超过24小时，不推送给用户");
                        continue;
                    }

                    $wxpusher->send($text, 1, true, $wechatBinds[$arr['touid']]['uid'], $url);
                }
            }
        }
    }
    sleep(1);
}

$client->disConnect();

function printlog($msg) {
    echo date('[Y-m-d H:i:s] '), $msg, "\n";
}

function updateUserInfo($uid, $name, $info) {
    global $wechatBinds;

    $data = data::unserialize($info);
    if (!is_array($data) || !isset($data['wechat']) || !isset($data['wechat']['uid'])) {
        if (isset($wechatBinds[$uid])) {
            unset($wechatBinds[$uid]);
            printlog("$name (uid: $uid) 已解除微信绑定");
        }
        return false;
    }

    $data = $data['wechat'];
    $wechatBinds[$uid] = $data;
    printlog("$name (uid: $uid) -> $data[userName] ($data[uid])");
    return true;
}
