<?php

/**
 * memcached高速缓存类
 *
 * 该类实现了cache类规定的接口，可以被cache类调用。
 */
class cacheMemcached implements cacheInterface
{
    /* Memcached 设置 */
    protected static $memcachedName = MEMCACHED_NAME;
    protected static $memcachedServers = [
        [MEMCACHED_HOST, MEMCACHED_PORT]
    ];

    protected $memcached = null;

    public function __construct()
    {
        $this->memcached = new Memcached(self::$memcachedName);
        $this->memcached->addServers(self::$memcachedServers);
    }

    public function get($key)
    {
        return $this->memcached->get($key);
    }

    public function set($key, $value, $timeout = 0)
    {
        return $this->memcached->set($key, $value, $timeout);
    }

    public function del($key)
    {
        return $this->memcached->delete($key);
    }
}
