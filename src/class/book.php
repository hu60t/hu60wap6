<?php

/**
 * 小说阅读器
 */
class book
{
    protected $db;
    protected $user;

    /**
     * 初始化
     * 参数：用户对象（可空）
     */
    public function __construct($user = null)
    {
        $this->db = new db;
		
        if (!is_object($user) or !$user->islogin)
            $this->user = new user;
        else
            $this->user = $user;
    }

	/**
     * 获取实例
     */
    public static function getInstance($user = null)
    {
        return new book($user);
    }
	
	/**
     * 小说总数
     */
    public function bookCount()
    {
        $rs = $this->db->select('count(*)', 'book_meta');
        $n = $rs->fetch(db::num);
		return $n[0];
    }

    /**
     * 小说列表
     */
    public function bookList($offset=0, $size=20, $fetch='*', $orderby='mtime')
    {
        $rs = $this->db->select($fetch, 'book_meta', "ORDER BY `$orderby` DESC, id DESC LIMIT ?,?", $offset, $size);
        return $rs->fetchAll();
    }
	
	/**
     * 小说元信息
     */
	public function bookMeta($bookId, $fetch='*') {
        $rs = $this->db->select($fetch, 'book_meta', 'WHERE id=?', $bookId);
        return $rs->fetch(db::ass);
    }
    
	/**
     * 小说章节数
     */
    public function chapterCount($bookId) {
        $rs = $this->db->select("chapter_count", 'book_meta', 'WHERE id=?', $bookId);
        $n = $rs->fetch(db::num);
        return $n[0];
    }

    /**
     * 章节元信息
	 * 将返回多个version的信息
     */
    public function chapterMeta($bookId, $chapter, $offset=0, $size=1, $fetch='id,book_id,chapter,title,version,uid,ctime,mtime', $orderby='version') {
        $rs = $this->db->select($fetch, 'book_chapter',
			"WHERE book_id=? AND chapter=? ORDER BY `$orderby` DESC LIMIT ?,?",
			$bookId, $chapter, $offset, $size);
        return $rs->fetchAll();
    }
	
	/**
     * 章节内容
     */
    public function chapterContent($capterId, $fetch='*') {
        $rs = $this->db->select($fetch, 'book_chapter', "WHERE id=?", $capterId);
        return $rs->fetch(db::ass);
    }
}
