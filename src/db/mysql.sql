-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: 2018-05-07 17:30:45
-- 服务器版本： 10.0.28-MariaDB-2
-- PHP Version: 7.1.13-1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hu60`
--

-- --------------------------------------------------------

--
-- 表的结构 `hu60_addin_chat_data`
--

CREATE TABLE `hu60_addin_chat_data` (
  `id` int(11) NOT NULL,
  `room` varchar(32) NOT NULL,
  `lid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `uname` varchar(16) NOT NULL,
  `content` mediumblob NOT NULL,
  `time` bigint(20) NOT NULL,
  `hidden` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_addin_chat_list`
--

CREATE TABLE `hu60_addin_chat_list` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `ztime` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_bbs_forum_meta`
--

CREATE TABLE `hu60_bbs_forum_meta` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `mtime` bigint(20) NOT NULL DEFAULT '0',
  `notopic` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_bbs_topic_content`
--

CREATE TABLE `hu60_bbs_topic_content` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `ctime` bigint(20) NOT NULL,
  `mtime` bigint(20) NOT NULL,
  `content` mediumblob NOT NULL,
  `uid` int(11) NOT NULL,
  `reply_id` int(11) NOT NULL,
  `floor` int(11) NOT NULL DEFAULT '0',
  `locked` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_bbs_topic_meta`
--

CREATE TABLE `hu60_bbs_topic_meta` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `title` varchar(150) CHARACTER SET utf8mb4 NOT NULL,
  `read_count` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL,
  `ctime` bigint(20) NOT NULL,
  `mtime` bigint(20) NOT NULL,
  `level` tinyint(4) NOT NULL DEFAULT '0',
  `forum_id` int(11) NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_book_chapter`
--

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
  `chapter_count` int(11) NOT NULL DEFAULT '0',
  `referer` varchar(100) NOT NULL DEFAULT '',
  `referer_url` varchar(255) NOT NULL DEFAULT '',
  `characters` varchar(255) NOT NULL DEFAULT '',
  `uid` int(11) NOT NULL DEFAULT '0',
  `admin_uids` varchar(255) NOT NULL DEFAULT '',
  `ctime` bigint(20) NOT NULL DEFAULT '0',
  `mtime` bigint(20) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_msg`
--

CREATE TABLE `hu60_msg` (
  `id` int(11) NOT NULL,
  `touid` int(11) NOT NULL,
  `byuid` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `isread` int(11) NOT NULL,
  `content` blob NOT NULL,
  `ctime` bigint(20) NOT NULL,
  `rtime` bigint(20) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_user`
--

CREATE TABLE `hu60_user` (
  `uid` int(11) NOT NULL,
  `name` varchar(16) NOT NULL,
  `pass` char(32) NOT NULL,
  `sid` varchar(64) NOT NULL,
  `safety` blob,
  `regtime` bigint(20) NOT NULL,
  `sidtime` bigint(20) NOT NULL,
  `acctime` bigint(20) NOT NULL,
  `info` blob,
  `mail` varchar(255) DEFAULT NULL,
  `regphone` char(11) CHARACTER SET ascii DEFAULT NULL,
  `permission` bit(8) NOT NULL DEFAULT b'0',
  `active` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hu60_addin_chat_data`
--
ALTER TABLE `hu60_addin_chat_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room` (`room`),
  ADD KEY `time` (`time`),
  ADD KEY `room_time` (`room`,`time`) USING BTREE,
  ADD KEY `room_lid` (`room`,`lid`) USING BTREE;

--
-- Indexes for table `hu60_addin_chat_list`
--
ALTER TABLE `hu60_addin_chat_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ztime` (`ztime`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `hu60_bbs_forum_meta`
--
ALTER TABLE `hu60_bbs_forum_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `mtime` (`mtime`);

--
-- Indexes for table `hu60_bbs_topic_content`
--
ALTER TABLE `hu60_bbs_topic_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topic_id` (`topic_id`),
  ADD KEY `reply_id` (`reply_id`);

--
-- Indexes for table `hu60_bbs_topic_meta`
--
ALTER TABLE `hu60_bbs_topic_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ctime` (`level`,`ctime`) USING BTREE,
  ADD KEY `mtime` (`level`,`mtime`) USING BTREE,
  ADD KEY `lfctime` (`level`,`forum_id`,`ctime`),
  ADD KEY `lfmtime` (`level`,`forum_id`,`mtime`);

--
-- Indexes for table `hu60_book_chapter`
--
ALTER TABLE `hu60_book_chapter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_chapter` (`book_id`,`chapter`,`version`) USING BTREE;

--
-- Indexes for table `hu60_book_meta`
--
ALTER TABLE `hu60_book_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mtime` (`mtime`);

--
-- Indexes for table `hu60_msg`
--
ALTER TABLE `hu60_msg`
  ADD PRIMARY KEY (`id`),
  ADD KEY `touid` (`type`,`touid`,`isread`,`ctime`) USING BTREE,
  ADD KEY `byuid` (`type`,`byuid`,`isread`,`ctime`) USING BTREE,
  ADD KEY `chat` (`type`,`touid`,`byuid`,`ctime`);

--
-- Indexes for table `hu60_speedtest`
--
ALTER TABLE `hu60_speedtest`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tag` (`tag`);

--
-- Indexes for table `hu60_token`
--
ALTER TABLE `hu60_token`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Indexes for table `hu60_user`
--
ALTER TABLE `hu60_user`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `sid` (`sid`),
  ADD UNIQUE KEY `main` (`mail`),
  ADD UNIQUE KEY `mail` (`mail`),
  ADD UNIQUE KEY `regphone` (`regphone`);

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
-- 使用表AUTO_INCREMENT `hu60_user`
--
ALTER TABLE `hu60_user`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
