<?php
/**
 * 首页入口和浏览器适配服务
 *
 * @package hu60t
 * @version 0.1.0
 * @author 老虎会游泳 <hu60.cn@gmail.com>
 * @copyright LGPLv3
 *
 * 当用户访问本文件时，浏览器适配服务会选择他的浏览器支持的页面，
 * 并重定向到路由服务(q.php)显示对应版本的默认首页。
 * 比如跳转到 q.php/index.index.xhtml
 * 默认bid会优先匹配。
 *
 * 当前的匹配方法非常简单，仅仅看 $_SERVER['HTTP_ACCEPT'] 变量是否包含该页面的mime。
 * 如果你准备同时提供Web版本和Wap2.0版本，这种匹配方法就不合适了，
 * 会总是匹配到Web或Wap2.0版本（取决于你的默认bid是什么）。
 * 可以改成按 $_SERVER['HTTP_USER_AGENT'] 匹配。
 *
 * 按MIME匹配的具体过程见：
 * @see PAGE::getMime()
 */
require_once dirname(__FILE__) . '/config.inc.php';
require_once SUB_DIR . '/reg_page_bid.php';
$PAGE = new page;
$PAGE->getMime();
$path = dirname($_SERVER['PHP_SELF']);
if (strlen($path) < 2) $path = '';
header('Location: //'. $_SERVER['HTTP_HOST'] . $path . '/q.php/index.index.' . $PAGE['bid']);
