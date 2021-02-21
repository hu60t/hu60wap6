<?php

//站内搜索类
class search
{
    const CACHE_TIMEOUT = 600;
    const SEARCH_LIMIT = 1000;
    protected $time;

    public function __construct()
    {
        $this->time = time();
    }

    protected function searchWord($table, $field, $index, $word, $order = '', $searchLimit = self::SEARCH_LIMIT)
    {
        $wordHash = md5("$table/$field/$word");
        $key = "search/$wordHash";

        $rs = cache::get($key);

        if (is_array($rs) && $this->time - $rs['time'] < self::CACHE_TIMEOUT) {
            return $rs['data'];
        }
		
		$limitKey = "search/limit/$_SERVER[REMOTE_ADDR]";
		$limit = (int)cache::get($limitKey);
		// 每个词会触发3次搜索，所以是30
		if (++$limit > 30) {
			throw new Exception('搜索词过多或搜索速度过快。虎绿林仅支持60秒内搜索10个词');
		}
		cache::set($limitKey, $limit, 60);


        $sql = "SELECT $index FROM " . DB_A . "$table WHERE $field LIKE ? $order LIMIT $searchLimit";
        $db = db::conn();
        $rs = $db->prepare($sql);

        if (!$rs || !$rs->execute(["%$word%"])) {
            throw new Exception("数据库错误");
        }

        $rs = $rs->fetchAll(db::ass);
        cache::set($key, ['time' => $this->time, 'data' => $rs], self::CACHE_TIMEOUT);

        return $rs;
    }

    protected function getAllResult($words)
    {
        $wordsHash = md5($words);
        $key = "search/result/$wordsHash";

        $rs = cache::get($key);

        if (is_array($rs) && $this->time - $rs['time'] < self::CACHE_TIMEOUT) {
            $data = $rs['data'];

            return $data;
        }

        $wordList = explode(' ', $words);
        $db = db::conn();
        $tzRs = $db->prepare('SELECT mtime,uid FROM ' . DB_A . 'bbs_topic_meta WHERE id=?');

        if (!$tzRs) {
            throw new Exception('数据库错误');
        }

        $data = [];
        $wordWV = 10 + count($wordList);

        foreach ($wordList as $word) {
            $wordWV--;

            $data[200 * $wordWV][] = $this->searchWord('bbs_topic_meta', 'title', 'id', $word, 'ORDER BY mtime DESC');
            $data[100 * $wordWV][] = $this->searchWord('bbs_topic_content', 'reply_id=0 AND content', 'topic_id', $word, 'ORDER BY mtime DESC');
            $data[0.01 * $wordWV][] = $this->searchWord('bbs_topic_content', 'reply_id!=0 AND content', 'topic_id', $word, 'ORDER BY mtime DESC');

        }

        $result = [];
        $uid = [];

        foreach ($data as $WV => $v) {
            foreach ($v as $vv)
                foreach ($vv as $vvv)
                    foreach ($vvv as $tzid) {
                        $result[$tzid] += $WV;
                    }
        }

        foreach ($result as $tzid => & $WV) {
            if ($WV > 2000) {
                $WV -= 1000;
            }

            $tzRs->execute([$tzid]);
            $rs = $tzRs->fetch(db::ass);

            if ($rs) {
                $WV += "0.$rs[mtime]";
                $uid[$tzid] = $rs['uid'];
            }
        }

        arsort($result);
        $tz = [];

        foreach ($result as $tzid => $WV) {
            $tz[] = ['tid' => $tzid, 'uid' => $uid[$tzid]];
        }

        cache::set($key, ['time' => $this->time, 'data' => $tz]);

        return $tz;
    }

    protected function uidFilter($uid, & $result)
    {
        foreach ($result as $key => $tz) {
            if ($tz['uid'] != $uid) {
                unset($result[$key]);
            }
        }
    }

    public function searchTopic($words, $userName = '', $offset = 0, $limit = self::SEARCH_LIMIT, & $count = true)
    {
        $words = strtolower(trim(preg_replace("![ \r\n\t\x0c\xc2\xa0]+!us", ' ', $words)));
        $userName = preg_replace('![^a-zA-Z0-9\x{4e00}-\x{9fa5}_-]!ius', '', $userName);

        if ($words == '' && $userName == '') {
            throw new Exception('搜索词和用户名不能都为空');
        } else if ($words == '') {
            $uinfo = new userinfo();
            $uinfo->name($userName);
            $uid = $uinfo['uid'];

            if (!$uid) {
                throw new Exception('用户名不存在');
            }

            $sql = 'SELECT id AS tid,uid FROM ' . DB_A . 'bbs_topic_meta WHERE uid=? ORDER BY level desc, mtime DESC LIMIT ?,?';
            $rs = db::conn()->prepare($sql);

            if (!$rs || !$rs->execute([$uid, $offset, $limit])) {
                throw new Exception('数据库错误');
            }

            $result = $rs->fetchAll(db::ass);

            if ($count !== true) {
                $rs = db::conn()->query('SELECT count(*) FROM ' . DB_A . 'bbs_topic_meta WHERE uid=' . (int)$uid);

                if ($rs) {
                    $count = $rs->fetch(db::num);

                    if (is_array($count)) {
                        $count = $count[0];
                    }
                }
            }

            return $result;
        }

        $result = $this->getAllResult($words);

        if ($userName != '') {
            $uinfo = new userinfo();
            $uinfo->name($userName);
            $uid = $uinfo['uid'];

            if (!$uid) {
                throw new Exception('用户名不存在');
            }

            $this->uidFilter($uid, $result);
        }

        if ($count !== true) {
            $count = count($result);
        }

        $result = array_splice($result, $offset, $limit);

        return $result;
    }

    /*
     * 回复搜索
     */
    public function searchReply($words, $userName = '', $offset = 0, $limit = self::SEARCH_LIMIT, &$count = true, $onlyReview = false)
    {
        global $USER;

        $words = strtolower(trim(preg_replace("![ \r\n\t\x0c\xc2\xa0]+!us", ' ', $words)));
        $userName = preg_replace('![^a-zA-Z0-9\x{4e00}-\x{9fa5}_-]!ius', '', $userName);

        if (!$onlyReview && $userName == '') {
            throw new Exception('勾选“搜索用户回复”时用户名不能为空');
        }

        if ($userName != '') {
            $uinfo = new userinfo();
            $uinfo->name($userName);
            $uid = $uinfo['uid'];

            if (!$uid) {
                throw new Exception('用户名不存在');
            }
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM ' . DB_A . 'bbs_topic_content WHERE ';
        if ($onlyReview == -1) {
            $sql .= 'review_log LIKE ?';
            $args[] = "%\"uid\":{$USER->uid},%";
        }
        elseif ($onlyReview) {
            $sql .= 'review=?';
            $args[] = (int)$onlyReview;
        } else {
            $sql .= 'reply_id!=0';
        }

        if (isset($uid)) {
            $sql .= ' AND uid=? AND reply_id!=0';
            $args = [$uid];
        }

        if ($words != '') {
            $words = explode(' ', $words);
            foreach ($words as $w) {
                $sql .= ' AND content LIKE ?';
                $args[] = '%'.$w.'%';
            }
        }

        $sql .= ' ORDER BY mtime DESC LIMIT ?,?';
        $args[] = $offset;
        $args[] = $limit;
        $rs = db::conn()->prepare($sql);

        if (!$rs || !$rs->execute($args)) {
            throw new Exception('数据库错误');
        }

        $result = $rs->fetchAll(db::ass);

        if ($count !== true) {
            $rs = db::conn()->query('SELECT FOUND_ROWS()');

            if ($rs) {
                $count = $rs->fetch(db::num);

                if (is_array($count)) {
                    $count = $count[0];
                }
            }
        }

        return $result;
    }
}
