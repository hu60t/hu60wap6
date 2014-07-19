CREATE TABLE `hu60_bbs_forum_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `mtime` bigint(20) DEFAULT NULL,
  `notopic` bit(1) DEFAULT b'0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `hu60_bbs_forum_topic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forum_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `ctime` bigint(20) NOT NULL,
  `mtime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `hu60_bbs_topic_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) NOT NULL,
  `ctime` bigint(20) NOT NULL,
  `mtime` bigint(20) NOT NULL,
  `content` text,
  `uid` int(11) NOT NULL,
  `reply_id` int(11) NOT NULL,
  `floor` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `hu60_bbs_topic_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `read_count` int(11) DEFAULT '0',
  `uid` int(11) NOT NULL,
  `ctime` bigint(20) NOT NULL,
  `mtime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `hu60_token` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `lifetime` bigint(20) NOT NULL,
  `token` char(32) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `hu60_user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL,
  `pass` char(32) NOT NULL,
  `sid` varchar(64) NOT NULL,
  `safety` blob,
  `regtime` bigint(20) NOT NULL,
  `sidtime` bigint(20) NOT NULL,
  `acctime` bigint(20) NOT NULL,
  `info` blob,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `sid` (`sid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `hu60_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `touid` int(11) NOT NULL,
  `byuid` int(11) NOT NULL,
  `type` int(11) DEFAULT '1',
  `isread` int(11) NOT NULL,
  `content` blob,
  `ctime` bigint(20) NOT NULL,
  `rtime` bigint(20) NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `hu60_addin_chat_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `ztime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `hu60_addin_chat_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room` varchar(32) NOT NULL,
  `lid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `uname` varchar(16) NOT NULL,
  `content` blob,
  `time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;