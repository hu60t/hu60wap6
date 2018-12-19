-- 数据库升级语句
-- 请根据实际情况使用


-- 2018年3月11日 21:39:26
-- token添加了data字段
ALTER TABLE `hu60_token` ADD `data` BLOB NOT NULL DEFAULT '' AFTER `uid`;


-- 2018年5月8日 01:27:13
-- 新增了 hu60_book_meta 和 hu60_book_capter 两个表

CREATE TABLE `hu60_book_chapter` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `chapter` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `version` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL,
  `ctime` bigint(20) NOT NULL DEFAULT '0',
  `mtime` bigint(20) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `hu60_book_meta` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(100) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(45) NOT NULL DEFAULT '',
  `chapter_count` int(11) NOT NULL DEFAULT '0',
  `referer` varchar(100) NOT NULL DEFAULT '',
  `referer_url` varchar(255) NOT NULL DEFAULT '',
  `characters` varchar(255) NOT NULL DEFAULT '',
  `uid` int(11) NOT NULL DEFAULT '0',
  `admin_uids` varchar(255) NOT NULL DEFAULT '',
  `ctime` bigint(20) NOT NULL DEFAULT '0',
  `mtime` bigint(20) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `hu60_book_chapter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_chapter` (`book_id`,`chapter`,`version`) USING BTREE;

ALTER TABLE `hu60_book_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mtime` (`mtime`);


-- 2018年12月18日 11:35
-- topic添加了essence字段
ALTER TABLE `hu60_bbs_topic_meta` ADD `essence` TINYINT(1) NOT NULL DEFAULT '0' AFTER `level`;
