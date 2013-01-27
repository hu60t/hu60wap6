<?php
/**
* @package 数据库设置
*/
  
  
/**
* 数据库类型
* 可以填mysql或sqlite
*/
define('DB_TYPE','sqlite');
  
  
/**
* SQLite数据库配置
* 如果你使用SQLite数据库，则需要配置以下项目
* 不使用SQLite的用户不需要关心以下项目
*/
  
/**
* SQLite数据库文件路径
* 该文件必须有读写权限
*/
define('DB_FILE_PATH',ROOT_DIR.'/db/test.db3');
  
  
/**
* MYSQL数据库配置
* 如果你使用MYSQL数据库则需要配置以下项目
* 使用SQLite的用户不需要关心以下项目
*/
  
/**
* 主数据库服务器
*/
define('DB_HOST','localhost');
  
/**
* 主数据库服务器端口
*/
define('DB_PORT','3306');
  
/**
* 只读数据库服务器
* 如果你的PHP运行在分布式平台（如新浪SAE）上，需要做读写分离，则可能需要配置该项。
* 不需要做读写分离的用户请保持该项的值为空，否则可能无法正常使用数据库。
*/
define('DB_HOST_RO','');
  
/**
* 只读数据库服务器端口
* 不使用读写分离的用户不需要关心该项
*/
define('DB_PORT_RO','3306');
  
/**
* 数据库名
*/
define('DB_NAME','test');
 
/**
* 数据库用户名
*/
define('DB_USER','test');
  
/**
* 数据库用户密码
*/
define('DB_PASS','NXW4aM4vhJKSJDun');
  
/**
* 数据表名前缀
* 设置不同的表名前缀可以使你在一个MYSQL中安装多个应用而不因为表名冲突而失败
* 对于开发者，hu60t并不会强制使用表名前缀。在拼凑SQL时需要自己使用DB_A常量加表名前缀。
*/
define('DB_A','hu60_');
