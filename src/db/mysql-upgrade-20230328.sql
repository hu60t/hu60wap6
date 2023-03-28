-- 数据库升级语句
-- 请根据实际情况使用

-- 2023-03-28
-- 帖子内容和聊天室内容添加flags字段，用于标识和机器人的对话

ALTER TABLE `hu60_bbs_topic_content` ADD `flags` TINYINT NOT NULL DEFAULT '0' AFTER `access`; 
ALTER TABLE `hu60_addin_chat_data` ADD `flags` TINYINT NOT NULL DEFAULT '0' AFTER `hidden`; 
