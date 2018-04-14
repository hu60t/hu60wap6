-- 数据库升级语句
-- 请根据实际情况使用

-- 2018年3月11日 21:39:26
-- token添加了data字段
ALTER TABLE `hu60_token` ADD `data` BLOB NOT NULL DEFAULT '' AFTER `uid`;
