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
        return unserialize($str);
    }
    static function isJSON($str) {
        if (empty($str)) {
            return false;
        }
        return in_array($str[0], ['{', '[']);
    }
}
