-- 数据库升级语句
-- 请根据实际情况使用

-- 2023-03-31
-- 修复sid不区分大小写的问题
ALTER TABLE `hu60_user` CHANGE `sid` `sid` VARCHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL;
