-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2022-01-31 21:08:36
-- 服务器版本： 10.5.10-MariaDB-log
-- PHP 版本： 8.0.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- 数据库： `hu60`
--

-- --------------------------------------------------------

--
-- 表的结构 `hu60_addin_chat_data`
--

CREATE TABLE `hu60_addin_chat_data` (
  `id` int(11) NOT NULL,
  `room` varchar(32) CHARACTER SET utf8mb4 NOT NULL,
  `lid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `uname` varchar(16) NOT NULL,
  `content` mediumtext CHARACTER SET utf8mb4 NOT NULL,
  `time` bigint(20) NOT NULL,
  `hidden` int(11) NOT NULL DEFAULT 0,
  `review` tinyint(4) DEFAULT 0,
  `review_log` text CHARACTER SET utf8mb4 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_addin_chat_list`
--

CREATE TABLE `hu60_addin_chat_list` (
  `id` int(11) NOT NULL,
  `name` varchar(32) CHARACTER SET utf8mb4 NOT NULL,
  `ztime` bigint(20) NOT NULL,
  `access` bit(32) NOT NULL DEFAULT b'1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_bbs_forum_meta`
--

CREATE TABLE `hu60_bbs_forum_meta` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 NOT NULL,
  `mtime` bigint(20) NOT NULL DEFAULT 0,
  `notopic` tinyint(1) NOT NULL DEFAULT 0,
  `access` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_bbs_topic_content`
--

CREATE TABLE `hu60_bbs_topic_content` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `ctime` bigint(20) NOT NULL,
  `mtime` bigint(20) NOT NULL,
  `content` mediumtext CHARACTER SET utf8mb4 NOT NULL,
  `uid` int(11) NOT NULL,
  `reply_id` int(11) NOT NULL,
  `floor` int(11) NOT NULL DEFAULT 0,
  `locked` tinyint(1) NOT NULL DEFAULT 0,
  `access` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `review` tinyint(4) NOT NULL DEFAULT 0,
  `review_log` text CHARACTER SET utf8mb4 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_bbs_topic_meta`
--

CREATE TABLE `hu60_bbs_topic_meta` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `title` varchar(150) CHARACTER SET utf8mb4 NOT NULL,
  `read_count` int(11) NOT NULL DEFAULT 0,
  `uid` int(11) NOT NULL,
  `ctime` bigint(20) NOT NULL,
  `mtime` bigint(20) NOT NULL,
  `level` tinyint(4) NOT NULL DEFAULT 0,
  `essence` tinyint(1) NOT NULL DEFAULT 0,
  `forum_id` int(11) NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT 0,
  `access` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `review` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_book_chapter`
--

CREATE TABLE `hu60_book_chapter` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `chapter` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `content` mediumtext CHARACTER SET utf8mb4 NOT NULL,
  `version` int(11) NOT NULL DEFAULT 0,
  `uid` int(11) NOT NULL,
  `ctime` bigint(20) NOT NULL DEFAULT 0,
  `mtime` bigint(20) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_book_meta`
--

CREATE TABLE `hu60_book_meta` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(100) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(45) NOT NULL DEFAULT '',
  `chapter_count` int(11) NOT NULL DEFAULT 0,
  `referer` varchar(100) NOT NULL DEFAULT '',
  `referer_url` varchar(255) NOT NULL DEFAULT '',
  `characters` varchar(255) NOT NULL DEFAULT '',
  `uid` int(11) NOT NULL DEFAULT 0,
  `admin_uids` varchar(255) NOT NULL DEFAULT '',
  `ctime` bigint(20) NOT NULL DEFAULT 0,
  `mtime` bigint(20) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_friend_links`
--

CREATE TABLE `hu60_friend_links` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_lutris_release`
--

CREATE TABLE `hu60_lutris_release` (
  `project` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `ctime` bigint(20) NOT NULL,
  `mtime` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_msg`
--

CREATE TABLE `hu60_msg` (
  `id` int(11) NOT NULL,
  `touid` int(11) NOT NULL,
  `byuid` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT 1,
  `isread` int(11) NOT NULL,
  `content` mediumtext CHARACTER SET utf8mb4 NOT NULL,
  `ctime` bigint(20) NOT NULL,
  `rtime` bigint(20) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_speedtest`
--

CREATE TABLE `hu60_speedtest` (
  `id` int(11) NOT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `tag` varchar(10) DEFAULT NULL,
  `startTime` bigint(20) DEFAULT NULL,
  `endTime` bigint(11) DEFAULT NULL,
  `speed` float DEFAULT NULL,
  `success` tinyint(1) DEFAULT NULL,
  `errCode` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_token`
--

CREATE TABLE `hu60_token` (
  `id` bigint(20) NOT NULL,
  `lifetime` bigint(20) NOT NULL,
  `token` char(32) NOT NULL,
  `uid` int(11) NOT NULL,
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_topic_favorites`
--

CREATE TABLE `hu60_topic_favorites` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_user`
--

CREATE TABLE `hu60_user` (
  `uid` int(11) NOT NULL,
  `name` varchar(16) NOT NULL,
  `pass` char(32) NOT NULL,
  `sid` varchar(64) NOT NULL,
  `safety` blob DEFAULT NULL,
  `regtime` bigint(20) NOT NULL,
  `sidtime` bigint(20) NOT NULL,
  `acctime` bigint(20) NOT NULL,
  `info` text CHARACTER SET utf8mb4 DEFAULT NULL,
  `mail` varchar(255) DEFAULT NULL,
  `regphone` varchar(20) CHARACTER SET ascii DEFAULT NULL,
  `permission` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `access` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_userdata`
--

CREATE TABLE `hu60_userdata` (
  `uid` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` mediumblob NOT NULL,
  `version` bigint(20) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_user_relationship`
--

CREATE TABLE `hu60_user_relationship` (
  `relationship_id` int(11) NOT NULL,
  `origin_uid` int(11) NOT NULL,
  `target_uid` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '关系类型'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转储表的索引
--

--
-- 表的索引 `hu60_addin_chat_data`
--
ALTER TABLE `hu60_addin_chat_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room` (`room`),
  ADD KEY `time` (`time`),
  ADD KEY `room_time` (`room`,`time`) USING BTREE,
  ADD KEY `room_lid` (`room`,`lid`) USING BTREE;

--
-- 表的索引 `hu60_addin_chat_list`
--
ALTER TABLE `hu60_addin_chat_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ztime` (`ztime`),
  ADD KEY `name` (`name`);

--
-- 表的索引 `hu60_bbs_forum_meta`
--
ALTER TABLE `hu60_bbs_forum_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `mtime` (`mtime`);

--
-- 表的索引 `hu60_bbs_topic_content`
--
ALTER TABLE `hu60_bbs_topic_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topic_id` (`topic_id`),
  ADD KEY `reply_id` (`reply_id`);

--
-- 表的索引 `hu60_bbs_topic_meta`
--
ALTER TABLE `hu60_bbs_topic_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ctime` (`level`,`ctime`) USING BTREE,
  ADD KEY `mtime` (`level`,`mtime`) USING BTREE,
  ADD KEY `lfctime` (`level`,`forum_id`,`ctime`),
  ADD KEY `lfmtime` (`level`,`forum_id`,`mtime`);

--
-- 表的索引 `hu60_book_chapter`
--
ALTER TABLE `hu60_book_chapter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_chapter` (`book_id`,`chapter`,`version`) USING BTREE;

--
-- 表的索引 `hu60_book_meta`
--
ALTER TABLE `hu60_book_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mtime` (`mtime`);

--
-- 表的索引 `hu60_friend_links`
--
ALTER TABLE `hu60_friend_links`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `hu60_lutris_release`
--
ALTER TABLE `hu60_lutris_release`
  ADD PRIMARY KEY (`project`,`version`),
  ADD KEY `project_ctime` (`project`,`ctime`) USING BTREE;

--
-- 表的索引 `hu60_msg`
--
ALTER TABLE `hu60_msg`
  ADD PRIMARY KEY (`id`),
  ADD KEY `touid` (`type`,`touid`,`isread`,`ctime`) USING BTREE,
  ADD KEY `byuid` (`type`,`byuid`,`isread`,`ctime`) USING BTREE,
  ADD KEY `chat` (`type`,`touid`,`byuid`,`ctime`);

--
-- 表的索引 `hu60_speedtest`
--
ALTER TABLE `hu60_speedtest`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tag` (`tag`);

--
-- 表的索引 `hu60_token`
--
ALTER TABLE `hu60_token`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- 表的索引 `hu60_topic_favorites`
--
ALTER TABLE `hu60_topic_favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_uid_and_topicId` (`uid`,`topic_id`);

--
-- 表的索引 `hu60_user`
--
ALTER TABLE `hu60_user`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `sid` (`sid`),
  ADD UNIQUE KEY `main` (`mail`),
  ADD UNIQUE KEY `mail` (`mail`),
  ADD UNIQUE KEY `regphone` (`regphone`);

--
-- 表的索引 `hu60_userdata`
--
ALTER TABLE `hu60_userdata`
  ADD PRIMARY KEY (`uid`,`key`);

--
-- 表的索引 `hu60_user_relationship`
--
ALTER TABLE `hu60_user_relationship`
  ADD PRIMARY KEY (`relationship_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `hu60_addin_chat_data`
--
ALTER TABLE `hu60_addin_chat_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `hu60_addin_chat_list`
--
ALTER TABLE `hu60_addin_chat_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `hu60_bbs_forum_meta`
--
ALTER TABLE `hu60_bbs_forum_meta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `hu60_bbs_topic_content`
--
ALTER TABLE `hu60_bbs_topic_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `hu60_bbs_topic_meta`
--
ALTER TABLE `hu60_bbs_topic_meta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `hu60_book_chapter`
--
ALTER TABLE `hu60_book_chapter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `hu60_book_meta`
--
ALTER TABLE `hu60_book_meta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `hu60_friend_links`
--
ALTER TABLE `hu60_friend_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `hu60_msg`
--
ALTER TABLE `hu60_msg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `hu60_speedtest`
--
ALTER TABLE `hu60_speedtest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `hu60_token`
--
ALTER TABLE `hu60_token`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `hu60_topic_favorites`
--
ALTER TABLE `hu60_topic_favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `hu60_user`
--
ALTER TABLE `hu60_user`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `hu60_user_relationship`
--
ALTER TABLE `hu60_user_relationship`
  MODIFY `relationship_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
