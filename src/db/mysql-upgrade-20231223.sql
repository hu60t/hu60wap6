-- 数据库升级语句
-- 请根据实际情况使用

-- 2023-12-23
-- 为待审核回复建立(flags,review)索引，加快管理员用户的首页加载速度
-- MySQL慢日志显示，在建立索引前，待审核回复的数量有时需要数秒才能统计出来
ALTER TABLE `hu60_addin_chat_data` ADD KEY `flags_review` (`flags`,`review`);
ALTER TABLE `hu60_bbs_topic_content` ADD KEY `flags_review` (`flags`,`review`);
