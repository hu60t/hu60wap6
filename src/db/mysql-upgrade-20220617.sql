-- 数据库升级语句
-- 请根据实际情况使用

-- 2022-06-17
-- 网页插件管理器改版，新增`webplug`表

CREATE TABLE `hu60_webplug` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `load_order` tinyint(11) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `hu60_webplug`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid_enabled_loadorder` (`uid`,`enabled`,`load_order`) USING BTREE,
  ADD KEY `uid_loadorder` (`uid`,`load_order`);

ALTER TABLE `hu60_webplug`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
