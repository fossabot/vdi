-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 05, 2018 at 10:57 PM
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

CREATE TABLE `ait_table_list` (
  `id` int(11) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `human_readable_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='List of additional information tables to select from';

--
-- Dumping data for table `ait_table_list`
--

INSERT INTO `ait_table_list` (`id`, `table_name`, `human_readable_name`) VALUES
(1, 'ait_tbl_drugs_list', 'Drugs Bag Check');

-- --------------------------------------------------------

--
-- Table structure for table `ait_tbl_drugs_list`
--

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

CREATE TABLE `crip_shifts` (
  `id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `description` varchar(64) NOT NULL,
  `hidden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `inspection_points`
--

CREATE TABLE `inspection_points` (
  `id` int(11) NOT NULL,
  `criteria` varchar(128) NOT NULL,
  `dsa` int(11) NOT NULL,
  `rrv` int(11) NOT NULL,
  `section` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inspection_points`
--

INSERT INTO `inspection_points` (`id`, `criteria`, `dsa`, `rrv`, `section`) VALUES
(1, 'Bodywork free from damage (protruding or sharp edges)', 1, 1, 1),
(2, 'Reflectors and reflective markings clean', 1, 1, 1),
(3, 'Driver and passenger doors function', 1, 1, 1),
(4, 'Road wheel nut indicators/wheels for damage (SOP 03/2014)', 1, 1, 1),
(5, 'Tyre condition - tread and pressure (min 3mm)', 1, 1, 1),
(6, 'Tail lift deploys and operates fully', 1, 0, 2),
(7, 'Stretcher trolley functions / secures fully', 1, 0, 2),
(8, 'Windscreen washer fluid level/wipe function', 1, 1, 1),
(9, 'Engine oil, brake, coolant and clutch levels', 1, 1, 2),
(10, 'Windscreen and wing mirrors clean and damage free', 1, 1, 1),
(12, 'Instrumentation warning lights check', 1, 1, 2),
(13, 'Driver gauges and switches function correctly', 1, 1, 1),
(14, 'Gear shift & clutch operate fully', 1, 1, 2),
(15, 'All lights and indicators clean and function fully', 1, 1, 1),
(16, 'Windscreen washers / wipers function fully', 1, 1, 1),
(17, 'Driver and passengers seat belts function', 1, 1, 1),
(18, 'Parking and foot brake tested', 1, 1, 2),
(19, 'Road horn and steering tested (not in garage)', 1, 1, 1),
(20, 'Blue lights and sirens tested (not in garage)', 1, 1, 2),
(21, 'Defibrillator/Monitor (12 Lead) visual check and test', 1, 1, 2),
(22, 'Para and Responder bags - visual check', 1, 1, 2),
(23, 'Portable oxygen (contents and fill check)', 1, 1, 2),
(24, 'Drugs bag - visual check', 1, 1, 2),
(25, 'Electric suction unit (complete)', 1, 1, 2),
(29, 'Fuel level (>half a tank)', 1, 1, 2),
(30, 'Defibrillator/Monitor (12 lead) - Function', 1, 1, 4),
(31, 'Para and responder bags - contents/function', 1, 1, 4),
(32, 'Drugs bags - contents if not checked and tagged', 1, 1, 4),
(33, 'Portable ventilator', 1, 0, 4),
(34, 'Oxygen therapy mask (all sizes)', 1, 1, 4),
(35, 'Nebulising mask (all sizes)', 1, 1, 4),
(36, 'O2 (contents and visual inspection of cylinders)', 1, 1, 4),
(37, 'Entonox', 1, 1, 4),
(38, 'Morphine and Diazepam (paramedics only)', 1, 1, 4),
(39, 'Long-board/head blocks/straps', 1, 0, 4),
(40, 'Orthopaedic stretcher (including straps)', 1, 0, 4),
(41, 'Carry Chair and tracks', 1, 0, 4),
(42, 'Cervical collars (Adults/Paediatric)', 1, 1, 4),
(43, 'Manual handling kit', 1, 1, 4),
(44, 'Manger Elk (complete with accessories)', 1, 1, 4),
(45, 'Fluid therapy', 1, 1, 4),
(46, 'Infusion/administration kit', 1, 1, 4),
(47, 'Cannula (all sizes)', 1, 1, 4),
(48, 'Trauma Management', 1, 1, 4),
(49, 'Sam Pelvic Splint (Small/Medium/Large)', 1, 1, 4),
(50, 'Burns Kit', 1, 1, 4),
(51, 'Splints (all sizes)', 1, 1, 4),
(52, 'Dressings bag', 1, 1, 4),
(53, 'Disposable Gloves (all sizes)', 1, 1, 4),
(54, 'Infection control kit', 1, 1, 4),
(55, 'Clinical waste bags', 1, 1, 4),
(56, 'Incontinence pads/Unisex urine bottles', 1, 1, 4),
(57, 'Vomit Bowls', 1, 1, 4),
(58, 'Maternity Pack x 2', 1, 1, 4),
(59, 'Radios (hand portables and vehicle) function', 1, 1, 3),
(60, 'MDT - function', 1, 1, 3),
(61, 'Toughbook - function', 1, 1, 3),
(62, 'Mobile phone - function', 1, 1, 3),
(63, 'Emergency Paperwork Pack, ROLE and Non-Conveyance forms', 1, 1, 3),
(64, '12v/240v inverter operation', 1, 0, 3),
(65, 'Agency Fuel Card', 1, 1, 3),
(66, 'Fire Extinguisher', 1, 1, 3),
(67, 'Major Incident Triage Labels', 1, 1, 3),
(68, 'Document Wallets', 1, 1, 3),
(69, 'Body Bag', 1, 1, 4),
(70, 'Vehicle Exterior - Visibly Clean', 1, 1, 5),
(71, 'Wing mirror setting', 1, 1, 2),
(72, 'Adblu level (>5 bars or 50%)', 1, 1, 2),
(73, 'Map Books', 1, 1, 3),
(74, 'Vehicle Interior - Meets IPC Standard', 1, 1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `id` int(11) NOT NULL,
  `location` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `link` varchar(256) NOT NULL,
  `user_role` int(11) NOT NULL,
  `position` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `name`, `link`, `user_role`, `position`) VALUES
(1, 'Home (Vehicle Board)', '/vdi-dev/', 1, 1),
(2, 'VDI Fault List', 'vdi-action.php', 3, 2),
(3, 'Project Page (GitHub)', 'https://github.com/chssn/vdi/issues', 1, 98),
(4, 'Logout', 'login.php?logout', 1, 99),
(5, 'Engineering', 'eng.php', 4, 97),
(7, 'Vehicle Editor', 'edit-vehicle.php', 3, 5),
(9, 'CRIP (DEV ONLY)', 'crip.php', 4, 3),
(10, 'Veh Info Request', 'ait.php', 2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `staff_number` int(11) NOT NULL,
  `forename` varchar(32) NOT NULL,
  `surname` varchar(32) NOT NULL,
  `email` varchar(32) NOT NULL,
  `user_role` int(11) NOT NULL DEFAULT '0',
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `session_key` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `id` int(11) NOT NULL,
  `user_role` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`id`, `user_role`) VALUES
(1, 'Staff User'),
(2, 'Supervisor'),
(3, 'Duty Locality Officer'),
(4, 'Administrator');

-- --------------------------------------------------------

--
-- Table structure for table `vdi_log`
--

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
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
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
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `vdi_log`
--
ALTER TABLE `vdi_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `vdi_log_actions`
--
ALTER TABLE `vdi_log_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `vdi_log_detail`
--
ALTER TABLE `vdi_log_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=484;
--
-- AUTO_INCREMENT for table `vehicle_list`
--
ALTER TABLE `vehicle_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;
--
-- AUTO_INCREMENT for table `vehicle_notes`
--
ALTER TABLE `vehicle_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `vehicle_status`
--
ALTER TABLE `vehicle_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `vehicle_types`
--
ALTER TABLE `vehicle_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
