<?php
/**
 * 高速缓存操作类
 *
 * 该类调用具体的某种特定的高速缓存类（如cacheMemcached类）来实现缓存操作。
 * 被该类调用的高速缓存类须实现cacheInterface接口。
 */
class cache {
    protected static $cacheClassName = CACHE_TYPE;
    protected static $instance = null;

    static protected function getInstance() {
        if (self::$instance == null) {
			$cacheClass = 'cache'.self::$cacheClassName;
            self::$instance = new $cacheClass();
        }

        return self::$instance;
    }

    static public function get($key) {
        return self::getInstance()->get($key);
    }

    static public function set($key, $value, $timeout = 0) {
        return self::getInstance()->set($key, $value, $timeout);
    }

    static public function del($key) {
        return self::getInstance()->del($key);
    }
}
