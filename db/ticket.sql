-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 26, 2016 at 01:16 PM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ticket`
--

-- --------------------------------------------------------

--
-- Table structure for table `boarding_vehicle`
--

CREATE TABLE IF NOT EXISTS `boarding_vehicle` (
`id` int(11) NOT NULL,
  `booked_vehicle_id` int(11) NOT NULL DEFAULT '0',
  `trip_id` mediumint(9) NOT NULL,
  `park_map_id` int(11) NOT NULL,
  `vehicle_type_id` smallint(6) NOT NULL,
  `booked_seats` varchar(80) NOT NULL,
  `departure_order` tinyint(2) NOT NULL,
  `fare` decimal(7,2) NOT NULL,
  `seat_status` enum('Not full','Full') NOT NULL DEFAULT 'Not full',
  `travel_date` date NOT NULL,
  `travel_id` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `booked_vehicles`
--

CREATE TABLE IF NOT EXISTS `booked_vehicles` (
`id` int(11) NOT NULL,
  `vehicle_no` varchar(15) NOT NULL,
  `vehicle_info_id` int(11) NOT NULL,
  `vehicle_type_id` mediumint(1) NOT NULL,
  `departure_order` tinyint(2) NOT NULL,
  `park_map_id` mediumint(9) NOT NULL,
  `route_id` mediumint(9) NOT NULL,
  `travel_date` date NOT NULL,
  `travel_id` mediumint(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `booking_details`
--

CREATE TABLE IF NOT EXISTS `booking_details` (
`id` int(11) NOT NULL,
  `payment_status` varchar(15) NOT NULL DEFAULT 'Not paid',
  `channel` varchar(15) NOT NULL,
  `response` varchar(100) NOT NULL,
  `ticket_no` char(8) NOT NULL,
  `boarding_vehicle_id` int(11) NOT NULL,
  `seat_no` tinyint(2) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `date_booked` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  `status` char(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `booking_synch`
--

CREATE TABLE IF NOT EXISTS `booking_synch` (
`id` smallint(6) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `travel_date` date NOT NULL,
  `seat_no` tinyint(2) NOT NULL,
  `departure_order` tinyint(2) NOT NULL,
  `cust_name` varchar(60) NOT NULL,
  `cust_phone` varchar(15) NOT NULL,
  `next_of_kin_phone` varchar(15) NOT NULL,
  `logged_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE IF NOT EXISTS `customers` (
`id` int(11) NOT NULL,
  `c_name` varchar(40) NOT NULL,
  `phone_no` varchar(12) NOT NULL,
  `next_of_kin_phone` varchar(12) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `destination`
--

CREATE TABLE IF NOT EXISTS `destination` (
`id` int(11) NOT NULL,
  `park_map_id` smallint(6) NOT NULL,
  `destination` varchar(30) NOT NULL,
  `destination_park_id` tinyint(4) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `manifest_audit`
--

CREATE TABLE IF NOT EXISTS `manifest_audit` (
`id` int(11) NOT NULL,
  `boarding_vehicle_id` int(11) NOT NULL,
  `fuel` decimal(7,2) NOT NULL DEFAULT '0.00',
  `drivers_feeding` decimal(7,2) NOT NULL DEFAULT '0.00',
  `expenses` decimal(8,2) NOT NULL,
  `scouters_charge` decimal(6,2) NOT NULL DEFAULT '0.00',
  `load_charge` decimal(8,2) NOT NULL,
  `date_modifed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `manifest_serial_no`
--

CREATE TABLE IF NOT EXISTS `manifest_serial_no` (
`id` int(11) NOT NULL,
  `booked_vehicle_id` int(11) NOT NULL,
  `serial_no` char(6) NOT NULL,
  `travel_id` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `merged_routes`
--

CREATE TABLE IF NOT EXISTS `merged_routes` (
`id` int(11) NOT NULL,
  `booked_ids` varchar(23) NOT NULL,
  `seat_status` varchar(8) NOT NULL,
  `seating_arrangement` tinyint(4) NOT NULL,
  `going_booked_id` int(11) NOT NULL,
  `going_route` varchar(15) NOT NULL,
  `merged_route` varchar(15) NOT NULL,
  `travel_date` date NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `parks`
--

CREATE TABLE IF NOT EXISTS `parks` (
`id` int(11) NOT NULL,
  `state_id` tinyint(4) NOT NULL,
  `park` varchar(30) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `parks`
--

INSERT INTO `parks` (`id`, `state_id`, `park`) VALUES
(1, 25, 'Jibowu'),
(2, 25, 'Maza maza'),
(3, 25, 'Orile'),
(4, 25, 'Oshodi-Charity'),
(5, 25, 'Ojuelegba'),
(6, 25, 'Ikotun'),
(7, 25, 'Berger'),
(8, 25, 'Cele'),
(9, 25, 'Oshodi-Bolade'),
(10, 1, 'Iba'),
(11, 25, 'Iyana Ipaja'),
(12, 1, 'Volks'),
(13, 25, 'Yaba'),
(14, 25, 'Ajah'),
(15, 25, 'Festac Gate'),
(16, 3, 'Holy ghost'),
(17, 3, 'Nsukka'),
(18, 25, 'Maryland'),
(19, 25, 'Ikeja'),
(21, 3, 'Old Park'),
(24, 3, 'Amokwe'),
(25, 1, 'Berger'),
(26, 13, 'Afikpo'),
(27, 20, 'Kano II'),
(28, 1, 'Utako'),
(29, 3, 'Ogui Junction'),
(30, 3, 'Garriki');

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE IF NOT EXISTS `states` (
`id` tinyint(2) NOT NULL,
  `state_name` varchar(15) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`id`, `state_name`) VALUES
(1, 'Abuja'),
(2, 'Anambra'),
(3, 'Enugu'),
(4, 'Akwa Ibom'),
(5, 'Adamawa'),
(6, 'Abia'),
(7, 'Bauchi'),
(8, 'Bayelsa'),
(9, 'Benue'),
(10, 'Borno'),
(11, 'Cross River'),
(12, 'Delta'),
(13, 'Ebonyi'),
(14, 'Edo'),
(15, 'Ekiti'),
(16, 'Gombe'),
(17, 'Imo'),
(18, 'Jigawa'),
(19, 'Kaduna'),
(20, 'Kano'),
(21, 'Katsina'),
(22, 'Kebbi'),
(23, 'Kogi'),
(24, 'Kwara'),
(25, 'Lagos'),
(26, 'Nasarawa'),
(27, 'Niger'),
(28, 'Ogun'),
(29, 'Ondo'),
(30, 'Osun'),
(31, 'Oyo'),
(32, 'Plateau'),
(33, 'Rivers'),
(34, 'Sokoto'),
(35, 'Taraba'),
(36, 'Yobe'),
(37, 'Zamfara');

-- --------------------------------------------------------

--
-- Table structure for table `travels`
--

CREATE TABLE IF NOT EXISTS `travels` (
`id` tinyint(2) NOT NULL,
  `travel_name` varchar(50) NOT NULL,
  `abbr` varchar(40) NOT NULL,
  `travel_id` tinyint(4) NOT NULL,
  `state` varchar(30) NOT NULL,
  `park` varchar(30) NOT NULL,
  `phone_nos` varchar(25) NOT NULL,
  `offline_charge` varchar(4) NOT NULL,
  `online_charge` varchar(6) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `trips`
--

CREATE TABLE IF NOT EXISTS `trips` (
`id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL DEFAULT '0',
  `park_map_id` int(11) NOT NULL,
  `departure` tinyint(2) NOT NULL,
  `route_id` int(11) NOT NULL,
  `vehicle_type_id` int(11) NOT NULL,
  `amenities` varchar(255) NOT NULL,
  `departure_time` time NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fare` float(6,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`id` tinyint(4) NOT NULL,
  `fullname` varchar(70) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` char(64) NOT NULL,
  `salt` char(32) NOT NULL,
  `user_type` varchar(15) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` char(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `password`, `salt`, `user_type`, `date_created`, `deleted`) VALUES
(1, 'Chibuzo', 'chibuzo', '8185569fe5f5f34bd48bcdf3155f570297b96928d443ebec7d3c1626536fc318', 'vFF+q6x+1ceEWmVvhlCgELhzhPJqc0pC', 'admin', '2015-01-09 12:25:49', '0'),
(32, 'Oliver Ossai', 'oliver', '15c266f78dd913f78c8453bee3921a0193d4aec15ca0d7bb8c8e9c4ffd4adcfe', 'M3PG7/73JkI6Ye6oXDoiaDL6R27JLS5z', 'admin', '2016-06-29 20:18:34', '0');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_info`
--

CREATE TABLE IF NOT EXISTS `vehicle_info` (
`id` int(11) NOT NULL,
  `vehicle_no` varchar(15) NOT NULL,
  `driver_name` varchar(40) NOT NULL,
  `drivers_phone` char(11) NOT NULL,
  `vehicle_type_id` mediumint(9) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 = active, 0 = inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_types`
--

CREATE TABLE IF NOT EXISTS `vehicle_types` (
`id` int(11) NOT NULL,
  `vehicle_name` varchar(20) NOT NULL,
  `vehicle_type` varchar(40) NOT NULL,
  `vehicle_type_id` tinyint(4) NOT NULL,
  `num_of_seats` tinyint(2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 for active, 1 for inactive',
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `boarding_vehicle`
--
ALTER TABLE `boarding_vehicle`
 ADD PRIMARY KEY (`id`), ADD KEY `park_map_id` (`park_map_id`,`vehicle_type_id`), ADD KEY `travel_id` (`travel_id`), ADD KEY `booked_vehicle_id` (`booked_vehicle_id`);

--
-- Indexes for table `booked_vehicles`
--
ALTER TABLE `booked_vehicles`
 ADD PRIMARY KEY (`id`), ADD KEY `bus_info_id` (`vehicle_no`), ADD KEY `park_map_id` (`park_map_id`), ADD KEY `travel_id` (`travel_id`), ADD KEY `vehicle_info_id` (`vehicle_info_id`);

--
-- Indexes for table `booking_details`
--
ALTER TABLE `booking_details`
 ADD PRIMARY KEY (`id`), ADD KEY `ticket_no` (`ticket_no`) USING BTREE, ADD KEY `boarding_vehicle_id` (`boarding_vehicle_id`);

--
-- Indexes for table `booking_synch`
--
ALTER TABLE `booking_synch`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
 ADD PRIMARY KEY (`id`), ADD KEY `next_of_kin_phone` (`next_of_kin_phone`);

--
-- Indexes for table `destination`
--
ALTER TABLE `destination`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `park_map_id` (`park_map_id`,`destination`);

--
-- Indexes for table `manifest_audit`
--
ALTER TABLE `manifest_audit`
 ADD PRIMARY KEY (`id`), ADD KEY `booked_vehicle_id` (`boarding_vehicle_id`);

--
-- Indexes for table `manifest_serial_no`
--
ALTER TABLE `manifest_serial_no`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `seria_no` (`serial_no`), ADD KEY `booked_id` (`booked_vehicle_id`);

--
-- Indexes for table `merged_routes`
--
ALTER TABLE `merged_routes`
 ADD PRIMARY KEY (`id`), ADD KEY `travel_date` (`travel_date`);

--
-- Indexes for table `parks`
--
ALTER TABLE `parks`
 ADD PRIMARY KEY (`id`), ADD KEY `travel_id` (`state_id`) USING BTREE;

--
-- Indexes for table `states`
--
ALTER TABLE `states`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `travels`
--
ALTER TABLE `travels`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
 ADD PRIMARY KEY (`id`), ADD KEY `trip_id` (`trip_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vehicle_info`
--
ALTER TABLE `vehicle_info`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `bus_no` (`vehicle_no`);

--
-- Indexes for table `vehicle_types`
--
ALTER TABLE `vehicle_types`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `boarding_vehicle`
--
ALTER TABLE `boarding_vehicle`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `booked_vehicles`
--
ALTER TABLE `booked_vehicles`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `booking_details`
--
ALTER TABLE `booking_details`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `booking_synch`
--
ALTER TABLE `booking_synch`
MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `destination`
--
ALTER TABLE `destination`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `manifest_audit`
--
ALTER TABLE `manifest_audit`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `manifest_serial_no`
--
ALTER TABLE `manifest_serial_no`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `merged_routes`
--
ALTER TABLE `merged_routes`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `parks`
--
ALTER TABLE `parks`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
MODIFY `id` tinyint(2) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=38;
--
-- AUTO_INCREMENT for table `travels`
--
ALTER TABLE `travels`
MODIFY `id` tinyint(2) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `id` tinyint(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT for table `vehicle_info`
--
ALTER TABLE `vehicle_info`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vehicle_types`
--
ALTER TABLE `vehicle_types`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
