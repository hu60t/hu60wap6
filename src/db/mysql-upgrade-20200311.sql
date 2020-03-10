-- 数据库升级语句
-- 请根据实际情况使用

-- 2020-03-11
-- 增加内信内容字段允许存储的内容长度
ALTER TABLE `hu60_msg` CHANGE `content` `content` MEDIUMBLOB NOT NULL;

