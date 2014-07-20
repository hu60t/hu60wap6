<?php
/**
* 用户信息类
*/
class msg {

      protected $db;
	  
	/*初始化*/
    public function __construct() {
        $this->db = new db;
    }
	
	/*检测是否有未读信息*/
	public function noreadmsg($uid){
	$rs = $this->db->select('id','msg','WHERE touid=? AND isread=0 AND type=0',$uid);
	if(!$rs) return false;
	$n = count($rs->fetchAll());
	return $n;
	}
	
	/*发送信息*/
	public function send_msg($uid,$touid,$content){
	$ctime = time();
	$rs = $this->db->insert('msg','touid,byuid,type,isread,content,ctime',$touid,$uid,'0','0',$content,$ctime);
	if(!$rs) return false;
	return true;
	}
	
	/*读取信息*/
	public function read_msg($uid,$id){
	$rs = $this->db->select('*','msg','WHERE (touid=? OR byuid=?) AND id=?',$uid,$uid,$id);
	if(!$rs) return false;
	$rs = $rs->fetch();
	if($rs->touid!=$uid || ($rs->touid==$uid && $rs->byuid==$uid))$this->update_msg($uid,$id);
	return $rs;
	}
	
	/*更新信息读取状态*/
	public function update_msg($uid,$id){
	$rtime = time();
	$rs = $this->db->update('msg','isread=?,rtime=? WHERE touid=? AND id=?','1',$rtime,$uid,$id);
	if(!$rs) return false;
	return true;
	}
	
	/*删除信息*
	public function delete_msg($uid,$id){
	$rs = $this->db->delete('msg','WHERE (touid=? OR byuid=?) AND id=?',$uid,$uid,$id);
	if(!$rs) return false;
	return true;
	}
	*/
	
    /*读取指定UID收件箱信息列表*/
    public function read_inbox($uid,$type,$size=15){
	    switch($type){case 'yes':$isread = 'AND isread=1';break;case 'no':$isread = 'AND isread=0';break;default:$isread = '';}
        $rs = $this->db->select('*','msg',"WHERE type=0 AND touid=? $isread",$uid);
        if (!$rs) return false;
		$n = count($rs->fetchAll());
		$px = $this->page($n,$size);
		$rs = $this->db->select("*",'msg',"WHERE type=0 AND touid=? $isread ORDER BY `ctime` DESC LIMIT ?,?",$uid,$px->thispage,$px->pagesize);
		$row['row'] = $rs->fetchAll();
		$row['px'] = $px->pageshow();
		return $row;
	}
	
	/*读取指定UID发件箱信息列表*/
    public function read_outbox($uid,$type,$size=15){
	    switch($type){case 'yes':$isread = 'AND isread=1';break;case 'no':$isread = 'AND isread=0';break;default:$isread = '';}
        $rs = $this->db->select('*','msg',"WHERE type=0 AND byuid=? $isread",$uid);
        if (!$rs) return false;
		$n = count($rs->fetchAll());
		$px = $this->page($n,$size);
		$rs = $this->db->select("*",'msg',"WHERE type=0 AND byuid=? $isread ORDER BY `ctime` DESC LIMIT ?,?",$uid,$px->thispage,$px->pagesize);
		$row['row'] = $rs->fetchAll();
		$row['px'] = $px->pageshow();
		return $row;
	}
	
	//调用分页类
	private function page($n,$size=10,$url="?p") {
	   $px = new pagex();
	   $px->pageurl = $url;
	   $px->total = $n;
	   $px->pagesize = $size;
	   return $px;
    }
/*class end!*/
}
