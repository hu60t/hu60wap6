#创建用户表#
CREATE TABLE `hu60_user` ( `uid` int PRIMARY KEY AUTO_INCREMENT, `name` varchar(16) NOT NULL UNIQUE, `pass` char(32) NOT NULL,`sid` varchar(64) NOT NULL UNIQUE, `safety` blob, `regtime` bigint NOT NULL, `sidtime` bigint NOT NULL, `acctime` bigint NOT NULL, `info` blob) ENGINE=MyISAM DEFAULT CHARSET=utf8;
#创建论坛表#
create table hu60_bbs_forum_meta(
  id int primary key auto_increment,
  parent_id int not null,
  name varchar(50) not null
)
CREATE TABLE `hu60_bbs_forum_meta` (   `id` int(11) NOT NULL AUTO_INCREMENT,   `parent_id` int(11) NOT NULL,   `name` varchar(50) NOT NULL,   PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8