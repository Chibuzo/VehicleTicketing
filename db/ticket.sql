-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 31, 2016 at 11:59 PM
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `boarding_vehicle`
--

INSERT INTO `boarding_vehicle` (`id`, `booked_vehicle_id`, `trip_id`, `park_map_id`, `vehicle_type_id`, `booked_seats`, `departure_order`, `fare`, `seat_status`, `travel_date`, `travel_id`) VALUES
(1, 0, 11, 7, 3, '2,4,3,1,5,6', 1, '4000.00', 'Not full', '2016-05-30', 0);

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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `booked_vehicles`
--

INSERT INTO `booked_vehicles` (`id`, `vehicle_no`, `vehicle_info_id`, `vehicle_type_id`, `departure_order`, `park_map_id`, `route_id`, `travel_date`, `travel_id`) VALUES
(1, '8876', 7, 3, 1, 7, 0, '2016-04-04', 4),
(7, '7776', 8, 3, 2, 7, 0, '2016-04-04', 4),
(8, 'SRT 958', 1, 1, 1, 7, 0, '2016-04-12', 4),
(9, 'BS 565 AS', 9, 3, 1, 13, 0, '2016-04-14', 6),
(10, 'SE 345 TL', 10, 1, 1, 14, 0, '2016-04-14', 6),
(11, 'HU 564 RT', 11, 3, 1, 14, 0, '2016-04-14', 6),
(13, 'HI 4573', 13, 8, 1, 7, 0, '2016-04-21', 4),
(14, 'TY 741', 4, 3, 1, 7, 0, '2016-04-29', 4),
(15, 'TY 741', 4, 3, 1, 7, 0, '2016-05-09', 4),
(16, 'TY 741', 4, 3, 1, 7, 0, '2016-05-17', 4),
(17, 'ft 756 rt', 5, 3, 1, 7, 0, '2016-05-23', 0);

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `booking_details`
--

INSERT INTO `booking_details` (`id`, `payment_status`, `channel`, `response`, `ticket_no`, `boarding_vehicle_id`, `seat_no`, `customer_id`, `date_booked`, `user_id`, `status`) VALUES
(1, 'Paid', 'offline', '', 'AOMG1KAX', 1, 2, 17, '2016-05-28 01:40:02', 0, '1'),
(2, 'Paid', 'offline', '', 'YM6VEOAI', 1, 4, 102, '2016-05-28 01:43:59', 0, '1'),
(3, 'Paid', 'offline', '', 'CIFUH3WL', 1, 3, 17, '2016-05-28 01:47:52', 0, '1'),
(4, 'Paid', 'offline', '', 'GW5TYHJ6', 1, 1, 17, '2016-05-28 01:48:34', 0, '1'),
(5, 'Paid', 'offline', '', 'MV6XDEHR', 1, 5, 17, '2016-05-28 01:49:17', 0, '1'),
(6, 'Paid', 'offline', '', '1EY4X8PJ', 1, 6, 17, '2016-05-28 01:52:17', 0, '1');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE IF NOT EXISTS `customers` (
`id` int(11) NOT NULL,
  `c_name` varchar(40) NOT NULL,
  `phone_no` varchar(12) NOT NULL,
  `next_of_kin_phone` varchar(12) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=103 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `c_name`, `phone_no`, `next_of_kin_phone`) VALUES
(1, 'Kingsley ekeh', '080648594887', '89485948594'),
(2, 'Chioma', '080378348309', '405039530943'),
(3, 'Eneh', '787989811111', '0358395389'),
(4, 'Obinna', '000', '9845'),
(5, 'Chibuzo', '03593350', '905358308111'),
(6, 'Elvis', '89385046805', '080865473675'),
(7, 'Amaka', '85395839', '968596853333'),
(8, 'Eneh', '7879898', '0358395389'),
(9, 'Obinna', '93859339', '984574759'),
(10, 'Chibuzo', '0359335000', '905358308111'),
(11, 'Kelvin', '989787867', '080897878'),
(12, 'Amaka', '85395839', '96859685'),
(13, 'Frank', '787878', '746847'),
(14, 'elvina', '04830', '305390358'),
(15, 'Ken', '8539438', '93539589'),
(16, 'Kester', '0485048540', '038535808'),
(17, 'Jide', '08035725606', '08035725606'),
(18, 'Okolo', '09689484', '085359308'),
(19, 'uzo', '845808977', '8798987987'),
(20, 'Elvis', '75968059', '090954090'),
(21, 'Elvis', '306840680', '0890950930'),
(22, 'Uzo', '9840860480', '984549898'),
(23, 'Frank', '86940609', '053050390'),
(24, 'Kester', '9835938935', '93589375938'),
(25, 'John', '0845940', '95840808'),
(26, 'tessy', '0505303', '835480'),
(27, 'nip', '839503490', '0945039503'),
(28, 'Kelvin', '08408604', '068408'),
(29, 'Gee', '08385035', '938539590'),
(30, 'Uche', '05800904', '906804503'),
(31, 'Williams', '0680458608', '0857080587'),
(32, 'Leo', '083853058', '9353058008'),
(33, 'Olufunsho', '090935738758', '090977565656'),
(34, 'john', '94069576878', '9464646464'),
(35, 'Chijioke', '08359358380', '08343093003'),
(36, 'Kene', '35938539803', '038593759383'),
(37, 'god', '53490435452', '903049435222'),
(38, 'Osita', '038535830808', '486048509304'),
(39, 'Kester', '835309530490', '535305930593'),
(40, 'Okam', '83048304308', '9389384938'),
(41, 'Obioha', '4567689079', '09095459454'),
(42, 'Rehab', '4960495649', '4086049595'),
(43, 'Chuchu', '080457467497', '948694684984'),
(44, 'Kene', '3985938593', '038503859389'),
(45, 'JOhn', '9865405400', '0079097898'),
(46, 'Chibuzo', '409605965079', '90970790950'),
(47, 'Favour', '09058306803', '406846046904'),
(48, 'ytuturtye', '868686868', '7575757557'),
(49, 'Hello', '39503568405', '085308603503'),
(50, 'Olufunsho', '9059049698', '08048406804'),
(51, 'Chisom', '986405450495', '080485947545'),
(52, 'Chisom', '08080909090', '08058958947'),
(53, 'John', '08058305803', '984958458'),
(54, 'Yinka', '089887867688', '088788687999'),
(55, 'John Eke', '080854758375', '080897485748'),
(56, 'Kester', '090886767688', '080808343997'),
(57, 'Ekene Edeh', '081734584579', '080385745844'),
(58, 'Peter Williams', '080374865843', '090375838957'),
(59, 'Adebowale', '938385039035', '083580585991'),
(60, 'Damola Adewusi', '090384869493', '081747576463'),
(61, 'Pat Amobi', '985035034534', '080385949584'),
(62, 'Blessing Onah', '083584983727', '080385935748'),
(63, 'Amina', '080857587388', '080584754857'),
(64, 'Chibuzo Nwabueze', '080845847594', '080854754878'),
(65, 'Chioma Okafor', '085458948893', '080857485784'),
(66, 'Juliet Igbokwe', '089054869569', '080854875487'),
(67, 'Keneth Chukwu', '083058948545', '080854787865'),
(68, 'Patrick John', '03850358055', '08460486958'),
(69, 'Judith Obi', '070784865475', '070837584481'),
(70, 'Kester', '080865645555', '080765543454'),
(71, 'Guest', '08080809090', '0865086058'),
(72, 'Chiemeka Onah', '050454865605', '080894594589'),
(73, 'Ejike Aneke', '090874783943', '080638474930'),
(74, 'Amaka Monique', '070074568383', '080375487548'),
(75, 'Abigail Okolo', '94759475499', '979794355475'),
(76, 'Frank Eneh', '08530583053', '08083869484'),
(77, 'Judith Obi', '08083953955', '080849545794'),
(78, 'Dodoh Okiro', '98498649684', '08707469448'),
(79, 'Goddy Egbe', '498598594859', '070784685788'),
(80, 'Jeffery Bassey', '08075937593', '08084538588'),
(81, 'Sandra Onu', '07947594759', '080375784787'),
(82, 'Ossai Oliver', '080378583785', '070786475683'),
(83, 'kester Onyia', '08694864846', '08048650468'),
(84, 'Agustine', '0877656555', '0807878766'),
(85, 'hgfkdkh', '950795079', '50695609'),
(86, 'Francis', '038503580353', '080835084800'),
(87, 'kester Onyia', '853958395839', '080386498499'),
(88, 'Kingsley Surnma', '466788', '089786775'),
(89, 'Chike Oluwa', '94849689844', '08049674979'),
(90, 'Frank Eneh', '498694869849', '080489654689'),
(91, 'Jeffery Bassey', '080835787583', '08045849769'),
(92, 'Ifeoma Ani', '03859385305', '00869476448'),
(93, 'Promise Arungwa', '08069486944', '07047597989'),
(94, 'Mark Anthony', '00383085938', '08046958649'),
(95, 'Chinonye Nneji', '08083508505', '08083598938'),
(96, 'Chibuzo', '385948593893', '080853357999'),
(97, 'frank', '667889', '67899988'),
(98, 'Alika 7up', '0804534253', '0809450000'),
(99, 'Johny Depp', '09111111', '09111111'),
(100, 'Sione', '08135763222', '08135763222'),
(101, 'Kenny Frank', '01111111', '01111111'),
(102, 'Trother', '080555555667', '080555555667');

-- --------------------------------------------------------

--
-- Table structure for table `destination`
--

CREATE TABLE IF NOT EXISTS `destination` (
`id` int(11) NOT NULL,
  `park_map_id` smallint(6) NOT NULL,
  `destination` varchar(30) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `destination`
--

INSERT INTO `destination` (`id`, `park_map_id`, `destination`, `status`) VALUES
(1, 7, 'Lagos', 1),
(2, 10, 'Abuja', 1);

-- --------------------------------------------------------

--
-- Table structure for table `manifest_audit`
--

CREATE TABLE IF NOT EXISTS `manifest_audit` (
`id` int(11) NOT NULL,
  `boarding_vehicle_id` int(11) NOT NULL,
  `fuel` decimal(7,2) NOT NULL DEFAULT '0.00',
  `drivers_feeding` decimal(7,2) NOT NULL DEFAULT '0.00',
  `scouters_charge` decimal(6,2) NOT NULL DEFAULT '0.00',
  `travel_id` smallint(6) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `manifest_audit`
--

INSERT INTO `manifest_audit` (`id`, `boarding_vehicle_id`, `fuel`, `drivers_feeding`, `scouters_charge`, `travel_id`) VALUES
(1, 31, '2500.00', '700.00', '0.00', 0),
(2, 34, '2500.00', '6000.00', '200.00', 0);

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

--
-- Dumping data for table `merged_routes`
--

INSERT INTO `merged_routes` (`id`, `booked_ids`, `seat_status`, `seating_arrangement`, `going_booked_id`, `going_route`, `merged_route`, `travel_date`) VALUES
(1, '21,24', 'Not Full', 15, 21, 'Nsukka', 'Onitsha', '2013-07-21');

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
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `travels`
--

INSERT INTO `travels` (`id`, `travel_name`, `abbr`, `travel_id`, `state`, `park`, `phone_nos`, `offline_charge`, `online_charge`) VALUES
(5, 'Peace Mass Transit', 'Peace', 6, 'Enugu-3', 'Holy ghost-16', '08035725606, 07038585849', '2', '5');

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `trips`
--

INSERT INTO `trips` (`id`, `trip_id`, `park_map_id`, `departure`, `route_id`, `vehicle_type_id`, `amenities`, `departure_time`, `date_added`, `fare`) VALUES
(3, 10, 7, 1, 1, 5, 'A/C', '07:00:00', '2016-05-21 13:06:09', 4000),
(4, 11, 7, 1, 1, 3, 'A/C', '07:15:00', '2016-05-21 13:06:09', 4000);

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
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `password`, `salt`, `user_type`, `date_created`, `deleted`) VALUES
(1, 'Chibuzo', 'chibuzo', '8185569fe5f5f34bd48bcdf3155f570297b96928d443ebec7d3c1626536fc318', 'vFF+q6x+1ceEWmVvhlCgELhzhPJqc0pC', 'admin', '2015-01-09 12:25:49', '0'),
(2, 'Njideka Onu', 'onu', '82cc406d16a8f3011f3921939fce857a86503f3a866df9e2a5758cc9701dc2c4', 'nJlZfWOzxdBRPCC5LxnBzv6FYK2QnogC', 'Operator', '2015-01-09 12:43:13', '1'),
(3, 'Okpeke Admin', 'okpo', 'dfa8617b02c9d168c793c64528521f8f6ac3120492541e5407f7acc2870c20cb', '2q2SKQMAAq3VdZqbK+NOKQu56RDCcWl/', 'user', '2015-09-15 14:43:23', '1'),
(4, 'amaka', 'amaka', '6ab0034f44a8f3fc92c2c9259133bfec8d581037cd36db30fc72bcb3d1bed9f6', 'X5AWxXtEqactoQH+DrjSuBfk4tZRyZZR', 'account', '2015-03-06 17:57:15', '0'),
(5, 'Administrator', 'admin', '34d9b4b876b7bda4e21a078ec6ddaa8f6379e4bde47009db722c82466988cdd1', 'izL6onBroshZLfM85Vrb1ZqEH+BuIRGi', 'user', '2015-03-27 12:00:46', '0'),
(6, 'Iroegbu Iroegbu', 'iroegbu', '648fbd2a1cc291edb8a1f7fee439d4fd63e6d470335ecce975ae0440cfb6f8a8', 'SLQbO/M3AgXsP80g4w/LQqEpG5fQ8O5w', 'travel_admin', '2015-10-31 16:18:50', '0'),
(7, 'Pelumi', 'pelu', '6ba0345fa0dfb195e0be52dfb0d2ead435c1f0a4d0b369e70c42daf13a9507e9', 'mEpDVw91QNhMkITN78Ge6RDQ7DR7ilQp', 'travel_admin', '2015-10-31 16:37:06', '0'),
(8, 'Chioma Amobi', 'chioma', 'a9b406e042798333f05e9856f8007eed7fe6ee62e390fc650121dbf6e8c3e5c7', 'lBBIrp9cvP4c/KcCqyh4mOP8V7WcUBez', 'travel_admin', '2015-10-31 16:43:13', '0'),
(12, 'Full Aproko', 'aproko', '8af34d1d0c4f28cb65dc0b9d63a2b42c1a452339c0e763f42b83d313c07de022', 'BbUHCpEa0v3zdt/P/iFGjDvmpmRQjVsi', 'state_admin', '2015-11-22 09:28:28', '0'),
(24, 'Okolo Uzo', 'okoloc', 'f44c0263ddf60765eeeed2b26152d02562ace000f8727c1f177a8f8d21df0d14', '8tE5BGSybCMOezCmDVdGA78lcgtWq+0C', 'park_admin', '2015-11-03 00:15:11', '0'),
(19, 'Adesuwa Okpefa', 'okpefa', 'af36c1cff6d5eec594d9071f10022513539416e656cde383922c87dcad43e1a0', '5kCa9idNn9VKN/9TzNrdUoMxgI0LCquL', 'state_admin', '2015-11-02 21:45:47', '0'),
(25, 'Augustine Ogwo', 'ogwo', '9e685dad18b5d9da472d85a4b9611105ae8c580f1ba88177a9940f7a26268ff8', 'oYn9dLq7X0w3pckhFU3E2gr8Nj3Tu5ct', 'park_admin', '2015-11-03 00:17:19', '0'),
(26, 'Chike Mgbemena', 'chike', '76db376ca24afc11fd3d8a336917fada8d0d58fcb87a892b7a6d7ec9cf13d24f', 'Tn+vJ9gSH/bRaocs2ISsVgfCb1huczj3', 'user', '2015-11-18 23:13:12', '0'),
(27, 'okon', 'okon', 'f402c127d61caea3bf41c4c3b0186d9fc2e6c24e031484dfb8a7212446eb337f', 'bywti1DmsnnrLRZTFpPG1t3Wq+4T77dA', 'travel_admin', '2016-01-28 19:10:13', '0'),
(28, 'Kano Admin', 'kano', '34ee91c74a47a7b683c659229706699d42fb97bc4b184515b17ca62881760004', 'R2jV1gxEX6f60z+FaF3SBpsN081ZPt1R', 'state_admin', '2016-01-28 19:24:22', '0'),
(29, 'Chibuzo Okolo', 'buzo', '7db807d9c3361a22e8e353ccf39906d467724bd76548499b8135b933d88226b3', 'FbFTf7p9IFg36kP6Qpyt0cXH56kx2zlt', 'state_admin', '2016-02-21 11:44:09', '0'),
(30, 'Chris Ossai', 'chris', '37d95369d4ba4ee3dfff6b9cbb4266da958efa8604cc174c73863ede1965b853', 'zHeQ1Y0zwpyHoxdO7LE7MyDmF4OIC6xE', 'park_admin', '2016-02-21 11:59:15', '0'),
(31, 'Oliver Ossai', 'bekee', '562bc813edcbce4aae12f936c31f9bf251ef3600be9ea17c63f3791a9a693400', '2RSToOqvxuEsd+net8y1FrhdFM7qTZvX', 'park_admin', '2016-02-22 20:34:25', '0');

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vehicle_info`
--

INSERT INTO `vehicle_info` (`id`, `vehicle_no`, `driver_name`, `drivers_phone`, `vehicle_type_id`, `status`) VALUES
(1, 'SRT 958', 'Tachiii', '08083683743', 1, 1),
(2, 'Xl 435 GL', 'Elvis', '08086843489', 1, 1),
(3, 'ABC 463', 'Kelvin Ama', '08475847843', 1, 1),
(4, 'TY 741', 'Lenard', '08057845632', 3, 1),
(5, 'ft 756 rt', 'Koko beef', '08085787493', 3, 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vehicle_types`
--

INSERT INTO `vehicle_types` (`id`, `vehicle_name`, `vehicle_type`, `vehicle_type_id`, `num_of_seats`, `status`, `date_added`) VALUES
(3, 'Foton', 'Hiace', 8, 16, 0, '2016-05-21 13:06:09'),
(4, 'Hiace', 'Toyota Bus', 3, 10, 0, '2016-05-21 13:06:09');

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
 ADD PRIMARY KEY (`id`), ADD KEY `travel_id` (`travel_id`), ADD KEY `booked_vehicle_id` (`boarding_vehicle_id`);

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
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `booked_vehicles`
--
ALTER TABLE `booked_vehicles`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `booking_details`
--
ALTER TABLE `booking_details`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=103;
--
-- AUTO_INCREMENT for table `destination`
--
ALTER TABLE `destination`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `manifest_audit`
--
ALTER TABLE `manifest_audit`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
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
MODIFY `id` tinyint(2) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `id` tinyint(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT for table `vehicle_info`
--
ALTER TABLE `vehicle_info`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `vehicle_types`
--
ALTER TABLE `vehicle_types`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
