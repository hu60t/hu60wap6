<?php

interface cacheInterface
{
    public function get($key);

    public function set($key, $value, $timeout = 0);

    public function del($key);
}
