-- 数据库升级语句
-- 请根据实际情况使用

-- 2022-07-26
-- 删除多余的索引
-- hu60_user 上有两个索引是重复的，main 和 mail，所以删除其中一个

ALTER TABLE `hu60_user` DROP INDEX `main`;
