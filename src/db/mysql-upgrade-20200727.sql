-- 数据库升级语句
-- 请根据实际情况使用

-- 2020-07-27
-- 把内容字段从 BLOB 改成 TEXT，并设为 utf8mb4_general_ci 编码，以解决搜索的大小写敏感问题。
ALTER TABLE `hu60_msg` CHANGE `content` `content` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `hu60_addin_chat_data` CHANGE `content` `content` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;

-- 把各种标题字段转换为utf8mb4，以允许emoji表情包
ALTER TABLE `hu60_addin_chat_data` CHANGE `room` `room` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `hu60_addin_chat_list` CHANGE `name` `name` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `hu60_bbs_forum_meta` CHANGE `name` `name` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `hu60_bbs_topic_content` CHANGE `content` `content` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `hu60_book_chapter` CHANGE `title` `title` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `hu60_book_chapter` CHANGE `content` `content` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
