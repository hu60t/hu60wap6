<?php

class token
{
    protected $user;
    protected $db;
    protected $token = null;
	protected $data = null; // 自定义数据
    protected $lifetime = 0; // 过期时间（unix时间戳）

    public function __construct($user = null)
    {
        if (!is_object($user)) {
            $user = new user;
		}
        $this->user = $user;
        $this->db = new db;
        //删除过期token
        $this->db->exec('DELETE FROM ' . DB_A . 'token' . ' WHERE lifetime<' . time());
    }

    public function check($token)
    {
        $rs = $this->db->select('lifetime,data', 'token', 'WHERE token=? and uid=?', $token, (int)$this->user->uid);
        $rs = $rs->fetch();
        if (!$rs)
            return false;
        $this->token = $token;
        $this->lifetime = $rs['lifetime'];
		$this->data = $rs['data'];
        return true;
    }

    /**
     * 创建token
     *
     * 参数：
     *     $lifetime 有效期（秒），默认24小时（86400秒）
     */
    public function create($lifetime = 86400, $data = '')
    {
        $this->lifetime = time() + $lifetime;
		$this->data = $data;
        $this->token = md5(str::random_bytes(128));
        $rs = $this->db->insert('token', 'uid,token,lifetime,data', (int)$this->user->uid, $this->token, (int)$this->lifetime, (string)$this->data);
        if ($rs)
            return $this->token;
        else
            return false;
    }

    public function delete()
    {
        $rs = $this->db->delete('token', 'WHERE token=?', $this->token);
        return $rs ? true : false;
    }

    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;
        $rs = $this->update('token', 'lifetime=?', $lifetime);
        return $rs ? true : false;
    }

    public function addLifetime($lifetime)
    {
        $this->lifetime += $lifetime;
        $rs = $this->update('token', 'lifetime=?', $this->lifetime);
        return $rs ? true : false;
    }

    public function token()
    {
        return $this->token;
    }

    public function user()
    {
        return $this->user;
    }

    public function lifetime()
    {
        return $this->lifetime;
    }

	// 返回剩余的有效期
    public function resLifetime()
    {
        return $this->lifetime - time();
    }

	// 返回自定义数据
	public function data() {
		return $this->data;
	}
}
