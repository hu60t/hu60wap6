-- 数据库升级语句
-- 请根据实际情况使用

-- 2019-10-14
-- 新增一个用于友链的表

CREATE TABLE `hu60_friend_links` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

ALTER TABLE `hu60_friend_links`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `hu60_friend_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- 修改表的存储引擎为MyISAM
ALTER TABLE `hu60_topic_favorites` ENGINE = MyISAM;
ALTER TABLE `hu60_userdata` ENGINE = MyISAM;
