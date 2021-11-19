-- Wine游戏助手特定功能的数据库，可以不必导入

-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2021-11-20 02:41:17
-- 服务器版本： 10.5.12-MariaDB-0+deb11u1-log
-- PHP 版本： 8.0.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- 数据库： `hu60`
--

-- --------------------------------------------------------

--
-- 表的结构 `hu60_lutris_release`
--

CREATE TABLE `hu60_lutris_release` (
  `project` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `ctime` bigint(20) NOT NULL,
  `mtime` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- 转储表的索引
--

--
-- 表的索引 `hu60_lutris_release`
--
ALTER TABLE `hu60_lutris_release`
  ADD PRIMARY KEY (`project`,`version`),
  ADD KEY `project_ctime` (`project`,`ctime`) USING BTREE;
COMMIT;
