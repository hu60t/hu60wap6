-- 数据库升级语句
-- 请根据实际情况使用

-- 2022-07-23
-- 用户名支持生僻字

ALTER TABLE `hu60_user` CHANGE `name` `name` VARCHAR(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;

ALTER TABLE `hu60_addin_chat_data` CHANGE `uname` `uname` VARCHAR(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
