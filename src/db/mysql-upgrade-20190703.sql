-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: 2019-07-03 14:10:49
-- 服务器版本： 10.3.13-MariaDB-1-log
-- PHP Version: 7.3.3-1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `hu60`
--

-- --------------------------------------------------------

--
-- 表的结构 `hu60_userdata`
--

CREATE TABLE `hu60_userdata` (
  `uid` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hu60_userdata`
--
ALTER TABLE `hu60_userdata`
  ADD PRIMARY KEY (`uid`,`key`);

