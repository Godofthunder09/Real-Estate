-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 02, 2024 at 03:21 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lrsdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbladmin`
--

CREATE TABLE `tbladmin` (
  `ID` int(10) NOT NULL,
  `AdminName` varchar(120) DEFAULT NULL,
  `UserName` varchar(120) DEFAULT NULL,
  `MobileNumber` bigint(10) DEFAULT NULL,
  `Email` varchar(120) DEFAULT NULL,
  `Password` varchar(120) DEFAULT NULL,
  `AdminRegdate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`ID`, `AdminName`, `UserName`, `MobileNumber`, `Email`, `Password`, `AdminRegdate`) VALUES
(1, 'Admin', 'admin', 8989898981, 'admin@gmail.com', 'f925916e2754e5e03f75dd58a5733251', '2024-11-27 07:15:32');

-- --------------------------------------------------------

--
-- Table structure for table `tblpage`
--

CREATE TABLE `tblpage` (
  `ID` int(10) NOT NULL,
  `PageType` varchar(120) DEFAULT NULL,
  `PageTitle` mediumtext DEFAULT NULL,
  `PageDescription` mediumtext DEFAULT NULL,
  `Email` varchar(120) DEFAULT NULL,
  `MobileNumber` bigint(10) DEFAULT NULL,
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblpage`
--

INSERT INTO `tblpage` (`ID`, `PageType`, `PageTitle`, `PageDescription`, `Email`, `MobileNumber`, `UpdationDate`) VALUES
(1, 'aboutus', 'About Us', '<div><span style=\"font-family: Poppins, sans-serif;\"><b style=\"\"><font color=\"#003399\">Land Record system used to maintain the of land and properties digitally.</font></b></span></div><div><br></div>', 'info@gmsil.com', 8989899898, '2024-12-02 13:54:55'),
(2, 'contactus', 'Contact Us', 'D-204, Hole Town South West,<div>Delhi-110096,India</div>', 'support@gmail.com', 8529631235, '2024-11-27 06:59:55');

-- --------------------------------------------------------

--
-- Table structure for table `tblproperty`
--

CREATE TABLE `tblproperty` (
  `ID` int(5) NOT NULL,
  `PropertyNumber` int(10) DEFAULT NULL,
  `PropertyID` int(5) DEFAULT NULL,
  `LandSubtype` varchar(250) DEFAULT NULL,
  `FirstPartyname` varchar(250) DEFAULT NULL,
  `FPIDType` varchar(250) DEFAULT NULL,
  `FPIDNumber` varchar(100) DEFAULT NULL,
  `FPMobilenumber` bigint(11) DEFAULT NULL,
  `FPAddress` mediumtext DEFAULT NULL,
  `SPName` varchar(250) DEFAULT NULL,
  `SPIDType` varchar(100) DEFAULT NULL,
  `SPIDNumber` varchar(250) DEFAULT NULL,
  `SPMobilenumber` bigint(11) DEFAULT NULL,
  `SPAddress` mediumtext DEFAULT NULL,
  `Landarea` varchar(250) DEFAULT NULL,
  `firstwitnessname` varchar(250) DEFAULT NULL,
  `fwitidtype` varchar(250) DEFAULT NULL,
  `fwitidnumber` varchar(250) DEFAULT NULL,
  `fwitmobnumber` bigint(10) DEFAULT NULL,
  `switname` varchar(250) DEFAULT NULL,
  `switidtype` varchar(250) DEFAULT NULL,
  `switidnumber` varchar(250) DEFAULT NULL,
  `switmobnumber` bigint(11) DEFAULT NULL,
  `fpartypic` varchar(250) DEFAULT NULL,
  `secpartypic` varchar(250) DEFAULT NULL,
  `landmappic` varchar(250) DEFAULT NULL,
  `landfirstpic` varchar(250) DEFAULT NULL,
  `landsecpic` varchar(250) DEFAULT NULL,
  `fwitpic` varchar(250) DEFAULT NULL,
  `secwitpic` varchar(250) DEFAULT NULL,
  `CreationDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblproperty`
--

INSERT INTO `tblproperty` (`ID`, `PropertyNumber`, `PropertyID`, `LandSubtype`, `FirstPartyname`, `FPIDType`, `FPIDNumber`, `FPMobilenumber`, `FPAddress`, `SPName`, `SPIDType`, `SPIDNumber`, `SPMobilenumber`, `SPAddress`, `Landarea`, `firstwitnessname`, `fwitidtype`, `fwitidnumber`, `fwitmobnumber`, `switname`, `switidtype`, `switidnumber`, `switmobnumber`, `fpartypic`, `secpartypic`, `landmappic`, `landfirstpic`, `landsecpic`, `fwitpic`, `secwitpic`, `CreationDate`) VALUES
(1, 434322908, 2, 'Shop', 'Mahima Tyagi', 'Voter ID', 'HJK1234', 7979878978, 'O-908, GHU, Block-7', 'Rohan Tyagi', 'Voter ID', 'KII0875675444', 7465479864, 'K-909, JKjluio, GHjgjh', '1850 squareft', 'Heera Devi', 'Voter ID', 'LKJ-879877', 9796497879, 'Mohan Tyagi', 'Voter ID', 'GHUU-0987', 4654798797, '89a741ac94dbf74e0a4ad7931d0f06311732686641.png', 'c651c75dfccc0b3bbd71755e7b8ac7241732686921.png', '7fdc1a630c238af0815181f9faa190f51732507022.jpg', '6516766649a3bafb4d270a9f61fe4ff41732687621jpeg', '590649ac8814f2be004b14adbc9baafb1732688412jpeg', '89a741ac94dbf74e0a4ad7931d0f06311732507022.png', 'b9aa7eb70cf940abaceb8532867deb431732690212.png', '2024-11-25 03:57:02'),
(2, 903195779, 3, 'Shop', 'Amit kumar', 'Passport', 'HSHG324234', 145236478, 'A 10 Gali no4 Rjanagr ext Ghazibad', 'Ajay Singh', 'Aadhar', '14563214562', 7418529632, 'A 123 XYZ Apartment New Delhi110001', '17000 sqft', 'Rahul', 'Driving Licence', '435435', 5435345345, 'Garims', 'Driving Licence', '235235235', 34545646, '27a2d64d07fdc53db9c4fc1a4addb0e01733147642.png', '9633bdc271363bb6674c85b86214771a1733147642jpeg', 'da9017da9ea00144dcdde6f5c1135fb11733147642.jpg', '387848d58032d0500cb34af4b21c61791733147642.jpg', 'cf65cc0ca2e46bcec32acb1d9fa6af671733147642.jpg', '4f11c4a023f29c528f91b9ea7e4b6d011733147642.png', '30ab613af6f4a62713c6d98615fec4921733147642jpeg', '2024-12-02 13:54:02');

-- --------------------------------------------------------

--
-- Table structure for table `tblpropertytype`
--

CREATE TABLE `tblpropertytype` (
  `ID` int(10) NOT NULL,
  `PropertType` varchar(120) DEFAULT NULL,
  `EnterDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblpropertytype`
--

INSERT INTO `tblpropertytype` (`ID`, `PropertType`, `EnterDate`) VALUES
(1, 'Agriculture', '2024-11-21 03:45:45'),
(2, 'Residential', '2024-11-21 03:45:56'),
(3, 'Commercials', '2024-11-21 03:46:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblpage`
--
ALTER TABLE `tblpage`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblproperty`
--
ALTER TABLE `tblproperty`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblpropertytype`
--
ALTER TABLE `tblpropertytype`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblpage`
--
ALTER TABLE `tblpage`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblproperty`
--
ALTER TABLE `tblproperty`
  MODIFY `ID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblpropertytype`
--
ALTER TABLE `tblpropertytype`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
