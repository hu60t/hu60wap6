-- 数据库升级语句
-- 请根据实际情况使用

-- 2020-07-09
-- 把数据库引擎从 MyISAM 改为 InnoDB 以提升性能
ALTER TABLE hu60_addin_chat_data   ENGINE=InnoDB;
ALTER TABLE hu60_addin_chat_list   ENGINE=InnoDB;
ALTER TABLE hu60_bbs_forum_meta    ENGINE=InnoDB;
ALTER TABLE hu60_bbs_topic_content ENGINE=InnoDB;
ALTER TABLE hu60_bbs_topic_meta    ENGINE=InnoDB;
ALTER TABLE hu60_book_chapter      ENGINE=InnoDB;
ALTER TABLE hu60_book_meta         ENGINE=InnoDB;
ALTER TABLE hu60_friend_links      ENGINE=InnoDB;
ALTER TABLE hu60_msg               ENGINE=InnoDB;
ALTER TABLE hu60_speedtest         ENGINE=InnoDB;
ALTER TABLE hu60_token             ENGINE=InnoDB;
ALTER TABLE hu60_topic_favorites   ENGINE=InnoDB;
ALTER TABLE hu60_user              ENGINE=InnoDB;
ALTER TABLE hu60_userdata          ENGINE=InnoDB;
ALTER TABLE hu60_user_relationship ENGINE=InnoDB;

-- 2020-07-09
-- 为`hu60_userdata`添加一个`version`字段，以实现带版本更新
ALTER TABLE `hu60_userdata` ADD `version` BIGINT NOT NULL DEFAULT '1' AFTER `value`; 
