-- 数据库升级语句
-- 请根据实际情况使用

-- 2021-02-21
-- 添加审核记录字段
ALTER TABLE `hu60_bbs_topic_content` ADD `review_log` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `review`;
ALTER TABLE `hu60_addin_chat_data` ADD `review_log` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `review`;
