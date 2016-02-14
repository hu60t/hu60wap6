<?php
/**
* 注册bid过程
* 
* @package hu60t
* @version 0.1.0
* @author 老虎会游泳 <hu60.cn@gmail.com>
* @copyright 配置文件
* 
* 该过程文件由路由层(q.php)调用，用于注册当前页可用的页面类型（bid）。
* 通常简单列出所有可用的bid即可，当然也可以根据情况判断并有选择地注册。
* 至于bid的概念和注册方法，见：
* @see PAGE::regBid()
* 
*/
page::regBid('html');
page::regBid('xhtml');
page::regTpl('default');
