-- 数据库升级语句
-- 请根据实际情况使用

-- 2021-02-21
-- 添加访问权限字段
ALTER TABLE `hu60_user` ADD `access` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `permission`;
ALTER TABLE `hu60_addin_chat_list` ADD `access` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `ztime`;
ALTER TABLE `hu60_bbs_forum_meta` ADD `access` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `notopic`;
ALTER TABLE `hu60_bbs_topic_meta` ADD `access` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `locked`;
ALTER TABLE `hu60_bbs_topic_content` ADD `access` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `locked`;

-- 把 bit 类型转换成 int 类型，避免以后遇到各种麻烦
ALTER TABLE `hu60_user` CHANGE `permission` `permission` INT UNSIGNED NOT NULL DEFAULT '0';

-- 同步版块访问权限到帖子
UPDATE hu60_bbs_topic_meta t INNER JOIN hu60_bbs_forum_meta f ON t.forum_id = f.id SET t.access=f.access;
UPDATE hu60_bbs_topic_content c INNER JOIN hu60_bbs_topic_meta t ON c.topic_id = t.id SET c.access=t.access;
