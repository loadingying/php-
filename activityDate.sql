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
-- 转存表中的数据 `activity`
--

INSERT INTO `activity` (`id`, `name`, `organization`, `createtime`, `publictime`, `signstarttime`, `signendtime`, `starttime`, `endtime`, `classification`, `sort`, `description`, `content`, `file`) VALUES
(1, '示例活动', 1, '2020-03-17 15:43:25', '2020-03-19 15:53:40', '2020-03-20 15:41:25', '2020-03-25 15:41:25', '2020-03-25 15:41:25', '2020-03-27 15:41:25', '示例分类', 0, '这是示例活动的描述。', '这是示例活动的内容。', NULL),
(6, '第一个活动', 1, '2020-03-25 11:09:13', '2020-03-25 00:59:00', '2020-03-26 00:00:00', '2020-03-26 00:00:00', '2020-03-27 00:00:00', '2020-03-27 23:59:00', '默认分类', 0, '第一个', '活动内容', '5;6;'),
(18, '活动', 1, '2020-03-25 12:55:33', '2020-03-25 00:00:00', '2020-03-25 00:00:00', '2020-03-25 00:00:00', '2020-03-25 00:00:00', '2020-03-29 00:00:00', '默认分类', 0, '', '', '25;27;28;29;30;31;');

-- --------------------------------------------------------

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
-- 转存表中的数据 `activityoption`
--

INSERT INTO `activityoption` (`id`, `name`, `activity`, `value`) VALUES
(1, 'signpeople', 1, 0),
(2, 'signpeople', 6, 1),
(14, 'signpeople', 18, 1),
(15, 'startpasswd', 18, 551885),
(16, 'startpasswd', 1, 459949);

-- --------------------------------------------------------

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
-- 转存表中的数据 `file`
--

INSERT INTO `file` (`id`, `type`, `name`, `path`, `size`, `sort`) VALUES
(5, 'application/msword', '期末考试重点与答案大全（稳过版）.doc', '../upload/202003/11034173821865913822.doc', 286720, 0),
(6, 'application/pdf', '期末考试重点与答案大全（稳过版）.pdf', '../upload/202003/1770413981913817217.pdf', 65285, 0),
(25, 'application/vnd.openxmlformats-officedocument.presentationml.presentation', '大学体育-发展体能PPT.pptx', '../upload/202003/1943307644594133961.pptx', 422955, 0),
(27, 'image/png', '76b5129e32dae3805efb16a6d5bc60f.png', '../upload/202003/1926780343704205688.png', 139645, 0),
(28, 'application/x-zip-compressed', 'aliyun-php-sdk-afs-20180112-2.zip', '../upload/202003/12044540591119910501.zip', 103991, 0),
(29, 'application/x-zip-compressed', '仿站小工具.zip', '../upload/202003/92062819443490369.zip', 1042262, 0),
(30, 'application/octet-stream', '汇编软件.rar', '../upload/202003/13950280991250768854.rar', 1556741, 0),
(31, 'application/octet-stream', 'gravityWall.rar', '../upload/202003/2005086150453308683.rar', 10595949, 0);

-- --------------------------------------------------------

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
(1, 'root', '华', '123456', '20190310106', 1, 0, 100),
(8, 'www', 'www', '123456', '020405', 1, 2, 100),
(11, 'load', 'load', '123456', '123456', 1, 3, 100),
(13, 'xxx', '成员', '123456', '9568', 1, 4, 100);

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
-- 转存表中的数据 `peopleandactivity`
--

INSERT INTO `peopleandactivity` (`id`, `peopleid`, `activityid`, `sort`, `status`) VALUES
(1, 1, 6, 0, 0),
(2, 8, 6, 0, 0),
(3, 11, 6, 0, 0),
(5, 13, 6, 0, 0),
(7, 13, 1, 0, 0),
(8, 1, 1, 0, 2),
(30, 1, 18, 0, 3),
(31, 8, 18, 0, 3),
(32, 11, 18, 0, 3),
(34, 13, 18, 0, 3);

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
