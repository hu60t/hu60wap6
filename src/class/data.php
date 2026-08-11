<?php
class data {
    static function serialize($data) {
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    static function unserialize($str) {
        if (empty($str)) {
            return null;
        }
        if (in_array($str[0], ['{', '['])) {
            return json_decode($str, true);
        }
        // 禁止实例化类，防止 PHP 对象注入
        return unserialize($str, ['allowed_classes' => false]);
    }
    static function isJSON($str) {
        if (empty($str)) {
            return false;
        }
        return in_array($str[0], ['{', '[']);
    }
}
