<?php
/**
 * 系统设置
 *
 * @package hu60t
 * @version 0.1.0
 * @author 老虎会游泳 <hu60.cn@gmail.com>
 * @copyright 配置文件
 *
 * 该文件用来配置hu60t框架的细节。
 */

/* 缓存设置 */
/**
 * 缓存类型
 *
 * 可选：memcached（内存缓存）、null（无缓存）
 */
define('CACHE_TYPE', 'null');

/* memcached设置（选择其他缓存类型时不需要设置） */
/** 服务器名称 */
define('MEMCACHED_NAME', 'hu60');
/** 服务器主机名 */
define('MEMCACHED_HOST', 'localhost');
/** 服务器端口 */
define('MEMCACHED_PORT', 11211);

/**
 * Smarty编译设置
 *
 * 取值：
 *     0 默认
 *     1 关闭编译检查
 *     2 强制编译
 * 该设置只对通过page对象获取的Smarty对象有效。
 */
define('SMARTY_COMPILE', 0);

/**
 * 默认时区
 *
 *asia/shanghai是北京时间（其实是“上海时间”）
 */
date_default_timezone_set('asia/shanghai');

/**
 * 用户自动掉线时间
 *
 * 单位：秒
 * 2592000秒是30天
 */
define('DEFAULT_LOGIN_TIMEOUT', 2592000);


/*页面默认设置*/

/**
 * 首选页面模板
 *
 * 设为 'default' 使用默认模板
 */
define('DEFAULT_PAGE_TPL', 'jhin');
/**
 * 默认页面cid
 */
define('DEFAULT_PAGE_CID', 'index');
/**
 * 默认页面pid
 */
define('DEFAULT_PAGE_PID', 'index');
/**
 * 默认页面bid
 */
define('DEFAULT_PAGE_BID', 'html');
/**
 * 默认页面mime
 */
define('DEFAULT_PAGE_MIME', 'text/html');

/**
 * Cookie设置
 *
 * 以下设置都不是强制生效的，需要开发人员在setCookie时自行使用下面的常量
 * 如果设置引起掉线，可以把下面常量的值都改为NULL（Cookie前缀可以不要改）
 */

/**
 * cookie作用路径
 */
define('COOKIE_PATH', '/');
/**
 * cookie作用域名
 */
define('COOKIE_DOMAIN', $_SERVER['HTTP_HOST']);
/**
 * Cookie前缀
 */
define('COOKIE_A', 'hu60_');

/**
 *网页gzip压缩等级
 *
 * 9为最高，0关闭
 * 如果用户的浏览器支持gzip压缩，该功能可以使他浏览网页节省一半多的流量，并提高网页加载速度。
 */
define('PAGE_GZIP', 0);

/*目录路径*/

/**
 * 用户文件存放目录
 *
 * 开发者应该把用户上传的文件统一写入该目录
 */
define('USERFILE_DIR', ROOT_DIR . '/userfile');

/**
 * 本地头像文件夹
 */
define('AVATOR_DIR', ROOT_DIR.'/upload');

/**
 * 使用七牛云存储头像
 */
define('QINIU_STORAGE_AVATOR', true);

/**
 * 七牛云头像路径前缀
 */
define('QINIU_AVATOR_PATH', 'avator/');

