<?php
/**
 * 聊天室类
 */
class chat{
    
     protected $db;
    
    /**
     * 初始化

     * 参数：用户对象（可空）
     */
     public function __construct(){
         $this -> db = new db;
         }
    /**
     * 检查聊天室名是否有效
聊天室名只允许汉字、字母、数字、下划线(_)和减号(-)。
     */
     public function checkName($name){
         if($name == '') throw new chatexception('聊天室名不能为空。', 10);
         if(strlen(mb_convert_encoding($name, 'gbk', 'utf-8')) > 10) throw new chatexception("聊天室名 \"$name\" 过长。聊天室名最长只允许10个英文字母或5个汉字（10字节）。", 13);
         if(!str :: 匹配汉字($name, 'A-Za-z0-9_\\-')) throw new chatexception("聊天室名 \"$name\" 无效。只允许汉字、字母、数字、下划线(_)和减号(-)。", 11);
         return TRUE;
         }
    
    /**
     * ***新建聊天室****
     */
     public function newchatroom($name){
         $this -> checkName($name);
         $this -> db -> insert('addin_chat_list', 'name', $name);
        
         }
    
    /**
     * 聊天室列表
     */
     public function roomlist($size = 20){
         $rs = $this -> db -> select('*', 'addin_chat_list', 'ORDER BY `ztime`');
         if (!$rs)
             throw new chatException('数据库错误，表' . DB_A . 'addin_chat_list不可读', 500);
         $n = count($rs -> fetchAll());
         $px = $this -> page($n, $size);
         $rs = $this -> db -> select("*", 'addin_chat_list', 'ORDER BY `ztime` DESC LIMIT ?,?', $px -> thispage, $px -> pagesize);
         $rs = $rs -> fetchAll();
         foreach($rs as $k => $m){
             $rs[$k]['ctime'] = $this -> time_trun(time() - $m['ztime']);
             }
         $row['row'] = $rs;
         $row['px'] = $px -> pageshow();
         return $row;
         }
    
    /**
     * 检查聊天室是否存在,不存在则尝试创建
     */
     public function checkroom($name){
         $rs = $this -> db -> select('name', 'addin_chat_list', 'WHERE name=?', $name);
         $rs = $rs -> fetch();
         if(!$rs) $this -> newchatroom($name);
         }
    
    /**
     * 删除指定聊天室
     */
     public function deleteroom($name){
         $this -> db -> delete('addin_chat_list', 'WHERE name=?', $name);
         $this -> db -> delete('addin_chat_data', 'WHERE room=?', $name);
         }
    
     // 清空指定聊天室内容
    public function emptyroom($name){
         $this -> db -> delete('addin_chat_data', 'WHERE room=?', $name);
         }
    
     // 读取指定聊天室设置
    public function read($name){
         $rs = $this -> db -> select('name', 'addin_chat_list', 'WHERE name=?', $name);
         return $rs -> fetch();
         }
    
     // 设置指定聊天室信息
    public function set($name, $set){
         $rs = $this -> db -> update('addin_chat_list', "? WHERE name=?", $set, $name);
         if(!$rs) return false;
         return true;
         }
    
    /**
     * 指定聊天室发言列表
     */
     public function chatlist($name, $size = 10){
         $rs = $this -> db -> select("*", 'addin_chat_data', 'WHERE room=? ORDER BY `time` DESC', $name);
         $n = count($rs -> fetchAll());
         $px = $this -> page($n, $size);
         $rs = $this -> db -> select("*", 'addin_chat_data', 'WHERE room=? ORDER BY `time` DESC LIMIT ?,?', $name, $px -> thispage, $px -> pagesize);
         $row['row'] = $rs -> fetchAll();
         $row['px'] = $px -> pageshow();
         return $row;
         }
    
    /**
     * 在指定聊天室发言
     */
     public function chatsay($room, $uid, $uname, $content, $time){
         $lid = $this -> db -> select('max(lid)', 'addin_chat_data', 'WHERE room=?', $room) -> fetch();
         $lid = $lid['max(lid)'] + 1;
         $rs = $this -> db -> insert('addin_chat_data', 'room,lid,uid,uname,content,time', $room, $lid, $uid, $uname, $content, $time);
         if($rs){
             $this -> db -> update('addin_chat_list', 'ztime=? WHERE name=?', $time, $room);
             return true;
             }else{
             return false;
             }
         }
    
     // 删除指定聊天室楼层
    public function delete($name, $lid){
         $this -> db -> delete('addin_chat_data', 'WHERE room=? AND lid=?', $name, $lid);
         }
    
     // 计算时间差
    public function time_trun($s){
         if ($s < 60){
             return $s . '秒钟前';
             }elseif ($s < 3600){
             return floor ($s / 60) . '分钟前';
             }elseif ($s < 86400){
             return floor ($s / 3600) . '小时前';
             }else{
             return floor ($s / 86400) . '天前';
             }
         }
    
     // 调用分页类
    private function page($n, $size = 10, $url = "?p"){
         $px = new pagex();
         $px -> pageurl = $url;
         $px -> total = $n;
         $px -> pagesize = $size;
         return $px;
         }
    /**
     * class end!
     */
     }
