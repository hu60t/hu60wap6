-- 数据库升级语句
-- 请根据实际情况使用

-- 2019-07-03
-- 新增一个用于存储用户自定义插件设置的表

CREATE TABLE `hu60_userdata` (
  `uid` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` mediumblob NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

ALTER TABLE `hu60_userdata`
  ADD PRIMARY KEY (`uid`,`key`);
