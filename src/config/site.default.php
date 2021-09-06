<?php
/**
 * 站点设置
 */


/**
 * 网站网址前缀
 */
define('SITE_URL_PREFIX', 'https://hu60.cn');
define('SITE_ROUTER_PATH', '/q.php');
define('SITE_URL_BASE', SITE_URL_PREFIX.SITE_ROUTER_PATH.'/');


/**
 * 网站注册功能开关
 *
 * 注册功能关闭后，可以配置 tpl/classic/html/site/info.conf 中的 SITE_REG_CLOSE_REASON 来显示注册关闭原因
 */
define('SITE_REG_ENABLE', true);

/**
 * 本站域名列表
 * 以 / 开头的字符串被视为正则表达式
 */
$SITE_DOMAIN_LIST = [
    $_SERVER['HTTP_HOST'], // 当前访问的域名
    '/^(.*\.)?hu60\.(cn|net)$/s', // hu60.cn或hu60.net结尾的任意域名
];

/**
 * 可信任的域名列表
 * 用户访问这些域名的链接时可直接跳转，不显示警告页面
 * 以 / 开头的字符串被视为正则表达式
 */
$SAFE_DOMAIN_LIST = [
    'tieba.baidu.com', // 百度贴吧
];

/**
 * 网址替换正则表达式
 * 用于将特定网址替换为其他网址
 */
$URL_REPLACE_REGEXP = [
	['#^https?://((ipv6|ssl|www|wap|m)\.)?hu60\.(cn|net)/#is', '/'],
];

/**
 * 禁止指定用户发特定敏感词
 * 
 * @param preg_match 匹配敏感词的正则表达式
 * @param users 禁止发该内容的uid数字，如果为空或者不存在，则禁止所有用户发该内容
 */
$USER_WORD_BLOCKLIST = [
    ['preg_match' => '/你\s*品/us', 'users' => [1, 17448, 23688]],
];
