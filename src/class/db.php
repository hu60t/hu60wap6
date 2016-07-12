<?php
/*设置默认的数据库连接参数*/
db::$TYPE = DB_TYPE;
db::$FILE_PATH = DB_FILE_PATH;
db::$HOST = DB_HOST;
db::$PORT = DB_PORT;
db::$HOST_RO = DB_HOST_RO;
db::$PORT_RO = DB_PORT_RO;
db::$NAME = DB_NAME;
db::$USER = DB_USER;
db::$PASS = DB_PASS;
db::$A = DB_A;
db::$PCONNECT = DB_PCONNECT;

/**
 * 数据库操作类
 *
 * @package hu60t
 * @version 0.1.2
 * @author 老虎会游泳 <hu60.cn@gmail.com>
 * @copyright LGPLv3
 *
 * 用于快速建立一个配置好了的PDO数据库对象，减少打字。
 * 并且用它还可以实现Mysql/SQLite兼容
 * 而且它还支持读写分离
 * 0.1.2
 * 现在不仅如此。
 * 目前的它还能够让PDO的预处理功能变得万分易用！
 * 它已不再是一个数据库连接类，而演变为数据库操作类。
 *
 * <code>
 * $db=new $db;
 * $rs=$db->select('uid', 'user', 'WHERE name=? AND pass=?', $name, $pass);
 * var_dump($rs->fetch());
 * $db->insert('user', 'name,pass', $name, $pass);
 * </code>
 *
 * 觉得方便吗？快来使用吧！
 *
 */
class db
{

    /*数据库配置*/
    /**
     * 数据库类型
     *
     * 可以填mysql或sqlite
     */
    static $TYPE = 'sqlite';

    /**
     * 是否启用模拟预处理
     *
     * 默认false
     * 强烈建议false，禁用模拟预处理
     * true可能导致sql注入
     */
    static $EMULATE_PREPARES = false;

    /**
     * SQLite数据库配置
     *
     * 如果你使用SQLite数据库，则需要配置以下项目
     * 不使用SQLite的用户不需要关心以下项目
     */

    /**
     * SQLite数据库文件路径
     *
     * 该文件必须有读写权限
     */
    static $FILE_PATH = './test.db3';


    /**
     * MYSQL数据库配置
     *
     * 如果你使用MYSQL数据库则需要配置以下项目
     * 使用SQLite的用户不需要关心以下项目
     */

    /**
     * 是否启用数据库持久连接
     *
     * 在多进程服务器（如fastcgi、php-fpm）中，使用数据库持久连接可以提升服务器性能和抗压能力
     */
    static $PCONNECT = true;

    /**
     * 主数据库服务器
     */
    static $HOST = 'localhost';

    /**
     * 主数据库服务器端口
     *
     * 留空则采用php.ini里的默认值
     */
    static $PORT = '';

    /**
     * 从数据库服务器
     *
     * 如果你的PHP运行在分布式平台（如新浪SAE）上，需要做读写分离，则可能需要配置该项。
     * 不需要做读写分离的用户请保持该项的值为空，否则可能无法正常使用数据库。
     */
    static $HOST_RO = '';

    /**
     * 从数据库服务器端口
     *
     * 留空则采用php.ini里的默认值
     * 不使用读写分离的用户不需要关心该项
     */
    static $PORT_RO = '';

    /**
     * 数据库名
     */
    static $NAME = 'test';

    /**
     * 数据库用户名
     */
    static $USER = 'root';

    /**
     * 数据库用户密码
     */
    static $PASS = '';

    /**
     * 数据表名前缀
     *
     * 设置不同的表名前缀可以使你在一个MYSQL中安装多个应用而不因为表名冲突而失败
     * db类的部分方法有自动补全表名前缀的功能
     */
    static $A = '';

    /**
     * 记录集返回模式
     *
     * 对应PDO里的常量，但缩短名称，方便手机输入
     */
    /**
     * 返回关联数组
     */
    const ass = PDO::FETCH_ASSOC;
    /**
     * 返回数字数组
     */
    const num = PDO::FETCH_NUM;
    /**
     * 同时返回关联数组和数字数组
     */
    const both = PDO::FETCH_BOTH;
    /**
     * 返回对象
     */
    const obj = PDO::FETCH_OBJ;
    /**
     * 通过 bindColumn() 方法将列的值赋到变量上
     */
    const bound = PDO::FETCH_BOUND;
    /**
     * 结合了 PDO::FETCH_BOTH、PDO::FETCH_OBJ
     * 在它们被调用时创建对象变量
     */
    const lazy = PDO::FETCH_LAZY;
    /**
     * 只返回字段名
     */
    const col = PDO::FETCH_COLUMN;

    /**
     * 默认的记录集返回模式
     */
    static $DEFAULT_FETCH_MODE = PDO::FETCH_ASSOC;

    /**
     * 默认的PDO错误处理方式
     *
     * 可选的常量有：
     * PDO::ERRMODE_SILENT
     *    只设置错误代码
     * PDO::ERRMODE_WARNING
     *    除了设置错误代码以外， PDO 还将发出一条传统的 E_WARNING 消息。
     * PDO::ERRMODE_EXCEPTION
     *    除了设置错误代码以外， PDO 还将抛出一个 PDOException，并设置其属性，以反映错误代码和错误信息。
     */
    static $DEFAULT_ERRMODE = PDO::ERRMODE_SILENT;

    /*SQLite选项*/

    /**
     * 强制磁盘同步
     *
     * 可选值：
     * FULL
     *    完全磁盘同步。断电或死机不会损坏数据库，但是很慢（很多时间用在等待磁盘同步）
     * NORMAL
     *    普通。大部分情况下断电或死机不会损坏数据库，比OFF慢，
     * OFF
     *    不强制磁盘同步，由系统把更改写到文件。断电或死机后很容易损坏数据库，但是插入或更新速度比FULL提升50倍啊！
     */
    static $SQLITE_SYNC = 'OFF';

    /*MYSQL选项*/

    /**
     * 默认字符集
     */
    static $DEFAULT_CHARSET = 'utf8mb4';


    /*以下是类内部使用的属性*/

    protected $pdo; //临时打开的数据库PDO对象

    protected $rs; //当前的数据记录集对象
    public $auto_db_a = true; //自动添加表名前缀
    /*静态属性*/
    protected static $db; //PDO对象
    protected static $db_ro; //只读数据库PDO对象

    /**
     * 返回PDO连接对象
     */
    static function conn($read_only = false)
    {
        if (self::$TYPE == 'sqlite') {
            $db =& self::$db;
            if ($db) return $db;
            $db = new PDO(self::$TYPE . ':' . self::$FILE_PATH);
            $db->exec('PRAGMA synchronous=' . self::$SQLITE_SYNC);
        } else {
            if (($read_only || self::$HOST == '') && self::$HOST_RO != '') {
                $db =& self::$db_ro;
                $db_host = self::$HOST_RO;
                $db_port = self::$PORT_RO;
            } elseif (self::$HOST != '') {
                $db =& self::$db;
                $db_host = self::$HOST;
                $db_port = self::$PORT;
            } else throw new PDOException('数据库配置错误：self::$HOST和self::$HOST_RO都为空！', 1);
            if ($db)
                return $db;
            if ($db_port != '') $port = ';port=' . $db_port;
            else $port = '';
            $opt = array(
                PDO::ATTR_PERSISTENT => self::$PCONNECT,
            );
            $db = new PDO(self::$TYPE . ':dbname=' . self::$NAME . ';host=' . $db_host . $port, self::$USER, self::$PASS, $opt);
            $db->exec('SET NAMES ' . self::$DEFAULT_CHARSET); //设置默认编码
        }
        $db->setAttribute(PDO::ATTR_ERRMODE, self::$DEFAULT_ERRMODE); //设置以报错形式
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, self::$DEFAULT_FETCH_MODE); //设置fetch时返回数据形式
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, self::$EMULATE_PREPARES); //设置是否启用模拟预处理，强烈建议false
        return $db;
    }

    /**
     * 为表名加前缀
     */
    static function a($name)
    {
        $name = explode(',', $name);
        foreach ($name as &$n) {
            $n = trim($n);
            if ($n[0] === '`' or strpos($n, '.') !== false) continue;
            $n = '`' . self::$A . $n . '`';
        }
        return implode(',', $name);
    }




    /*以下是DB类的非静态部分*/

    /**
     * 取得PDO对象
     */
    public function pdo($read_only = false)
    {
        return $this->pdo !== NULL ? $this->pdo : self::conn($read_only);
    }

    /**
     * 生成PDO预处理参数数组
     */
    function pdoarray($exlen, $data)
    {
        $array = array();
        array_splice($data, 0, $exlen);
        foreach ($data as $d) {
            if (!is_array($d)) $array[] = $d;
            else $array = array_merge($array, $d);
        }

        return $array;
    }


    /**
     * 初始化类
     */
    public function __construct($pdo_conn_str = NULL, $user = NULL, $pass = NULL)
    {
        if ($pdo_conn_str !== NULL) {
            $db =& $this->pdo;
            $db = new PDO($pdo_conn_str, $user, $pass);
            $type = strtolower(substr($pdo_conn_str, 0, strpos($pdo_conn_str, ':')));
            if ($type === 'sqlite') {
                $db->exec('PRAGMA synchronous=' . self::$SQLITE_SYNC);
            } else {
                $db->exec('SET NAMES ' . self::$DEFAULT_CHARSET);
            }
            $db->setAttribute(PDO::ATTR_ERRMODE, self::$DEFAULT_ERRMODE);
            $db->setAttribute(PDO:: ATTR_DEFAULT_FETCH_MODE, self::$DEFAULT_FETCH_MODE);
            return $db;
        }
        return true;
    }


    /*
    * 执行SQL（内部使用）
    */
    protected function sqlexec($read_only, $sql, $data)
    {
        $db = $this->pdo($read_only);
        $rs =& $this->rs;
        if ($data !== array()) {
            $rs = $db->prepare($sql);
            if (!$rs) return false;
            $rs->execute($data);
            return $rs;
        }
        $rs = $db->query($sql);
        return $rs;
    }

    /*
    * 自动加表名前缀（类内部使用）
    */
    protected function auto_a($table)
    {
        if ($this->auto_db_a) $table = self::a($table);
        return $table;
    }

    /**
     * 查询
     */
    public function select($name, $table, $cond = '')
    {
        $table = $this->auto_a($table);
        $sql = "SELECT $name FROM $table $cond";
        $data = func_get_args();
        $data = $this->pdoarray(3, $data);
        return $this->sqlexec(true, $sql, $data);
    }


    /**
     * 更新数据
     */
    public function update($table, $set)
    {
        $table = $this->auto_a($table);
        $sql = "UPDATE $table SET $set";
        $data = func_get_args();
        $data = $this->pdoarray(2, $data);
        return $this->sqlexec(false, $sql, $data);
    }


    /**
     * 删除数据
     */
    public function delete($table, $cond = '')
    {
        $table = $this->auto_a($table);
        $sql = "DELETE FROM $table $cond";
        $data = func_get_args();
        $data = $this->pdoarray(2, $data);
        return $this->sqlexec(false, $sql, $data);
    }

    /**
     * 插入数据
     */
    public function insert($table, $value)
    {
        $table = explode('(', $table);
        $table[0] = $this->auto_a($table[0]);

        if (strpos($value, '(') === FALSE) {
            if ($table[1] != '') {
                $value = "VALUES($value)";
            } else {
                $table[1] = "$value)";
                $value = 'VALUES(' . str_repeat('?,', substr_count($value, ',')) . '?)';
            }
        }
        $sql = "INSERT INTO $table[0]($table[1] $value";
        $data = func_get_args();
        $data = $this->pdoarray(2, $data);
        return $this->sqlexec(false, $sql, $data);
    }

    /**
     * 执行SQL并返回结果集对象
     *
     * 该方法不支持自动添加表名前缀，需要自行添加
     */
    public function query($sql)
    {
        if (preg_match('/^\s*SELECT\s/is', $sql)) $read_only = true;
        else $read_only = false;
        $data = func_get_args();
        $data = $this->pdoarray(1, $data);
        return $this->sqlexec($read_only, $sql, $data);
    }

    /**
     * 执行SQL并返回影响行数
     *
     * 该方法不支持自动添加表名前缀，需要自行添加
     */
    public function exec($sql)
    {
        if (preg_match('/^\s*SELECT\s/is', $sql)) $read_only = true;
        else $read_only = false;
        return $this->pdo($read_only)->exec($sql);
    }

    /**
     * 预处理SQL并返回结果集对象
     *
     * 该方法不支持自动添加表名前缀，需要自行添加
     */
    public function prepare($sql)
    {
        if (preg_match('/^\s*SELECT\s/is', $sql)) $read_only = true;
        else $read_only = false;
        return $this->pdo($read_only)->prepare($sql);
    }

    /**
     * 返回最后一次插入的id
     */
    public function lastInsertId()
    {
        return $this->pdo()->lastInsertId();
    }
    /*db类结束*/
}
