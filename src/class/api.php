<?php

/*API工具类*/

class API
{

//解析数据中的ubb
    public static function parseUbb(&$data, $name = null)
    {
        if ($name !== null) {
            foreach ($data as &$v) {
                if (isset($v[$name])) {
                    parseUbb($v[$name]);
                }
            }
        } else {
            if ($_GET['parse'] == 2) {
                $data = xhtmlcheck(bbs_ubb($data));
            } else {
                $data = api_ubb(bbs_ubb($data));
            }
        }
    }

//输出用户的额外信息
    public static function extraUserInfo(&$data, $prefix = '')
    {
        if (is_array($data) && is_array(reset($data))) {
            foreach ($data as &$var) {
                self::extraUserInfo($var, $prefix);
            }

            return $data;
        }

        if (!is_array($data) || !isset($data[$prefix . 'uid'])) {
            return $data;
        }

        $uinfo = new UserInfo();
        $uid = $data[$prefix . 'uid'];
        $uinfo->uid($uid);

        $data[$prefix . 'uname'] = $uinfo->name;

        if (strpos($_GET['uinfo'], 'tx') !== false) {
            $data[$prefix . 'utoux'] = $uinfo->getinfo('avatar.url');
        }
        if (strpos($_GET['uinfo'], 'zc') !== false) {
            $data[$prefix . 'uzhuc'] = $uinfo->regtime;
        }
        if (strpos($_GET['uinfo'], 'qm') !== false) {
            $data[$prefix . 'uqianm'] = $uinfo->getinfo('signature');
        }
        if (strpos($_GET['uinfo'], 'lx') !== false) {
            $data[$prefix . 'ulianx'] = $uinfo->getinfo('contact');
        }

        return $data;
    }

//输出帖子的额外信息
    public static function extraTzInfo(&$data, $tzList = false)
    {
        if (!is_array($data) && !isset($data['tzid']) && !(reset($data)['tzid'])) {
            return $data;
        }

        $index = 'title';
        if ($tzList || strpos($_GET['tzinfo'], 'bkid') !== false) {
            $index .= ',bkid';
        }
        if ($tzList || strpos($_GET['tzinfo'], 'time') !== false) {
            $index .= ',fttime,hftime';
        }
        if ($tzList || strpos($_GET['tzinfo'], 'count') !== false) {
            $index .= ',hfcount,rdcount';
        }
        if (isset($_GET['tznr'])) {
            $tznr = (int)$_GET['tznr'];

            if ($tznr < 0) $index .= ',nr';
            else if ($tznr > 0) $index .= ",left(nr,$tznr) as nr";
        }

        $sql = "select $index from tz where id=?";
        $db = db::conn();
        $rs = $db->prepare($sql);

        if ($rs == null) {
            return $data;
        }

        $tzid = 0;
        $rs->bindParam(1, $tzid);

        if (isset($data['tzid'])) {
            $tzid = $data['tzid'];
            $rs->execute();
            $tzinfo = $rs->fetch(db::ass);
            if ($tzinfo) {
                $data += $tzinfo;
            }
            if ($tzList) {
                $data['id'] = $tzid;
                unset($data['tzid']);
            }
            return $data;
        }

        foreach ($data as &$var) {
            $tzid = $var['tzid'];
            $rs->execute();
            $tzinfo = $rs->fetch(db::ass);
            if ($tzinfo) {
                $var += $tzinfo;
            }
            if ($tzList) {
                $var['id'] = $tzid;
                unset($var['tzid']);
            }
        }

        return $data;
    }


//以json格式输出数据
    public static function echoJson($obj)
    {
        ob_clean();
        echo json_encode($obj, (false === strpos($_GET['json'], 'object') ? 0 : JSON_FORCE_OBJECT) | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | (false !== strpos($_GET['json'], 'compact') ? 0 : JSON_PRETTY_PRINT));
        ob_end_flush();
    }

}