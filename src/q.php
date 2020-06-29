<?php
/**
 * URL地址解析与路由服务
 *
 * @package hu60t
 * @version 0.1.0
 * @author 老虎会游泳 <hu60.cn@gmail.com>
 * @copyright LGPLv3
 *
 * 本文件实现了hu60t框架的路由层，
 * 即它解析网址并载入对应的页面。
 * hu60t中所有的URL都应指向该文件，
 * 通过该文件提供的路由服务调用对应的功能。
 *
 * URL的格式类似
 * <http://hu60.cn/q.php/user.login.xhtml>
 * URL格式的详解见：
 * @see PATH::cutPath()
 *
 * 如果有任何未捕捉的异常（如找不到对应的页面、数据库连接失败、PHP代码语法错误等），
 * 路由层会载入默认的错误提示页面：
 * php:error.pageerr
 * 这种页面命名规则见：
 * @see PATH::load()
 * 可以在默认错误提示页中做更进一步的错误处理，
 * 比如不同的错误信息用不同的页面显示等等。
 *
 * 在载入页面之前，路由层还会载入三个文件：
 * 1. 全局配置文件
 * 该配置文件定义了hu60t里必要的常量，并初始化PHP环境（设置错误提示等），
 * 它会在任何代码执行之前载入
 * 2. 注册bid过程
 * 该过程文件注册当前页可用的页面显示类型（bid）。
 * 通常简单列出所有可用的bid即可，当然也可以根据情况判断并有选择地注册。
 * 对于bid是什么的解释，见
 * @see PAGE::load()
 *
 * 3. 全局初始化过程
 * 该过程文件在载入页面之前载入，主要用于定义全局变量等。
 * 比如，可以在它里面定义所有页都可用的 $USER 对象，
 * 方便在页面中进行用户登录验证，避免每个页都要重复定义。
 * 该过程还可以用于用于验证用户是否有访问权限、过滤URL等。
 */
require_once dirname(__FILE__) . '/config.inc.php';


try {
    /*分析URL*/
    $PAGE = new page;

	// Json Page 跨域支持
	if (!empty($_GET['_origin'])) {
		// 坚决禁止跨域 Cookie 访问，否则会形成 XSS 漏洞
		$PAGE->setNoCookie(true);
		header('Access-Control-Allow-Origin: ' . preg_replace('/[^a-zA-Z0-9,._*-]/s', '', trim($_GET['_origin'])));
	}
    
	/*跨站数据提交防护*/
	include SUB_DIR . '/csrf_protect.php';

    $PAGE->cutPath();
	
	// 访问 /q.php 时跳转到 /q.php/index.index.html
	if (strpos($_SERVER['REQUEST_URI'], "$_SERVER[PHP_SELF]/") === FALSE) {
		header("Location: $_SERVER[PHP_SELF]/index.index.$PAGE[bid]");
		exit;
	}

    page::regBid($PAGE->bid);
    /*载入注册bid过程*/
    require_once SUB_DIR . '/reg_page_bid.php';
    /*选择自定义模板*/
    $PAGE->selectTpl();
    /*载入全局初始化过程*/
    include SUB_DIR . '/global_init.php';
    /*载入页面*/
    include $PAGE->load();

} catch (exception $ERR) {

    /*载入错误提示页*/
    include $PAGE->load('error', 'pageerr');
}
