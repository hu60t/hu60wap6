<?php
/**
 * Created by PhpStorm.
 * User: yee
 * Date: 17-11-9
 * Time: 下午7:36
 */

//由于smarty模板限制，一些查询只能有PHP实现，所以写这个类库，做兼容

class jhinfunc{
    static function forum($id = 0,$depth = 2){
        if($depth < 0)
            return [];
        $bbs = new bbs();
        $res = [];
        foreach($bbs->childForumMeta($id) as $forum){
            $res[] = [
                "id"=>$forum["id"],
                "name"=>$forum["name"],
                "child"=>jhinfunc::forum($forum['id'],--$depth)
            ];
        }
        return $res;
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

    // 生成翻页器UI的html
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
        }
      }elseif(($page+$n) > $pMax){
        // 末尾几页
        for($i=$pMax-2*$n;$i<=$pMax;$i++){
          $maxGenPage = max($maxGenPage, $i);
          $minGenPage = min($minGenPage, $i);

          $u=str_replace("##",$i,$url);
          $str .= ($page==$i)?"<li class=\"active\"><a href=\"{$u}\">{$i}</a></li>"."\n":"<li><a href=\"{$u}\">{$i}</a></li>"."\n";
          if($i >= $pMax) break;
        }
      }else{
        // 中间
        for($i=$n;$i>0;$i--){
          $p=$page-$i;
          if($p < 1){
            continue;
          }
          
          $maxGenPage = max($maxGenPage, $p);
          $minGenPage = min($minGenPage, $p);

          $u=str_replace("##",$p,$url);
          $str .= "<li><a href=\"{$u}\">{$p}</a></li>"."\n";
        }

        $u=str_replace("##",$page,$url);
        $str .= "<li class=\"active\"><a href=\"{$u}\">{$page}</a></li>"."\n";

        for($i=1;$i<=$n;$i++){
          $p=$page+$i;
          if($p > $pMax){
            break;
          }
          
          $maxGenPage = max($maxGenPage, $p);
          $minGenPage = min($minGenPage, $p);
          
          $u=str_replace("##",$p,$url);
          $str .= "<li><a href=\"{$u}\">{$p}</a></li>"."\n";
        }
      }
      
      // 首页按钮
      if ($minGenPage != 1) {
        $u=str_replace("##",1,$url);
        if ($minGenPage != 2) { $str = "<li class=\"disabled\">\n<a href=\"#\">...</a></li>\n".$str; }
        $str = "<li><a href=\"{$u}\">1</a></li>\n".$str;
      }


      // 上一页按钮
      if($page > 1){
        $u=str_replace("##",$page-1,$url);
        $str = "<li><a href=\"{$u}\">&lt;</a></li>"."\n".$str;
      }else{
        $str = "<li class=\"disabled\"><a href=\"#\">&lt;</a></li>"."\n".$str;
      }

      // 末页按钮
      if ($maxGenPage != $pMax) {
        $u=str_replace("##",$pMax,$url);
        if ($maxGenPage != $pMax-1) { $str .= "<li class=\"disabled\"><a href=\"#\">...</a></li>\n";}
        $str .= "<li><a href=\"{$u}\">$pMax</a></li>\n";
      }

      // 下一页按钮
      if($page < $pMax){
        $u=str_replace("##",$page+1,$url);
        $str .= "<li><a href=\"{$u}\">&gt;</a></li>"."\n";
      }else{
        $str .= "<li class=\"disabled\"><a href=\"#\">&gt;</a></li>"."\n";
      }

      $str = "<ul class=\"pagination\">\n{$str}\n</ul>\n";
      
      // 跳页输入框
      if ($pMax > 7) {
        $u = str_replace('##', "'+this.value+'", $url);
        $js = "if(event.keyCode==13){ location='$u'; }";
        $str .= '<ul class="page-jumper"><li><input placeholder="跳页" onkeypress="'.$js.'"></li></ul>';
      }
      
      return $str;
    }
//class end
}
