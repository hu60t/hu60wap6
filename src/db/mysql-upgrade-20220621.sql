-- 数据库升级语句
-- 请根据实际情况使用

-- 2022-06-21
-- 为网页插件添加安装数据统计功能


-- 网页插件表添加两个字段
ALTER TABLE `hu60_webplug` ADD `author_uid` INT NOT NULL DEFAULT '0' AFTER `content`;

ALTER TABLE `hu60_webplug` ADD `webplug_id` CHAR(16) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' AFTER `author_uid`;

ALTER TABLE `hu60`.`hu60_webplug` ADD INDEX `install_count` (`author_uid`, `webplug_id`) USING BTREE; 


-- 创建一个单独的安装量统计表
CREATE TABLE `hu60`.`hu60_webplug_count` (
    `author_uid` INT NOT NULL,
    `webplug_id` CHAR(16) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
    `install_count` INT NOT NULL
) ENGINE = InnoDB; 

ALTER TABLE `hu60_webplug_count` ADD PRIMARY KEY(`author_uid`, `webplug_id`); 
