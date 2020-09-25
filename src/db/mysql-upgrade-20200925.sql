-- 数据库升级语句
-- 请根据实际情况使用

-- 2020-09-25
-- 把内容字段从 BLOB 改成 TEXT，并设为 utf8mb4_general_ci 编码，以解决通过canal读取info更新时乱码的问题
ALTER TABLE `hu60_user` CHANGE `info` `info` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
