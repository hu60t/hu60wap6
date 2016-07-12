<?php

/**
 * 无缓存
 *
 * 该类实现了cache类规定的接口，可以被cache类调用。
 * 该类实际上并不缓存对象，只是提供一个空接口供cache类使用。
 * 在有可用缓存时，请不要使用该缓存类型。
 */
class cacheNull implements cacheInterface
{

    public function get($key)
    {
        return null;
    }

    public function set($key, $value, $timeout = 0)
    {
        return true;
    }

    public function del($key)
    {
        return true;
    }
}
