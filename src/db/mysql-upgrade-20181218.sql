-- 数据库升级语句
-- 请根据实际情况使用

-- 2018年12月18日 11:35
-- topic添加了essence字段
ALTER TABLE `hu60_bbs_topic_meta` ADD `essence` TINYINT(1) NOT NULL DEFAULT '0' AFTER `level`;
