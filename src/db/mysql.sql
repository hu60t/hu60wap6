-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-07-12 11:24:35
-- 服务器版本： 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `hu60org`
--

-- --------------------------------------------------------

--
-- 表的结构 `hu60_addin_chat_data`
--

CREATE TABLE IF NOT EXISTS `hu60_addin_chat_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room` varchar(32) NOT NULL,
  `lid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `uname` varchar(16) NOT NULL,
  `content` blob,
  `time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_addin_chat_list`
--

CREATE TABLE IF NOT EXISTS `hu60_addin_chat_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `ztime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_bbs_forum_meta`
--

CREATE TABLE IF NOT EXISTS `hu60_bbs_forum_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `mtime` bigint(20) DEFAULT NULL,
  `notopic` bit(1) DEFAULT b'0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_bbs_forum_topic`
--

CREATE TABLE IF NOT EXISTS `hu60_bbs_forum_topic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forum_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `ctime` bigint(20) NOT NULL,
  `mtime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_bbs_topic_content`
--

CREATE TABLE IF NOT EXISTS `hu60_bbs_topic_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) NOT NULL,
  `ctime` bigint(20) NOT NULL,
  `mtime` bigint(20) NOT NULL,
  `content` mediumblob,
  `uid` int(11) NOT NULL,
  `reply_id` int(11) NOT NULL,
  `floor` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_bbs_topic_meta`
--

CREATE TABLE IF NOT EXISTS `hu60_bbs_topic_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `title` varchar(150) CHARACTER SET utf8mb4 DEFAULT NULL,
  `read_count` int(11) DEFAULT '0',
  `uid` int(11) NOT NULL,
  `ctime` bigint(20) NOT NULL,
  `mtime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_msg`
--

CREATE TABLE IF NOT EXISTS `hu60_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `touid` int(11) NOT NULL,
  `byuid` int(11) NOT NULL,
  `type` int(11) DEFAULT '1',
  `isread` int(11) NOT NULL,
  `content` blob,
  `ctime` bigint(20) NOT NULL,
  `rtime` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_token`
--

CREATE TABLE IF NOT EXISTS `hu60_token` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `lifetime` bigint(20) NOT NULL,
  `token` char(32) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hu60_user`
--

CREATE TABLE IF NOT EXISTS `hu60_user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
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
  `permission` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `sid` (`sid`),
  UNIQUE KEY `main` (`mail`),
  UNIQUE KEY `mail` (`mail`),
  UNIQUE KEY `regphone` (`regphone`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
