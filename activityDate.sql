-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： mysql
-- 生成日期： 2020-03-26 03:20:57
-- 服务器版本： 5.6.47
-- PHP 版本： 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `activityDate`
--

-- --------------------------------------------------------

--
-- 表的结构 `activity`
--

CREATE TABLE `activity` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(256) COLLATE utf8_bin NOT NULL,
  `organization` int(10) UNSIGNED NOT NULL COMMENT '所属组织',
  `createtime` datetime NOT NULL COMMENT '创建时间',
  `publictime` datetime NOT NULL COMMENT '发布时间',
  `signstarttime` datetime NOT NULL COMMENT '开始报名时间',
  `signendtime` datetime NOT NULL COMMENT '结束报名时间',
  `starttime` datetime NOT NULL COMMENT '开始时间',
  `endtime` datetime NOT NULL COMMENT '结束时间',
  `classification` varchar(32) COLLATE utf8_bin NOT NULL COMMENT '分类',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `description` text COLLATE utf8_bin COMMENT '活动描述',
  `content` mediumtext COLLATE utf8_bin COMMENT '活动具体内容',
  `file` text COLLATE utf8_bin COMMENT '具体内容上传的文件'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='已创建的活动的信息表';


--
-- 表的结构 `activityoption`
--

CREATE TABLE `activityoption` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(256) COLLATE utf8_bin NOT NULL,
  `activity` int(10) UNSIGNED NOT NULL,
  `value` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='活动设置表';


--
-- 表的结构 `classification`
--

CREATE TABLE `classification` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(256) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- 转存表中的数据 `classification`
--

INSERT INTO `classification` (`id`, `name`) VALUES
(1, '默认分类'),
(2, '示例分类');

-- --------------------------------------------------------

--
-- 表的结构 `file`
--

CREATE TABLE `file` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` varchar(1024) COLLATE utf8_bin NOT NULL,
  `name` varchar(256) COLLATE utf8_bin NOT NULL,
  `path` varchar(256) COLLATE utf8_bin NOT NULL,
  `size` int(10) UNSIGNED NOT NULL COMMENT '大小',
  `sort` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



--
-- 表的结构 `op`
--

CREATE TABLE `op` (
  `id` int(10) UNSIGNED NOT NULL,
  `organization` int(11) NOT NULL,
  `name` varchar(256) COLLATE utf8_bin NOT NULL,
  `op` int(11) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- 转存表中的数据 `op`
--

INSERT INTO `op` (`id`, `organization`, `name`, `op`, `sort`) VALUES
(1, 1, 'createActivity', 3, 0),
(2, 1, 'createPeople', 3, 0),
(3, 1, 'delPeople', 3, 0);

-- --------------------------------------------------------

--
-- 表的结构 `organization`
--

CREATE TABLE `organization` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(256) COLLATE utf8_bin NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='组织信息表';

--
-- 转存表中的数据 `organization`
--

INSERT INTO `organization` (`id`, `name`, `sort`) VALUES
(1, '示例组织', 0);

-- --------------------------------------------------------

--
-- 表的结构 `people`
--

CREATE TABLE `people` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(256) COLLATE utf8_bin NOT NULL,
  `classname` varchar(256) COLLATE utf8_bin NOT NULL COMMENT '昵称',
  `passwd` varchar(16) COLLATE utf8_bin NOT NULL,
  `organizationID` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `organization` int(10) UNSIGNED NOT NULL,
  `op` int(11) NOT NULL DEFAULT '4',
  `credit` int(11) DEFAULT '100'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='已注册人员信息表';

--
-- 转存表中的数据 `people`
--

INSERT INTO `people` (`id`, `name`, `classname`, `passwd`, `organizationID`, `organization`, `op`, `credit`) VALUES
(1, 'root', 'root', '123456', '20190310106', 1, 0, 100);

-- --------------------------------------------------------

--
-- 表的结构 `peopleandactivity`
--

CREATE TABLE `peopleandactivity` (
  `id` int(10) UNSIGNED NOT NULL,
  `peopleid` int(10) UNSIGNED NOT NULL COMMENT '人员id',
  `activityid` int(10) UNSIGNED NOT NULL COMMENT '活动id',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '状态0为未报名1为已报名2为已录取3为已到场'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='人员报名表';



--
-- 转储表的索引
--

--
-- 表的索引 `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `activityoption`
--
ALTER TABLE `activityoption`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `classification`
--
ALTER TABLE `classification`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `file`
--
ALTER TABLE `file`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `op`
--
ALTER TABLE `op`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `organization`
--
ALTER TABLE `organization`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `people`
--
ALTER TABLE `people`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `peopleandactivity`
--
ALTER TABLE `peopleandactivity`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `activity`
--
ALTER TABLE `activity`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- 使用表AUTO_INCREMENT `activityoption`
--
ALTER TABLE `activityoption`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- 使用表AUTO_INCREMENT `classification`
--
ALTER TABLE `classification`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `file`
--
ALTER TABLE `file`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- 使用表AUTO_INCREMENT `op`
--
ALTER TABLE `op`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- 使用表AUTO_INCREMENT `organization`
--
ALTER TABLE `organization`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `people`
--
ALTER TABLE `people`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- 使用表AUTO_INCREMENT `peopleandactivity`
--
ALTER TABLE `peopleandactivity`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
