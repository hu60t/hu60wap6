<?php
/**
 * 站点设置
 */


/**
 * 网站注册功能开关
 *
 * 注册功能关闭后，可以配置 tpl/classic/html/site/info.conf 中的 SITE_REG_CLOSE_REASON 来显示注册关闭原因
 */
define('SITE_REG_ENABLE', true);

/**
 * 可信任的域名列表
 * 用户访问这些域名的链接时可直接跳转，不显示警告页面
 * 以 / 开头的字符串被视为正则表达式
 */
$SAFE_DOMAIN_LIST = [
    $_SERVER['HTTP_HOST'], // 当前访问的域名
    '/^(.*\.)?hu60\.(cn|net)$/s', // hu60.cn或hu60.net结尾的任意域名
    'tieba.baidu.com', // 百度贴吧
];

/**
 * 网址替换正则表达式
 * 用于将特定网址替换为其他网址
 */
$URL_REPLACE_REGEXP = [
	['#^https?://((ipv6|ssl|www|wap|m)\.)?hu60\.(cn|net)/#is', '/'],
];

