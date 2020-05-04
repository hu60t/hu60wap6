-- 数据库升级语句
-- 请根据实际情况使用

-- 2020-05-05
-- 增加先审后发功能
ALTER TABLE `hu60_bbs_topic_meta` ADD `review` TINYINT NOT NULL DEFAULT '0' AFTER `locked`; 
ALTER TABLE `hu60_bbs_topic_content` ADD `review` TINYINT NOT NULL DEFAULT '0' AFTER `locked`; 

