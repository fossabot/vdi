-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 12, 2018 at 11:43 PM
-- Server version: 5.7.21-0ubuntu0.16.04.1
-- PHP Version: 7.0.28-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vdi_dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `ait_request_log`
--

DROP TABLE IF EXISTS `ait_request_log`;
CREATE TABLE `ait_request_log` (
  `id` int(11) NOT NULL,
  `date_request` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_required_by` timestamp NULL DEFAULT NULL,
  `request_by` int(11) NOT NULL COMMENT 'who has made this request?',
  `ait_table_list_id` int(11) NOT NULL,
  `completed` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Log to request additional information for a vehicle';

-- --------------------------------------------------------

--
-- Table structure for table `ait_table_list`
--

DROP TABLE IF EXISTS `ait_table_list`;
CREATE TABLE `ait_table_list` (
  `id` int(11) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `human_readable_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='List of additional information tables to select from';

-- --------------------------------------------------------

--
-- Table structure for table `ait_tbl_drugs_list`
--

DROP TABLE IF EXISTS `ait_tbl_drugs_list`;
CREATE TABLE `ait_tbl_drugs_list` (
  `id` int(11) NOT NULL,
  `drug_name` varchar(64) NOT NULL,
  `qty` int(11) NOT NULL COMMENT 'expected qty of this drug in any given drugs bag',
  `controlled` int(11) NOT NULL DEFAULT '0' COMMENT 'is this a controlled drug? 1 = yes'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Additional information table';

-- --------------------------------------------------------

--
-- Table structure for table `ait_vehicle_list`
--

DROP TABLE IF EXISTS `ait_vehicle_list`;
CREATE TABLE `ait_vehicle_list` (
  `id` int(11) NOT NULL,
  `ait_request_log_id` int(11) NOT NULL,
  `vehicle_list_id` int(11) NOT NULL,
  `completed` int(11) DEFAULT NULL,
  `dtg_completed` timestamp NULL DEFAULT NULL,
  `completed_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='list of vehicles required to completed the action specified';

-- --------------------------------------------------------

--
-- Table structure for table `crip_shifts`
--

DROP TABLE IF EXISTS `crip_shifts`;
CREATE TABLE `crip_shifts` (
  `id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `description` varchar(64) NOT NULL,
  `hidden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `epcr_actions`
--

DROP TABLE IF EXISTS `epcr_actions`;
CREATE TABLE `epcr_actions` (
  `id` int(11) NOT NULL,
  `action` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `epcr_actions`
--

INSERT INTO `epcr_actions` (`id`, `action`) VALUES
(1, 'Assigned'),
(2, 'Faulty - Away for repair'),
(4, 'Faulty - Waiting collection'),
(7, 'Spare');

-- --------------------------------------------------------

--
-- Table structure for table `epcr_batteries`
--

DROP TABLE IF EXISTS `epcr_batteries`;
CREATE TABLE `epcr_batteries` (
  `id` int(11) NOT NULL,
  `serial` varchar(16) NOT NULL,
  `model_number` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `epcr_incidents`
--

DROP TABLE IF EXISTS `epcr_incidents`;
CREATE TABLE `epcr_incidents` (
  `id` int(11) NOT NULL,
  `incident_ref` varchar(32) NOT NULL,
  `comments` longtext NOT NULL,
  `resolution` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `epcr_laptops`
--

DROP TABLE IF EXISTS `epcr_laptops`;
CREATE TABLE `epcr_laptops` (
  `id` int(11) NOT NULL,
  `asset_tag` varchar(8) NOT NULL,
  `version` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `epcr_log`
--

DROP TABLE IF EXISTS `epcr_log`;
CREATE TABLE `epcr_log` (
  `id` int(11) NOT NULL,
  `laptop_id` int(11) DEFAULT NULL,
  `battery_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `action_id` int(11) NOT NULL,
  `incident_id` int(11) DEFAULT NULL,
  `dtg` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `archive_flag` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `help_text`
--

DROP TABLE IF EXISTS `help_text`;
CREATE TABLE `help_text` (
  `id` int(11) NOT NULL,
  `base64_code` varchar(128) NOT NULL,
  `title` varchar(128) NOT NULL,
  `help_text` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `help_text`
--

INSERT INTO `help_text` (`id`, `base64_code`, `title`, `help_text`) VALUES
(1, 'L3ZkaS8=', 'Vehicle Board', '<p>This screen shows all of the vehicles in the locality including radio numbers, service and MOT dates. You can use the search bar at the top of the page to look for a specific vehicle.</p>\r\n\r\n<p>To carry out a VDI, click on the coloured button for that vehicle. The colour relates to the vehicle availability.\r\n<h5><span class="badge badge-success">Vehicle available</span></h5>\r\n<h5><span class="badge badge-warning">Vehicle available but there is an advisory note</span></h5>\r\n<h5><span class="badge badge-danger">Vehicle off the road</span></h5>\r\n</p>\r\n<hr>\r\n<p>Users with additional access rights will have some extra options appear when they click on the vehicle button. These options are:\r\n<ul>\r\n<li>Carry Out VDI - allows you to complete a VDI for the vehicle</li>\r\n<li>VDI History - allows you view a complete VDI history for the vehicle</li>\r\n<li>Add Note - allows you to add a note for this vehicle which is visible to all users on this screen. You will also be able to set the vehicle status to one of the three options shown earlier.</li>\r\n<ul>\r\n</p>'),
(2, 'L3ZkaS92ZGkucGhw', 'VDI', '<p>\r\nCheck the vehicle details at the top of the page and then work your way down completing all of the boxes as you go.\r\n</p>\r\n<p>\r\nAll inspection points are set to FAIL by default. In this state you will need to enter a description of the fault below. If you mark an inspection point as PASSED, the fault description box for that point will be hidden with no input required.\r\n</p>\r\n<p>\r\n<lu>\r\n<li><button class="btn btn-danger" type="button"><i class="fas fa-times"></i></button> Fail</li>\r\n<li><button class="btn btn-success" type="button"><i class="fas fa-check"></i></button> Pass</li>\r\n</lu>\r\n</p>\r\n<p>Once complete, click the green submit VDI button at the bottom of the form. Any fields you have missed out will be highlighted at this point.</p>'),
(3, 'L3ZkaS92ZGktaGlzdG9yeS5waHA=', 'VDI History', '<p>This page show\'s a specific vehicle\'s VDI history. All of the inspection points are listed in alphabetical order. If you move your mouse over the inspection date at the top of a column, the name of the person who completed the VDI is shown.</p>\r\n\r\n<p>Anywhere you see a red cross in a column, you can click on it to display any comments by the inspector and/or subsequent actions taken.</p>');

-- --------------------------------------------------------

--
-- Table structure for table `inspection_points`
--

DROP TABLE IF EXISTS `inspection_points`;
CREATE TABLE `inspection_points` (
  `id` int(11) NOT NULL,
  `criteria` varchar(128) NOT NULL,
  `dsa` int(11) NOT NULL,
  `rrv` int(11) NOT NULL,
  `section` int(11) NOT NULL,
  `extra` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inspection_points`
--

INSERT INTO `inspection_points` (`id`, `criteria`, `dsa`, `rrv`, `section`, `extra`) VALUES
(1, 'Bodywork free from damage (protruding or sharp edges)', 1, 1, 1, 0),
(2, 'Reflectors and reflective markings clean', 1, 1, 1, 0),
(3, 'Driver and passenger doors function', 1, 1, 1, 0),
(4, 'Road wheel nut indicators/wheels for damage (SOP 03/2014)', 1, 1, 1, 0),
(5, 'Tyre condition - tread and pressure (min 3mm)', 1, 1, 1, 0),
(6, 'Tail lift deploys and operates fully', 1, 0, 2, 0),
(7, 'Stretcher trolley functions / secures fully', 1, 0, 2, 0),
(8, 'Windscreen washer fluid level/wipe function', 1, 1, 1, 0),
(9, 'Engine oil, brake, coolant and clutch levels', 1, 1, 2, 0),
(10, 'Windscreen and wing mirrors clean and damage free', 1, 1, 1, 0),
(12, 'Instrumentation warning lights check', 1, 1, 2, 0),
(13, 'Driver gauges and switches function correctly', 1, 1, 1, 0),
(14, 'Gear shift & clutch operate fully', 1, 1, 2, 0),
(15, 'All lights and indicators clean and function fully', 1, 1, 1, 0),
(16, 'Windscreen washers / wipers function fully', 1, 1, 1, 0),
(17, 'Driver and passengers seat belts function', 1, 1, 1, 0),
(18, 'Parking and foot brake tested', 1, 1, 2, 0),
(19, 'Road horn and steering tested (not in garage)', 1, 1, 1, 0),
(20, 'Blue lights and sirens tested (not in garage)', 1, 1, 2, 0),
(21, 'Defibrillator/Monitor (12 Lead) visual check and test', 1, 1, 2, 0),
(22, 'Para and Responder bags - visual check', 1, 1, 2, 0),
(23, 'Portable oxygen (contents and fill check)', 1, 1, 2, 0),
(24, 'Drugs bag - visual check', 1, 1, 2, 0),
(25, 'Electric suction unit (complete)', 1, 1, 2, 0),
(29, 'Fuel level (>half a tank)', 1, 1, 2, 0),
(30, 'Defibrillator/Monitor (12 lead) - Function', 1, 1, 4, 0),
(31, 'Para and responder bags - contents/function', 1, 1, 4, 0),
(32, 'Drugs bags - contents if not checked and tagged', 1, 1, 4, 1),
(33, 'Portable ventilator', 1, 0, 4, 0),
(34, 'Oxygen therapy mask (all sizes)', 1, 1, 4, 0),
(35, 'Nebulising mask (all sizes)', 1, 1, 4, 0),
(36, 'O2 (contents and visual inspection of cylinders)', 1, 1, 4, 0),
(37, 'Entonox', 1, 1, 4, 0),
(38, 'Morphine and Diazepam (paramedics only)', 1, 1, 4, 1),
(39, 'Long-board/head blocks/straps', 1, 0, 4, 0),
(40, 'Orthopaedic stretcher (including straps)', 1, 0, 4, 0),
(41, 'Carry Chair and tracks', 1, 0, 4, 0),
(42, 'Cervical collars (Adults/Paediatric)', 1, 1, 4, 0),
(43, 'Manual handling kit', 1, 1, 4, 0),
(44, 'Manger Elk (complete with accessories)', 1, 1, 4, 0),
(45, 'Fluid therapy', 1, 1, 4, 0),
(46, 'Infusion/administration kit', 1, 1, 4, 0),
(47, 'Cannula (all sizes)', 1, 1, 4, 0),
(48, 'Trauma Management', 1, 1, 4, 0),
(49, 'Sam Pelvic Splint (Small/Medium/Large)', 1, 1, 4, 0),
(50, 'Burns Kit', 1, 1, 4, 0),
(51, 'Splints (all sizes)', 1, 1, 4, 0),
(52, 'Dressings bag', 1, 1, 4, 0),
(53, 'Disposable Gloves (all sizes)', 1, 1, 4, 0),
(54, 'Infection control kit', 1, 1, 4, 0),
(55, 'Clinical waste bags', 1, 1, 4, 0),
(56, 'Incontinence pads/Unisex urine bottles', 1, 1, 4, 0),
(57, 'Vomit Bowls', 1, 1, 4, 0),
(58, 'Maternity Pack x 2', 1, 1, 4, 0),
(59, 'Radios (hand portables and vehicle) function', 1, 1, 3, 0),
(60, 'MDT - function', 1, 1, 3, 0),
(61, 'Toughbook - function', 1, 1, 3, 1),
(62, 'Mobile phone - function', 1, 1, 3, 0),
(63, 'Emergency Paperwork Pack, ROLE and Non-Conveyance forms', 1, 1, 3, 0),
(64, '12v/240v inverter operation', 1, 0, 3, 0),
(65, 'Agency Fuel Card', 1, 1, 3, 0),
(66, 'Fire Extinguisher', 1, 1, 3, 1),
(67, 'Major Incident Triage Labels', 1, 1, 3, 0),
(68, 'Document Wallets', 1, 1, 3, 0),
(69, 'Body Bag', 1, 1, 4, 0),
(70, 'Vehicle Exterior - Visibly Clean', 1, 1, 5, 0),
(71, 'Wing mirror setting', 1, 1, 2, 0),
(72, 'Adblu level (>5 bars or 50%)', 1, 1, 2, 0),
(73, 'Map Books', 1, 1, 3, 0),
(74, 'Vehicle Interior - Meets IPC Standard', 1, 1, 5, 0);

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
CREATE TABLE `location` (
  `id` int(11) NOT NULL,
  `location` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `page_permissions`
--

DROP TABLE IF EXISTS `page_permissions`;
CREATE TABLE `page_permissions` (
  `id` int(11) NOT NULL,
  `page` varchar(32) NOT NULL,
  `code` bit(32) NOT NULL,
  `icon` varchar(128) NOT NULL,
  `human_readable` varchar(64) NOT NULL,
  `item_position` int(11) NOT NULL,
  `display_in_menu` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `page_permissions`
--

INSERT INTO `page_permissions` (`id`, `page`, `code`, `icon`, `human_readable`, `item_position`, `display_in_menu`) VALUES
(1, 'index.php', b'00000000000000000000000000000001', '<i class="fas fa-home fa-lg text-white" data-toggle="tooltip" data-placement="bottom" title="Home"></i>', 'Home', 1, 1),
(2, 'update-index.php', b'00000000000000000000000000000001', '', 'Home (AJAX)', 0, 0),
(3, 'vdi.php', b'00000000000000000000000000000001', '', 'Home (VDI)', 0, 0),
(4, 'user-manager.php', b'00000000000000000000000000000010', '<i class="fas fa-users fa-lg text-white" data-toggle="tooltip" data-placement="bottom" title="User Mangement"></i>', 'User Management', 6, 1),
(5, 'vdi-action.php', b'00000000000000000000000000000100', '<i class="fas fa-wrench fa-lg text-white" data-toggle="tooltip" data-placement="bottom" title="VDI Fault List"></i>', 'VDI Fault List', 2, 1),
(6, 'login.php?logout', b'00000000000000000000000000000001', '<i class="fas fa-sign-out-alt fa-lg text-white" data-toggle="tooltip" data-placement="bottom" title="Sign Out"></i>', 'Sign Out', 8, 1),
(7, 'edit-vehicle.php', b'00000000000000000000000000001000', '<i class="fas fa-edit fa-lg text-white" data-toggle="tooltip" data-placement="bottom" title="Vehicle Editor"></i>', 'Vehicle Editor', 5, 1),
(8, 'crip.php', b'00000000000000000000000000010000', '<i class="fas fa-clipboard-list fa-lg text-white" data-toggle="tooltip" data-placement="bottom" title="CRIP"></i>', 'CRIP', 3, 1),
(9, 'epcr.php', b'00000000000000000000000000100000', '<i class="fas fa-laptop fa-lg text-white" data-toggle="tooltip" data-placement="bottom" title="ePCR Mangement"></i>', 'ePCR Mangement', 4, 1),
(10, 'live-update-db.php', b'00000000000000000000000000001010', '', 'Inline Editing', 0, 0),
(11, 'epcr-search.php', b'00000000000000000000000000100000', '', 'ePCR Search', 0, 0),
(12, 'edit-hide-vehicle.php', b'00000000000000000000000000001000', '', 'Hide a vehicle', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `staff_number` int(11) NOT NULL,
  `forename` varchar(32) NOT NULL,
  `surname` varchar(32) NOT NULL,
  `email` varchar(32) NOT NULL,
  `user_access_level` int(11) NOT NULL DEFAULT '0',
  `page_access_level` int(11) NOT NULL DEFAULT '1',
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `failed_login` int(11) NOT NULL DEFAULT '0',
  `session_key` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

DROP TABLE IF EXISTS `user_permissions`;
CREATE TABLE `user_permissions` (
  `id` int(11) NOT NULL,
  `user_role` varchar(64) NOT NULL,
  `code` bit(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`id`, `user_role`, `code`) VALUES
(1, 'Main screen additional functions', b'00000000000000000000000000000001'),
(2, 'Full vehicle edit rights', b'00000000000000000000000000000010');

-- --------------------------------------------------------

--
-- Table structure for table `vdi_log`
--

DROP TABLE IF EXISTS `vdi_log`;
CREATE TABLE `vdi_log` (
  `id` int(11) NOT NULL,
  `vehicle_list_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vdi_log_actions`
--

DROP TABLE IF EXISTS `vdi_log_actions`;
CREATE TABLE `vdi_log_actions` (
  `id` int(11) NOT NULL,
  `vehicle_log_detail_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vdi_log_detail`
--

DROP TABLE IF EXISTS `vdi_log_detail`;
CREATE TABLE `vdi_log_detail` (
  `id` int(11) NOT NULL,
  `vdi_log_id` int(11) NOT NULL,
  `inspection_point_id` int(11) NOT NULL,
  `report` int(11) NOT NULL,
  `comments` longtext NOT NULL,
  `action_closed` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_list`
--

DROP TABLE IF EXISTS `vehicle_list`;
CREATE TABLE `vehicle_list` (
  `id` int(11) NOT NULL,
  `callsign` varchar(16) NOT NULL,
  `vehicle_type` int(11) NOT NULL,
  `registration` varchar(16) NOT NULL,
  `mot` int(11) NOT NULL,
  `service` int(11) NOT NULL,
  `veh_status` int(11) NOT NULL DEFAULT '1',
  `issi_hh1` int(11) NOT NULL,
  `issi_hh2` int(11) NOT NULL,
  `issi_veh` int(11) NOT NULL,
  `hidden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_notes`
--

DROP TABLE IF EXISTS `vehicle_notes`;
CREATE TABLE `vehicle_notes` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `note` longtext NOT NULL,
  `expired` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_status`
--

DROP TABLE IF EXISTS `vehicle_status`;
CREATE TABLE `vehicle_status` (
  `id` int(11) NOT NULL,
  `vehicle_status` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vehicle_status`
--

INSERT INTO `vehicle_status` (`id`, `vehicle_status`) VALUES
(1, 'On The Road'),
(2, 'Advisory Note (Fit to Drive)'),
(3, 'Off The Road');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_types`
--

DROP TABLE IF EXISTS `vehicle_types`;
CREATE TABLE `vehicle_types` (
  `id` int(11) NOT NULL,
  `vehicle_type` varchar(64) NOT NULL,
  `veh_use` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ait_request_log`
--
ALTER TABLE `ait_request_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ait_table_list`
--
ALTER TABLE `ait_table_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ait_tbl_drugs_list`
--
ALTER TABLE `ait_tbl_drugs_list`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `drug_name` (`drug_name`);

--
-- Indexes for table `ait_vehicle_list`
--
ALTER TABLE `ait_vehicle_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crip_shifts`
--
ALTER TABLE `crip_shifts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `epcr_actions`
--
ALTER TABLE `epcr_actions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `epcr_batteries`
--
ALTER TABLE `epcr_batteries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `serial` (`serial`);

--
-- Indexes for table `epcr_incidents`
--
ALTER TABLE `epcr_incidents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `incident_ref` (`incident_ref`);

--
-- Indexes for table `epcr_laptops`
--
ALTER TABLE `epcr_laptops`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `asset_tag` (`asset_tag`);

--
-- Indexes for table `epcr_log`
--
ALTER TABLE `epcr_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `help_text`
--
ALTER TABLE `help_text`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inspection_points`
--
ALTER TABLE `inspection_points`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `page_permissions`
--
ALTER TABLE `page_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vdi_log`
--
ALTER TABLE `vdi_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vdi_log_actions`
--
ALTER TABLE `vdi_log_actions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vdi_log_detail`
--
ALTER TABLE `vdi_log_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vehicle_list`
--
ALTER TABLE `vehicle_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vehicle_notes`
--
ALTER TABLE `vehicle_notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vehicle_status`
--
ALTER TABLE `vehicle_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vehicle_types`
--
ALTER TABLE `vehicle_types`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ait_request_log`
--
ALTER TABLE `ait_request_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ait_table_list`
--
ALTER TABLE `ait_table_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ait_tbl_drugs_list`
--
ALTER TABLE `ait_tbl_drugs_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `ait_vehicle_list`
--
ALTER TABLE `ait_vehicle_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `crip_shifts`
--
ALTER TABLE `crip_shifts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `epcr_actions`
--
ALTER TABLE `epcr_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `epcr_batteries`
--
ALTER TABLE `epcr_batteries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `epcr_incidents`
--
ALTER TABLE `epcr_incidents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `epcr_laptops`
--
ALTER TABLE `epcr_laptops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;
--
-- AUTO_INCREMENT for table `epcr_log`
--
ALTER TABLE `epcr_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `help_text`
--
ALTER TABLE `help_text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `inspection_points`
--
ALTER TABLE `inspection_points`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;
--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `page_permissions`
--
ALTER TABLE `page_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `vdi_log`
--
ALTER TABLE `vdi_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `vdi_log_actions`
--
ALTER TABLE `vdi_log_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `vdi_log_detail`
--
ALTER TABLE `vdi_log_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1023;
--
-- AUTO_INCREMENT for table `vehicle_list`
--
ALTER TABLE `vehicle_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;
--
-- AUTO_INCREMENT for table `vehicle_notes`
--
ALTER TABLE `vehicle_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `vehicle_status`
--
ALTER TABLE `vehicle_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `vehicle_types`
--
ALTER TABLE `vehicle_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
