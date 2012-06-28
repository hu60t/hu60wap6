#创建用户表#
CREATE TABLE `hu60_user` ( `uid` int PRIMARY KEY AUTO_INCREMENT, `name` varchar(16) NOT NULL UNIQUE, `pass` char(32) NOT NULL,`sid` varchar(64) NOT NULL UNIQUE, `safety` blob, `regtime` int NOT NULL, `sidtime` int NOT NULL, `acctime` int NOT NULL, `info` blob) ENGINE=MyISAM DEFAULT CHARSET=utf8;
