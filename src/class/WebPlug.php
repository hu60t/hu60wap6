<?php
class WebPlug {
    protected $user;
    protected $db;

    public static function filterContent($content) {
        // UTF-8空格转一般空格
        $content = str::nbsp2space($content);
        if (strlen($content) > 60000) {
            throw new Exception('网页插件太长，不能超过60000字节');
        }
        return $content;
    }

    public function __construct($user) {
        $this->user = $user;
        $this->db = new db;
        $this->checkLogin();
    }

    /**
     * 检查用户是否登录
     */
    protected function checkLogin() {
        if (is_object($this->user) && $this->user->islogin)
            return true;
        throw new Exception('用户未登录或掉线，请先登录。', 401);
    }

    public function moveOldData() {
        $content = $this->user->getinfo('addin.webplug');
        if ($content !== null) {
            $loadOrder = 1;
            $enabled = true;
            $name = '未命名';
            $data = [
                'load_order' => $loadOrder,
                'id' => $this->add($loadOrder, $enabled, $name, $content),
                'enabled' => $enabled,
                'name' => $name,
                'size' => strlen($content),
                'content' => $content,
            ];
            if (!empty($data['id'])) {
                $this->user->setinfo('addin.webplug', null);
                return $data;
            }
        }
        return false;
    }

    public function getList() {
        $rs = $this->db->select('`load_order`, `id`, `enabled`, `name`, LENGTH(`content`) AS `size`, `author_uid`, `webplug_id`', 'webplug', 'WHERE `uid`=? ORDER BY `load_order`', $this->user->uid);
        if (!$rs) {
            throw new Exception('数据库错误，查询失败', 500);
        }
        $result = $rs->fetchAll(db::ass);
        // 自动迁移旧插件数据
        if (empty($result)) {
            $oldData = $this->moveOldData();
            if ($oldData) {
                unset($oldData['content']);
                $result[] = $oldData;
            }
        }
        return $result;
    }

    public function get($id) {
        $rs = $this->db->select('`load_order`, `id`, `enabled`, `name`, LENGTH(`content`) AS `size`, `content`, `author_uid`, `webplug_id`', 'webplug', 'WHERE `id`=? AND `uid`=?', $id, $this->user->uid);
        if (!$rs) {
            throw new Exception('数据库错误，查询失败', 500);
        }
        $result = $rs->fetch(db::ass);
        return $result;
    }

    public function getAll() {
        $rs = $this->db->select('`load_order`, `id`, `enabled`, `name`, LENGTH(`content`) AS `size`, `content`, `author_uid`, `webplug_id`', 'webplug', 'WHERE `uid`=? ORDER BY `load_order`', $this->user->uid);
        if (!$rs) {
            throw new Exception('数据库错误，查询失败', 500);
        }
        $result = $rs->fetchAll(db::ass);
        // 自动迁移旧插件数据
        if (empty($result)) {
            $oldData = $this->moveOldData();
            if ($oldData) {
                $result[] = $oldData;
            }
        }
        return $result;
    }

    public function getHTML() {
        $rs = $this->db->select('name,content', 'webplug', 'WHERE `uid`=? AND `enabled`=1 ORDER BY `load_order`', $this->user->uid);
        if (!$rs) {
            throw new Exception('数据库错误，查询失败', 500);
        }
        $rs = $rs->fetchAll(db::ass);

        // 自动迁移旧插件数据
        if (empty($rs)) {
            $oldData = $this->moveOldData();
            if ($oldData) {
                $rs[] = $oldData;
            }
        }

        $html = [];
        foreach ($rs as $v) {
            $html[] = '<!----- '.htmlspecialchars($v['name']).' ----->';
            $html[] = $v['content'];
        }
        return implode("\n", $html);
    }

    public function setLoadOrder($loadOrderArray /* = [id1 => order1, id2 => order2, ...] */) {
        $rs = $this->db->prepare('UPDATE `'.DB_A.'webplug` SET `load_order`=? WHERE `id`=? AND `uid`=?');
        if (!$rs) {
            throw new Exception('数据库错误，更新失败', 500);
        }
        $count = 0;
        foreach ($loadOrderArray as $id => $order) {
            $count += (int)$rs->execute([$order, $id, $this->user->uid]);
        }
        return $count;
    }

    public function update($id, $name, $content) {
        $content = self::filterContent($content);
        $rs = $this->db->update('webplug', '`name`=?, `content`=? WHERE `id`=? AND `uid`=?', $name, $content, $id, $this->user->uid);
        if (!$rs) {
            throw new Exception('数据库错误，更新失败', 500);
        }
        return $rs->rowCount();
    }

    public function enable($id, $enabled) {
        $rs = $this->db->update('webplug', '`enabled`=? WHERE `id`=? AND `uid`=?', (int)$enabled, $id, $this->user->uid);
        if (!$rs) {
            throw new Exception('数据库错误，更新失败', 500);
        }
        return $rs->rowCount();
    }

    public function add($loadOrder, $enabled, $name, $content, $authorUid = 0, $webplugId = '') {
        $content = self::filterContent($content);
        $loadOrder = (int)$loadOrder;
        if (!$loadOrder) {
            $rs = $this->db->select('MAX(`load_order`)', 'webplug', 'WHERE `uid`=?', $this->user->uid);
            if ($rs) {
                $rs = $rs->fetch(PDO::FETCH_COLUMN, 0);
                $loadOrder = (int)$rs + 1;
            } else {
                $loadOrder = 1;
            }
        }
        $ok = $this->db->insert('webplug', 'uid,load_order,enabled,name,content,author_uid,webplug_id',
            $this->user->uid, $loadOrder, (int)$enabled, $name, $content, $authorUid, $webplugId);
        if (!$ok) {
            throw new Exception('数据库错误，更新失败', 500);
        }
        $id = $this->db->lastInsertId();

        // 更新安装量统计
        $this->db->query('INSERT INTO `'.DB_A.'webplug_count`(`author_uid`,`webplug_id`,`install_count`) VALUES(?,?,1) ON DUPLICATE KEY UPDATE `install_count`=`install_count`+1',
            $authorUid, $webplugId);

        return $id;
    }

    public function delete($id) {
        $rs = $this->db->delete('webplug', 'WHERE `id`=? AND `uid`=?', $id, $this->user->uid);
        if (!$rs) {
            throw new Exception('数据库错误，更新失败', 500);
        }
        return $rs->rowCount();
    }

    public function import($json) {
        if (!is_array($json)) {
            return 0;
        }
        if (isset($json['content']) && is_string($json['content'])) {
            $this->add($json['load_order'], $json['enabled'], $json['name'], $json['content'],
                (int)$json['author_uid'], str::word($json['webplug_id']));
            return 1;
        }
        $count = 0;
        foreach ($json as $v) {
            if (!is_array($v)) continue;
            $count += $this->import($v);
        }
        return $count;
    }

    public static function count($authorUid, $webplugId) {
        static $db = null;
        if (!$db) $db = new db;

        $rs = $db->select('install_count', 'webplug_count', 'WHERE `author_uid`=? AND `webplug_id`=?',
            $authorUid, $webplugId);
        $data = $rs->fetch(db::ass);

        $rs = $db->select('count(*)', 'webplug', 'WHERE `author_uid`=? AND `webplug_id`=?',
            $authorUid, $webplugId);
        $data['current_user'] = $rs->fetch(PDO::FETCH_COLUMN, 0);

        return $data;
    }
}
