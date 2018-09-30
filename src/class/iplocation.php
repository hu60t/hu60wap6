<?php
/**
 * 该代码来自：https://github.com/myweishanli/yii2-ip2location
 * 虎绿林没有获得该代码的授权，并且不清楚作者对该代码的授权情况。
 * 该代码在GPLv3下的分发是有风险的。
 */

/**
 * Created by PhpStorm.
 * User: shanli
 * Date: 2016/4/22
 * Time: 16:29
 */

/**
 * IP 地理位置查询类
 *
 * @package wsl\ip2location
 */
class IpLocation
{
    /**
     * @var string 编码
     */
    public $encoding = 'UTF-8';
    /**
     * @var resource QQWry.Dat文件指针
     */
    protected $fp;
    /**
     * @var int 第一条IP记录的偏移地址
     */
    protected $firstIp;
    /**
     * @var int 最后一条IP记录的偏移地址
     */
    protected $lastIp;
    /**
     * @var int IP 记录的总条数（不包含版本信息记录）
     */
    protected $totalIp;

    /**
     * 构造函数，打开 QQWry.Dat 文件并初始化类中的信息
     *
     * @param string $filename 数据库文件路径
     */
    function __construct($filename = QQWRY_IP_DB_PATH)
    {
        $this->fp = 0;
        if (($this->fp = @fopen($filename, 'rb')) !== false) {
            $this->firstIp = $this->getLong();
            $this->lastIp = $this->getLong();
            $this->totalIp = ($this->lastIp - $this->firstIp) / 7;
        } else {
            throw new IpLocationException("纯真IP数据库 {$filename} 无法打开");
        }
    }

    /**
     * 返回读取的长整型数
     *
     * @return int
     */
    private function getLong()
    {
        //将读取的little-endian编码的4个字节转化为长整型数
        $result = unpack('Vlong', fread($this->fp, 4));
        return $result['long'];
    }

    /**
     * 返回读取的3个字节的长整型数
     *
     * @return int
     */
    private function getLong3()
    {
        //将读取的little-endian编码的3个字节转化为长整型数
        $result = unpack('Vlong', fread($this->fp, 3) . chr(0));
        return $result['long'];
    }

    /**
     * 返回压缩后可进行比较的IP地址
     *
     * @param string $ip
     * @return string
     */
    private function packIp($ip)
    {
        // 将IP地址转化为长整型数，如果在PHP5中，IP地址错误，则返回False，
        // 这时intval将Flase转化为整数-1，之后压缩成big-endian编码的字符串
        return pack('N', intval(ip2long($ip)));
    }

    /**
     * 返回读取的字符串
     *
     * @param string $data
     * @return string
     */
    private function getString($data = '')
    {
        $char = fread($this->fp, 1);
        while (ord($char) > 0) { // 字符串按照C格式保存，以结束
            $data .= $char; // 将读取的字符连接到给定字符串之后
            $char = fread($this->fp, 1);
        }
        return $data;
    }

    /**
     * 返回地区信息
     *
     * @return string
     */
    private function getArea()
    {
        $byte = fread($this->fp, 1); // 标志字节
        switch (ord($byte)) {
            case 0: // 没有区域信息
                $area = '';
                break;
            case 1:
            case 2: // 标志字节为1或2，表示区域信息被重定向
                fseek($this->fp, $this->getLong3());
                $area = $this->getString();
                break;
            default: // 否则，表示区域信息没有被重定向
                $area = $this->getString($byte);
                break;
        }
        return $area;
    }

    /**
     * 根据所给 IP 地址或域名返回所在地区信息
     *
     * @param string $ip 查询的ip
     * @return null|Location
     */
    public function getLocation($ip)
    {
        if (!$this->fp) return null; // 如果数据文件没有被正确打开，则直接返回空
        $location['ip'] = gethostbyname($ip); // 将输入的域名转化为IP地址
        $ip = $this->packIp($location['ip']); // 将输入的IP地址转化为可比较的IP地址
        // 不合法的IP地址会被转化为255.255.255.255
        // 对分搜索
        $l = 0; // 搜索的下边界
        $u = $this->totalIp; // 搜索的上边界
        $findip = $this->lastIp; // 如果没有找到就返回最后一条IP记录（QQWry.Dat的版本信息）

        while ($l <= $u) { // 当上边界小于下边界时，查找失败
            $i = floor(($l + $u) / 2); // 计算近似中间记录
            fseek($this->fp, $this->firstIp + $i * 7);
            $beginip = strrev(fread($this->fp, 4)); // 获取中间记录的开始IP地址
            // strrev函数在这里的作用是将little-endian的压缩IP地址转化为big-endian的格式
            // 以便用于比较，后面相同。
            if ($ip < $beginip) { // 用户的IP小于中间记录的开始IP地址时
                $u = $i - 1; // 将搜索的上边界修改为中间记录减一
            } else {
                fseek($this->fp, $this->getLong3());
                $endip = strrev(fread($this->fp, 4)); // 获取中间记录的结束IP地址
                if ($ip > $endip) { // 用户的IP大于中间记录的结束IP地址时
                    $l = $i + 1; // 将搜索的下边界修改为中间记录加一
                } else { // 用户的IP在中间记录的IP范围内时
                    $findip = $this->firstIp + $i * 7;
                    break; // 则表示找到结果，退出循环
                }
            }
        }

        //获取查找到的IP地理位置信息
        fseek($this->fp, $findip);
        $location['begin_ip'] = long2ip($this->getLong()); // 用户IP所在范围的开始地址
        $offset = $this->getLong3();
        fseek($this->fp, $offset);
        $location['end_ip'] = long2ip($this->getLong()); // 用户IP所在范围的结束地址
        $byte = fread($this->fp, 1); // 标志字节

        switch (ord($byte)) {
            case 1: // 标志字节为1，表示国家和区域信息都被同时重定向
                $countryOffset = $this->getLong3(); // 重定向地址
                fseek($this->fp, $countryOffset);
                $byte = fread($this->fp, 1); // 标志字节
                switch (ord($byte)) {
                    case 2: // 标志字节为2，表示国家信息又被重定向
                        fseek($this->fp, $this->getLong3());
                        $location['country'] = $this->getString();
                        fseek($this->fp, $countryOffset + 4);
                        $location['area'] = $this->getArea();
                        break;
                    default: // 否则，表示国家信息没有被重定向
                        $location['country'] = $this->getString($byte);
                        $location['area'] = $this->getArea();
                        break;
                }
                break;
            case 2: // 标志字节为2，表示国家信息被重定向
                fseek($this->fp, $this->getLong3());
                $location['country'] = $this->getString();
                fseek($this->fp, $offset + 8);
                $location['area'] = $this->getArea();
                break;
            default: // 否则，表示国家信息没有被重定向
                $location['country'] = $this->getString($byte);
                $location['area'] = $this->getArea();
                break;
        }

        if ($location['country'] == ' CZ88.NET') { // CZ88.NET表示没有有效信息
            $location['country'] = '未知';
        }
        if ($location['area'] == ' CZ88.NET') {
            $location['area'] = '';
        }

        // 转换编码并去除无信息时显示的CZ88.NET
        $location = array_map(function ($item) {
            if (function_exists('mb_convert_encoding')) {
                $item = mb_convert_encoding($item, $this->encoding, 'GBK');
            } else {
                $item = iconv('GBK', $this->encoding . '//IGNORE', $item);
            }
            return preg_replace('/\s*cz88\.net\s*/i', '', $item);
        }, $location);

        return $location;
    }

    /**
     * 获取字符串形式的查询结果
     *
     * @author 老虎会游泳
     * @date 2016-8-15 11:38:45
     */
    public function getLocationString($ip) {
        $location = $this->getLocation($ip);
        $country = trim($location['country']);
        $area = trim($location['area']);

        if (empty($country) || $country == '世界') {
            if (empty($area)) {
                return '无位置记录';
            } else {
                return $area;
            }
        } else {
            if (empty($area)) {
                return $country;
            }elseif (substr($area, 0, strlen($country)) == $country) {
                //$area的开头和$country相同，不需要把$country再显示一遍
                return $area;
            } else {
                return "$country $area";
            }
        }
    }

    /**
     * 析构函数，用于在页面执行结束后自动关闭打开的文件。
     *
     */
    function __desctruct()
    {
        if ($this->fp) {
            fclose($this->fp);
        }
        $this->fp = 0;
    }
}

class IpLocationException extends Exception {
    // 未重载父类
}