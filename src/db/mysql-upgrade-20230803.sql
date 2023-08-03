-- 数据库升级语句
-- 请根据实际情况使用

-- 2023-08-03
-- 为帖子回复建立uid索引，加快用户回复显示速度
ALTER TABLE `hu60_bbs_topic_content` ADD INDEX(`uid`);
