SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `hu60_addin_chat_data` (
  `id` int(11) NOT NULL,
  `room` varchar(32) NOT NULL,
  `lid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `uname` varchar(16) NOT NULL,
  `content` blob,
  `time` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `hu60_addin_chat_list` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `ztime` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `hu60_bbs_forum_meta` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `mtime` bigint(20) DEFAULT NULL,
  `notopic` bit(1) DEFAULT b'0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `hu60_bbs_forum_topic` (
  `id` int(11) NOT NULL,
  `forum_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `ctime` bigint(20) NOT NULL,
  `mtime` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `hu60_bbs_topic_content` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `ctime` bigint(20) NOT NULL,
  `mtime` bigint(20) NOT NULL,
  `content` mediumblob,
  `uid` int(11) NOT NULL,
  `reply_id` int(11) NOT NULL,
  `floor` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `hu60_bbs_topic_meta` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `title` varchar(150) CHARACTER SET utf8mb4 DEFAULT NULL,
  `read_count` int(11) DEFAULT '0',
  `uid` int(11) NOT NULL,
  `ctime` bigint(20) NOT NULL,
  `mtime` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `hu60_msg` (
  `id` int(11) NOT NULL,
  `touid` int(11) NOT NULL,
  `byuid` int(11) NOT NULL,
  `type` int(11) DEFAULT '1',
  `isread` int(11) NOT NULL,
  `content` blob,
  `ctime` bigint(20) NOT NULL,
  `rtime` bigint(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `hu60_token` (
  `id` bigint(20) NOT NULL,
  `lifetime` bigint(20) NOT NULL,
  `token` char(32) NOT NULL,
  `uid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
  `regphone` char(11) CHARACTER SET ascii DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


ALTER TABLE `hu60_addin_chat_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room` (`room`),
  ADD KEY `time` (`time`),
  ADD KEY `room_time` (`room`,`time`) USING BTREE,
  ADD KEY `room_lid` (`room`,`lid`) USING BTREE;

ALTER TABLE `hu60_addin_chat_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ztime` (`ztime`),
  ADD KEY `name` (`name`);

ALTER TABLE `hu60_bbs_forum_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `mtime` (`mtime`);

ALTER TABLE `hu60_bbs_forum_topic`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `forum_topic` (`forum_id`,`topic_id`) USING BTREE,
  ADD KEY `forum_id` (`forum_id`),
  ADD KEY `ctime` (`ctime`),
  ADD KEY `mtime` (`mtime`),
  ADD KEY `id_ctime` (`forum_id`,`ctime`) USING BTREE,
  ADD KEY `id_mtime` (`forum_id`,`mtime`) USING BTREE;

ALTER TABLE `hu60_bbs_topic_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topic_id` (`topic_id`),
  ADD KEY `reply_id` (`reply_id`);

ALTER TABLE `hu60_bbs_topic_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ctime` (`ctime`),
  ADD KEY `mtime` (`mtime`);

-- 感谢love封尘的提醒，已为长达572222条记录的内信+at消息表添加索引 --
ALTER TABLE `hu60_msg`
  ADD PRIMARY KEY (`id`),
  ADD KEY `touid` (`touid`,`type`,`isread`),
  ADD KEY `byuid` (`byuid`,`type`,`isread`);

ALTER TABLE `hu60_token`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

ALTER TABLE `hu60_user`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `sid` (`sid`),
  ADD UNIQUE KEY `main` (`mail`),
  ADD UNIQUE KEY `mail` (`mail`),
  ADD UNIQUE KEY `regphone` (`regphone`);


ALTER TABLE `hu60_addin_chat_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `hu60_addin_chat_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `hu60_bbs_forum_meta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `hu60_bbs_forum_topic`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `hu60_bbs_topic_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `hu60_bbs_topic_meta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `hu60_msg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `hu60_token`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `hu60_user`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
