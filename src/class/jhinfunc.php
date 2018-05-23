<?php
/**
 * Created by PhpStorm.
 * User: yee
 * Date: 17-11-9
 * Time: 下午7:36
 */

//由于smarty模板限制，一些查询只能有PHP实现，所以写这个类库，做兼容

class jhinfunc{
    static function forum($id = 0, $depth = 2){
        $bbs = new bbs();
        return $bbs->childForumMeta($id, '*', $depth);
    }
    static function IndexTopic(){
        $size = 20;
        $p = (int)$_GET['p'];
        if ($p < 1) $p = 1;
        $offset = ($p - 1) * $size;
        $db = new db;
        $meta = DB_A.'bbs_topic_meta';
        $content = DB_A.'bbs_topic_content';
        $forum = DB_A.'bbs_forum_meta';
        $lastTime = $_SERVER['REQUEST_TIME'] - 30 * 24 * 3600;
        $res = $db->query("SELECT {$meta}.*,{$meta}.`id` AS `topic_id`,{$forum}.`id` AS `forum_id`,{$forum}.`name` AS `forum_name`,(SELECT COUNT(*)-1 FROM `{$content}` WHERE `topic_id`=`{$meta}`.`id`) AS `reply_count` FROM `{$meta}` LEFT JOIN `{$forum}` ON `{$forum}`.`id`=`{$meta}`.`forum_id` WHERE `{$meta}`.`ctime`> {$lastTime} ORDER BY level DESC, `{$meta}`.`mtime` DESC LIMIT {$offset},{$size}");
        $topic = $res->fetchAll();
        foreach ($topic as &$v) {
            $v['uinfo'] = new userinfo;
            $v['uinfo']->uid($v['uid']);
        }
        return $topic;
    }

<<<<<<< HEAD
    // 生成翻页UI的html
    // 其中会将URL中的##换成对于页码
    /**
     * @param $page int 当前页
     * @param $pMax integer 最大页数
     * @param $url string 网址格式
     * @return string
     */
    public static function Pager($page,$pMax,$url){
      $str='';
      $n=3; // 共7页，每一边3页

      $maxGenPage = 1; // 输出的最大页码
      $minGenPage = $pMax; // 输出的最小页码

      if(($page-$n) <= 1){
        // 开头几页
        for($i=1;$i<=2*$n+1;$i++){
          $maxGenPage = max($maxGenPage, $i);
          $minGenPage = min($minGenPage, $i);

          $u=str_replace("##",$i,$url);
          $str .= ($page==$i)?"<li class=\"active\"><a href=\"{$u}\">{$i}</a></li>"."\n":"<li><a href=\"{$u}\">{$i}</a></li>"."\n";
          if($i >= $pMax) break;
=======
    // 生成翻页器UI的html
    public static function Pager($p, $pMax, $url, $urlPagePlaceholder='##', $length = 7) {
        $sideLength = (int)(($length - 1) / 2);

        if ($pMax - $p < $sideLength) {
            // 末尾几页
            $begin = max($p - $length + 1, 1);
        } else {
            // 开头或中间几页
            $begin = max($p - $sideLength, 1);
        }

        $end = min($begin + $length - 1, $pMax);

        $html = '';

        for ($i=$begin; $i<=$end; $i++) {
            $u = htmlspecialchars(str_replace($urlPagePlaceholder, $i, $url));
            if ($i == $p) {
                $html .= "<li class=\"active\"><a href=\"{$u}\">{$i}</a></li>\n";
            } else {
                $html .= "<li><a href=\"{$u}\">{$i}</a></li>\n";
            }
>>>>>>> c544a3ff00902aab8f8e22b78bdcbbc92de6ac60
        }

        // 首页按钮
        if ($begin > 1) {
            $u = htmlspecialchars(str_replace($urlPagePlaceholder, 1, $url));
            if ($begin > 2) {
                $html = "<li class=\"disabled\"><a href=\"#\">...</a></li>\n".$html;
            }
            $html = "<li><a href=\"{$u}\">1</a></li>\n".$html;
        }


        // 上一页按钮
        if ($p > 1) {
            $u = htmlspecialchars(str_replace($urlPagePlaceholder, $p-1, $url));
            $html = "<li><a href=\"{$u}\">&lt;</a></li>\n".$html;
        } else {
            $html = "<li class=\"disabled\"><a href=\"#\">&lt;</a></li>\n".$html;
        }

        // 末页按钮
        if ($end < $pMax) {
            $u = htmlspecialchars(str_replace($urlPagePlaceholder, $pMax, $url));
            if ($end < $pMax-1) {
                $html .= "<li class=\"disabled\"><a href=\"#\">...</a></li>\n";
            }
            $html .= "<li><a href=\"{$u}\">$pMax</a></li>\n";
        }

        // 下一页按钮
        if ($p < $pMax){
            $u = htmlspecialchars(str_replace($urlPagePlaceholder, $p+1, $url));
            $html .= "<li><a href=\"{$u}\">&gt;</a></li>"."\n";
        } else {
            $html .= "<li class=\"disabled\"><a href=\"#\">&gt;</a></li>\n";
        }

        $html = "<ul class=\"pagination\">\n{$html}\n</ul>\n";

        // 跳页输入框
        if ($pMax > $length) {
            $u = explode($urlPagePlaceholder, $url);
            foreach ($u as &$i) {
                $i = json_encode($i);
            }
            $u = implode(' + this.value + ', $u);
            $js = "if (event.keyCode == 13){ location = {$u}; }";
            $html .= '<ul class="page-jumper"><li><input placeholder="跳页" onkeypress=\''.$js.'\'></li></ul>';
        }

        return $html;
    }

//class end
}
