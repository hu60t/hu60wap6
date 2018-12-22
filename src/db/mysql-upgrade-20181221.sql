-- 用户关系表
-- 用于存储两位用户之间的关系
CREATE TABLE  `hu60_user_relationship` (
  `relationship_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `origin_uid` int(11) NOT NULL,
  `target_uid` int(11) NOT NULL,
  `type` tinyint NOT NULL COMMENT '关系类型'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;