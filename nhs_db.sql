-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 03, 2018 at 02:11 PM
-- Server version: 8.0.11
-- PHP Version: 7.2.7-1+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nhs_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendanceCodes`
--

CREATE TABLE `attendanceCodes` (
  `code` varchar(8) NOT NULL,
  `authenticationTime` datetime DEFAULT NULL,
  `meetingID` int(10) UNSIGNED NOT NULL,
  `createdBy` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `clubMembers`
--

CREATE TABLE `clubMembers` (
  `clubMemberID` int(10) UNSIGNED NOT NULL,
  `memberID` int(10) UNSIGNED NOT NULL,
  `clubID` int(10) UNSIGNED NOT NULL,
  `role` varchar(7) NOT NULL DEFAULT 'Member',
  `position` varchar(50) DEFAULT NULL,
  `joinedDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `clubs`
--

CREATE TABLE `clubs` (
  `clubID` int(10) UNSIGNED NOT NULL,
  `clubName` varchar(75) NOT NULL,
  `abbreviation` varchar(10) NOT NULL,
  `organizationType` varchar(15) NOT NULL,
  `trackService` tinyint(1) NOT NULL DEFAULT '0',
  `createdDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `meetingAttendees`
--

CREATE TABLE `meetingAttendees` (
  `meetingAttendeeID` int(10) UNSIGNED NOT NULL,
  `meetingID` int(10) UNSIGNED NOT NULL,
  `memberID` int(10) UNSIGNED NOT NULL,
  `attendanceTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attendanceMethod` varchar(25) NOT NULL,
  `signedInBy` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `meetings`
--

CREATE TABLE `meetings` (
  `meetingID` int(10) UNSIGNED NOT NULL,
  `clubID` int(10) UNSIGNED NOT NULL,
  `meetingName` varchar(50) NOT NULL,
  `meetingDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `memberID` int(10) UNSIGNED NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `graduating` int(10) UNSIGNED NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(60) DEFAULT NULL,
  `isAdmin` tinyint(1) NOT NULL DEFAULT '0',
  `createdDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `serviceEntries`
--

CREATE TABLE `serviceEntries` (
  `serviceEntryID` int(10) UNSIGNED NOT NULL,
  `clubMemberID` int(10) UNSIGNED NOT NULL,
  `serviceOpportunityID` int(10) UNSIGNED NOT NULL,
  `date` datetime NOT NULL,
  `hours` decimal(5,2) UNSIGNED NOT NULL,
  `officerID` int(10) UNSIGNED NOT NULL,
  `createdDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `serviceOpportunities`
--

CREATE TABLE `serviceOpportunities` (
  `serviceOpportunityID` int(10) UNSIGNED NOT NULL,
  `serviceType` varchar(15) NOT NULL,
  `serviceName` varchar(50) NOT NULL,
  `serviceDescription` text NOT NULL,
  `contactName` varchar(50) NOT NULL,
  `contactPhone` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(32) NOT NULL,
  `access` int(10) UNSIGNED DEFAULT NULL,
  `data` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendanceCodes`
--
ALTER TABLE `attendanceCodes`
  ADD PRIMARY KEY (`code`),
  ADD KEY `createdBy` (`createdBy`),
  ADD KEY `meetingID` (`meetingID`);

--
-- Indexes for table `clubMembers`
--
ALTER TABLE `clubMembers`
  ADD PRIMARY KEY (`clubMemberID`),
  ADD UNIQUE KEY `uniqueKey` (`memberID`,`clubID`) USING BTREE,
  ADD KEY `clubID` (`clubID`);

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`clubID`);

--
-- Indexes for table `meetingAttendees`
--
ALTER TABLE `meetingAttendees`
  ADD PRIMARY KEY (`meetingAttendeeID`),
  ADD UNIQUE KEY `uniqueKey` (`meetingID`,`memberID`) USING BTREE,
  ADD KEY `signedInBy` (`signedInBy`),
  ADD KEY `memberID` (`memberID`) USING BTREE;

--
-- Indexes for table `meetings`
--
ALTER TABLE `meetings`
  ADD PRIMARY KEY (`meetingID`),
  ADD KEY `clubID` (`clubID`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`memberID`),
  ADD UNIQUE KEY `uniqueKey` (`email`,`graduating`) USING BTREE;

--
-- Indexes for table `serviceEntries`
--
ALTER TABLE `serviceEntries`
  ADD PRIMARY KEY (`serviceEntryID`),
  ADD KEY `serviceOpportunityID` (`serviceOpportunityID`),
  ADD KEY `clubMemberID` (`clubMemberID`),
  ADD KEY `officerID` (`officerID`);

--
-- Indexes for table `serviceOpportunities`
--
ALTER TABLE `serviceOpportunities`
  ADD PRIMARY KEY (`serviceOpportunityID`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clubMembers`
--
ALTER TABLE `clubMembers`
  MODIFY `clubMemberID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=441;

--
-- AUTO_INCREMENT for table `clubs`
--
ALTER TABLE `clubs`
  MODIFY `clubID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `meetingAttendees`
--
ALTER TABLE `meetingAttendees`
  MODIFY `meetingAttendeeID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3508;

--
-- AUTO_INCREMENT for table `meetings`
--
ALTER TABLE `meetings`
  MODIFY `meetingID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `memberID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=376;

--
-- AUTO_INCREMENT for table `serviceEntries`
--
ALTER TABLE `serviceEntries`
  MODIFY `serviceEntryID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=636;

--
-- AUTO_INCREMENT for table `serviceOpportunities`
--
ALTER TABLE `serviceOpportunities`
  MODIFY `serviceOpportunityID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendanceCodes`
--
ALTER TABLE `attendanceCodes`
  ADD CONSTRAINT `attendanceCodes_ibfk_1` FOREIGN KEY (`meetingID`) REFERENCES `meetings` (`meetingID`),
  ADD CONSTRAINT `attendanceCodes_ibfk_2` FOREIGN KEY (`createdBy`) REFERENCES `members` (`memberID`);

--
-- Constraints for table `clubMembers`
--
ALTER TABLE `clubMembers`
  ADD CONSTRAINT `clubMembers_ibfk_1` FOREIGN KEY (`memberID`) REFERENCES `members` (`memberID`),
  ADD CONSTRAINT `clubMembers_ibfk_2` FOREIGN KEY (`clubID`) REFERENCES `clubs` (`clubID`);

--
-- Constraints for table `meetingAttendees`
--
ALTER TABLE `meetingAttendees`
  ADD CONSTRAINT `meetingAttendees_ibfk_1` FOREIGN KEY (`meetingID`) REFERENCES `meetings` (`meetingID`),
  ADD CONSTRAINT `meetingAttendees_ibfk_2` FOREIGN KEY (`memberID`) REFERENCES `members` (`memberID`),
  ADD CONSTRAINT `meetingAttendees_ibfk_3` FOREIGN KEY (`signedInBy`) REFERENCES `members` (`memberID`);

--
-- Constraints for table `meetings`
--
ALTER TABLE `meetings`
  ADD CONSTRAINT `meetings_ibfk_1` FOREIGN KEY (`clubID`) REFERENCES `clubs` (`clubID`);

--
-- Constraints for table `serviceEntries`
--
ALTER TABLE `serviceEntries`
  ADD CONSTRAINT `serviceEntries_ibfk_1` FOREIGN KEY (`serviceOpportunityID`) REFERENCES `serviceOpportunities` (`serviceOpportunityID`),
  ADD CONSTRAINT `serviceEntries_ibfk_2` FOREIGN KEY (`clubMemberID`) REFERENCES `clubMembers` (`clubMemberID`),
  ADD CONSTRAINT `serviceEntries_ibfk_3` FOREIGN KEY (`officerID`) REFERENCES `members` (`memberID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
