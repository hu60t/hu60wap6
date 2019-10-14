-- 数据库升级语句
-- 请根据实际情况使用

-- 2018年12月27日 20:32
-- 新增了 hu60_topic_favorites 表
CREATE TABLE `hu60_topic_favorites` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

ALTER TABLE `hu60_topic_favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_uid_and_topicId` (`uid`,`topic_id`);

ALTER TABLE `hu60_topic_favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
