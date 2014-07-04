<?php
class token {
    protected $user;
    protected $db;
    protected $token = null;
    protected $lifetime = 0; //过期时间（unix时间戳）
    
    public function __construct($user) {
        if (!is_object($user) or !$user->islogin)
            $this->user = new user;
        else
            $this->user = $user;
        $this->db = new db;
        //删除过期token
        $this->db->exec('DELETE FROM '.DB_A.'token'.' WHERE lifetime<'.time());
    }
    
    public function check($token) {
        $rs = $this->db->select('lifetime', 'token', 'WHERE token=? and uid=?', $token, $this->user->uid);
        $rs = $rs->fetch();
        if (!$rs)
            return false;
        $this->token = $token;
        $this->lifetime = $rs['lifetime'];
        return true;
    }
    
    /**
    * 创建token
    *
    * 参数：
    *     $lifetime 有效期（秒），默认24小时（86400秒）
    */
    public function create($lifetime = 86400) {
        $this->lifetime = time() + $lifetime;
        $this->token = str_shuffle(md5($this->user->sid.microtime().rand(-2147483648,2147483647)));
        $rs = $this->db->insert('token', 'uid,token,lifetime', $this->user->uid, $this->token, $this->lifetime);
        if ($rs)
            return $this->token;
        else
            return false;
    }
    
    public function delete() {
        $rs = $this->db->delete('token', 'WHERE token=?', $this->token);
        return $rs ? true : false;
    }
    
    public function setLifetime($lifetime) {
        $this->lifetime = $lifetime;
        $rs = $this->update('token', 'lifetime=?', $lifetime);
        return $rs ? true : false;
    }
    
    public function addLifetime($lifetime) {
        $this->lifetime += $lifetime;
        $rs = $this->update('token', 'lifetime=?', $this->lifetime);
        return $rs ? true : false;
    }
    
    public function token() {
        return $this->token;
    }
    
    public function user() {
        return $this->user;
    }
    
    public function lifetime() {
        return $this->lifetime;
    }
    
    public function resLifetime() {
        return $this->lifetime - time();
    }
    
}