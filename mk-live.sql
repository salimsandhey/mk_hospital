-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 08, 2025 at 01:10 PM
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
-- Database: `mk-live`
--

-- --------------------------------------------------------

--
-- Table structure for table `allergic_medicines`
--

CREATE TABLE `allergic_medicines` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `medicine_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `allergic_medicines`
--

INSERT INTO `allergic_medicines` (`id`, `patient_id`, `medicine_name`) VALUES
(9, 431, 'demo 1'),
(10, 431, 'ddemo2'),
(23, 435, 'petacole rft'),
(24, 435, 'hgygy'),
(25, 435, 'dsfgdfyg hghfg');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `subdomain` varchar(50) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `restrictions` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `name`, `username`, `subdomain`, `status`, `restrictions`, `created_at`) VALUES
(1, 'salim sandhey', 'salimsandhey', 'demo', 'active', NULL, '2024-10-27 17:56:23');

-- --------------------------------------------------------

--
-- Table structure for table `client_login`
--

CREATE TABLE `client_login` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(500) NOT NULL,
  `subdomain` varchar(100) NOT NULL,
  `registration_date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `client_login`
--

INSERT INTO `client_login` (`id`, `name`, `username`, `password`, `subdomain`, `registration_date`) VALUES
(1, 'demo', 'yaseensandhey', '$2y$10$lXrL/u5duuHEPYh3jJs.8uTuGK/ju151eP2yMyP8fBjkz0MkarOWG', 'mkhospital', '2024-10-25'),
(2, 'demo', 'demo', '$2y$10$38Ib74aBX/y9pQHw/pfhx.ii1oVkSvOZm9xU.euplJahQX4LPZwa.', 'demo', '2025-03-08');

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`id`, `name`) VALUES
(22, 'Tab Acevir-SP'),
(23, 'Tab Acevir-MR'),
(24, 'Tab Vanafenic MR'),
(25, 'Tab Dolain-S'),
(26, 'Tab Paridac-SP'),
(27, 'Tab Pariz-SP'),
(28, 'Tab Etovit-90'),
(29, 'Tab Vanacoxib-90'),
(30, 'Tab EtoBer-TH'),
(31, 'Tab Vanacoxib-MR'),
(32, 'Tab Etovit P'),
(33, 'Cipro 250'),
(34, 'Cipro 500'),
(35, 'Tab Amoxicillin 625 LB'),
(36, 'Tab Amoxicillin 625'),
(37, 'Tab Paricef-O'),
(38, 'Tab Cefivir-O'),
(39, 'Tab Defer-6'),
(40, 'Tab Cefaclass-200'),
(41, 'Tab Ctz 5mg'),
(42, 'Tab Defian-6'),
(43, 'Tab Zyle-100'),
(44, 'Tab Bremec-plus'),
(45, 'Cap Alphasan-Plus'),
(46, 'Tab Mykogaba-MNT'),
(47, 'Tab Gabaret-NT'),
(48, 'Tab Nerovis-Trio'),
(49, 'Tab Neurock-LC'),
(50, 'Tab Medoc-SL'),
(51, 'Tab Alphasan-SL'),
(52, 'Cap Parirab-DSR'),
(53, 'Cap Enter-D'),
(54, 'Tab Pantian-D'),
(55, 'Cap Ospecal-MAX'),
(56, 'Cap Neurobre-OD'),
(57, 'Cap Paxnerve D'),
(58, 'Tab Colzen-C2+'),
(59, 'Cap Bresure-5G'),
(60, 'Cap Pushvit-Active'),
(61, 'Cap Mikowit-CT'),
(62, 'Cap Supramax Forte'),
(63, 'Cap Decoflex Plus'),
(64, 'Cap Calcain-CT'),
(65, 'Cap Brecal-CT'),
(66, 'Cap Brevit'),
(67, 'Tab Brejod'),
(68, 'Cap Calomed-D3 Max'),
(69, 'Tab Brenac-MR'),
(70, 'Tab Osteose-GM'),
(71, 'Cap Jod raksha'),
(72, 'Cap E Satrate'),
(73, 'Cap Orthodec'),
(74, 'Cap Omega'),
(75, 'Tab Proxicam DT'),
(76, 'Gel Brenac'),
(77, 'Gel Orthosis'),
(78, 'Oil Rosidoll'),
(79, 'Oil Vanafenic'),
(80, 'Nano Shot BC D3'),
(81, 'Nano Shot Calcain'),
(82, 'Tab Lorfi P'),
(83, 'Tab Parinac P'),
(84, 'Tab Brenac-sp'),
(86, 'Cap Nurokind Plus FR'),
(87, 'Tab Zerodol SP'),
(88, 'Tab Lyser D');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `age` int(10) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `contact` double NOT NULL,
  `address` varchar(200) NOT NULL,
  `disease` varchar(100) NOT NULL,
  `registration_date` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`id`, `name`, `age`, `gender`, `contact`, `address`, `disease`, `registration_date`) VALUES
(1, 'John Doe', 30, 'male', 1234567890, '123 Main St', 'Flu', '2024-11-08'),
(2, 'Jane Smith', 25, 'female', 2345678901, '456 Elm St', 'Allergy', '2024-11-08'),
(3, 'Emily Johnson', 40, 'female', 3456789012, '789 Maple Ave', 'Hypertension', '2024-11-07'),
(4, 'Michael Brown', 32, 'male', 4567890123, '321 Oak St', 'Diabetes', '2024-11-08'),
(5, 'Sarah Lee', 27, 'female', 5678901234, '654 Pine St', 'Asthma', '2024-11-06'),
(6, 'David Wilson', 35, 'male', 6789012345, '987 Cedar St', 'Arthritis', '2024-11-08'),
(7, 'Linda Garcia', 29, 'female', 7890123456, '111 Birch St', 'Back Pain', '2024-11-05'),
(402, 'Salim Sandhey', 20, 'male', 9877231183, 'malerkotla', 'leg pain', '2024-10-25'),
(403, 'karamjeet singh', 41, 'male', 7973471948, 'ldh', 'cirvical', '2024-10-25'),
(404, 'gagandeep kaur', 36, 'female', 6284334797, 'raisar', 'ls spine', '2024-10-25'),
(405, 'bahadur singh', 40, 'male', 1234567890, 'ramgarh', 'ls spine pain sweling', '2024-10-25'),
(406, 'harpal kaur', 65, 'female', 1234567877, 'aadampur', 'knee 90%', '2024-10-25'),
(408, 'narinder singh', 16, 'male', 6280434861, 'soyian', 'lsspine weeknes', '2024-10-25'),
(409, 'Gurmail kaur', 65, 'female', 9115020930, 'MLK', 'Wrist fracture ulna radus', '2024-10-25'),
(411, 'Sandeep kaur', 34, 'female', 1234567890, 'Gehla', 'Right foot muscle tear', '2024-10-25'),
(412, 'Randheer singh ', 50, 'male', 9855310612, 'Hamidi', 'Ls spine old problem ', '2024-10-25'),
(413, 'Navneet singh', 4, 'male', 9501654955, 'Diwana', 'Clavical', '2024-10-25'),
(414, 'Surjan singh ', 55, 'male', 9781790759, 'Sanghera ', 'Lsspine 20 year old', '2024-10-25'),
(415, 'Arshdeep singh ', 23, 'male', 9876475403, 'Klalmajra ', 'Right foot muscle tear', '2024-10-25'),
(416, 'Paramjeet singh ', 33, 'male', 7717262335, 'Sehjda', 'Ls spine swelling ', '2024-10-25'),
(417, 'Nagar singh', 70, 'male', 9781400505, 'Ludhiana', 'Knee space 85%', '2024-10-25'),
(418, 'Mandeep kaur ', 40, 'female', 8968065512, 'Sehna', 'Lsspine 8year old pain cirvical arms legs pain 7 month Madison ', '2024-10-25'),
(419, 'Gurpreet singh ', 35, 'male', 9877792463, 'Sohian ', 'Lhand thumb xrey two heair line 3 month old', '2024-10-25'),
(420, 'Sarinder kaur ', 61, 'female', 8872088542, 'Wazidke ', 'Left knee two years old pain ', '2024-10-25'),
(421, 'charanjeet kaur', 45, 'female', 8872625155, 'dhaner', 'mouth swelling', '2024-10-25'),
(422, 'Hartaj singh', 12, 'male', 7676567676, 'bihla', 'l hand', '2024-10-25'),
(423, 'jasvir kaur', 65, 'female', 9888751182, 'raisar', 'LS spine leg pain', '2024-10-25'),
(424, 'jasvir kaur', 55, 'female', 9915169032, 'mehal kalan', 'right knee problem', '2024-10-25'),
(425, 'ramanpreet singh', 22, 'male', 7658811744, 'hamidi', 'right shoulder', '2024-10-25'),
(426, 'nazia', 25, 'female', 9781452286, 'mehal kalan', 'LS spine old problem', '2024-10-25'),
(427, 'Sandeep singh', 29, 'male', 9256350150, 'sehjda', 'left hand muscle jam', '2024-10-25'),
(428, 'Ranjeet kaur', 80, 'female', 8198022573, 'chiniwal', 'Radius bone fracture', '2024-10-25'),
(429, 'Ajeb kaur', 80, 'female', 7087131091, 'diwana', 'left hand blood frozen', '2024-10-25'),
(430, 'RANI KAUR', 48, 'female', 8968530253, 'klala', 'right knee 70% 7 month treatment', '2024-10-25'),
(431, 'Paramjeet kaur', 42, 'female', 9653489438, 'Mehal kalan', 'left foot strech veins', '2024-10-25'),
(432, 'Karamjeet kaur ', 45, 'female', 9855715716, 'Kalala ', 'One year old knee pain ', '2024-10-26'),
(433, 'Ranjit singh', 23, 'male', 7973056902, 'Chiniwal khurd', 'Wrist and knee , general weekness', '2024-10-26'),
(434, 'demo', 12, 'male', 9876543210, 'malerkotla', 'nothing', '2024-11-02'),
(435, 'modal', 20, 'male', 9877231183, 'malerkotla', 'nothing', '2024-11-03'),
(436, 'dsa', 12, 'male', 9876543210, 'dsad', 'wd', '2024-11-10'),
(437, 'zaid', 18, 'male', 9876543210, 'kjbknkib', 'canceer', '2024-11-11');

-- --------------------------------------------------------

--
-- Table structure for table `visits`
--

CREATE TABLE `visits` (
  `id` int(6) UNSIGNED NOT NULL,
  `patient_id` int(6) NOT NULL,
  `visit_date` date NOT NULL,
  `treatment` text NOT NULL,
  `medicines` text NOT NULL,
  `xray_taken` tinyint(1) NOT NULL DEFAULT 0,
  `xray_details` text DEFAULT NULL,
  `xray_file` varchar(255) DEFAULT NULL,
  `fees` decimal(10,2) NOT NULL,
  `treatment_options` text DEFAULT NULL,
  `s_uric_acid` varchar(255) DEFAULT NULL,
  `calcium` varchar(255) DEFAULT NULL,
  `esr` varchar(255) DEFAULT NULL,
  `cholesterol` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visits`
--

INSERT INTO `visits` (`id`, `patient_id`, `visit_date`, `treatment`, `medicines`, `xray_taken`, `xray_details`, `xray_file`, `fees`, `treatment_options`, `s_uric_acid`, `calcium`, `esr`, `cholesterol`) VALUES
(1, 1, '2024-11-08', 'Physical Therapy', 'Ibuprofen', 0, 'X-ray of lower back', NULL, 300.00, 'FOOTBOOSTER (FB)', '5.5', '9.5', '15', '180'),
(2, 2, '2024-11-08', 'Allergy Testing', 'Cetirizine', 0, '', NULL, 200.00, 'TENSE+', '4.8', '9.0', '10', '160'),
(3, 1, '2024-11-07', 'Physical Therapy', 'Paracetamol', 0, 'X-ray of upper back', NULL, 350.00, 'IFT', '5.2', '9.2', '12', '170'),
(4, 3, '2024-11-06', 'Hypertension Checkup', 'Lisinopril', 0, '', NULL, 250.00, 'LICO', '6.0', '10.0', '13', '200'),
(5, 4, '2024-11-08', 'Diabetes Checkup', 'Metformin', 0, '', NULL, 400.00, 'LASER THERAPY', '5.0', '8.8', '11', '150'),
(6, 5, '2024-11-05', 'Asthma Treatment', 'Albuterol', 0, '', NULL, 300.00, 'BANDAGE', '4.5', '9.1', '10', '190'),
(7, 6, '2024-11-08', 'Arthritis Checkup', 'Naproxen', 0, 'X-ray of knee', NULL, 350.00, 'ELASTIC BANDAGE', '5.8', '9.4', '14', '180'),
(41, 403, '2024-10-13', 'cirvical leg pain', 'Cap Parirab-DSR - Quantity: 3', 0, NULL, '', 200.00, 'TENSE+', '', '', '', ''),
(42, 403, '2024-10-13', 'cirvical pain\r\n', 'Tab Etovit P - Quantity: 6, Tab Defian-6 - Quantity: 6, Cap Parirab-DSR - Quantity: 3, Tab Alphasan-SL - Quantity: 3', 0, NULL, '', 200.00, 'FOOTBOOSTER (FB)', '', '', '', ''),
(43, 404, '2024-10-13', 'ls spine tmc\r\n', 'Tab Brenac-MR - Quantity: 6, Cap Brevit - Quantity: 3', 0, NULL, '', 200.00, 'TMC', '', '', '', ''),
(44, 405, '2024-10-13', 'lsspine\r\n', 'Tab Etovit-90 - Quantity: 10, Tab Proxicam DT - Quantity: 10, Cap Brevit - Quantity: 5, Gel Brenac - Quantity: 1', 0, NULL, '', 400.00, 'TENSE+,TMC', '', '', '', ''),
(45, 406, '2024-10-13', 'knee tmc', 'Tab Brenac-sp - Quantity: 10, Tab Proxicam DT - Quantity: 10, Cap Brecal-CT - Quantity: 5, Gel Brenac - Quantity: 1', 0, NULL, '', 350.00, 'TMC', '', '', '', ''),
(48, 408, '2024-10-13', 'lsspine weeknes 2moth old\r\ntwo month treetment', 'Tab Paridac-SP - Quantity: 10, Cap Brevit - Quantity: 5, Cap Parirab-DSR - Quantity: 5, Nano Shot Calcain - Quantity: 1', 0, NULL, '', 300.00, 'FOOTBOOSTER (FB),TMC', '', '', '', ''),
(49, 409, '2024-10-13', 'Wrist L', 'Tab acivir sp  - Quantity: 6, Cipro 250 - Quantity: 6, Cap Brecal-CT - Quantity: 3, Cap Parirab-DSR - Quantity: 3', 1, 'Frc', 'uploads/xrays/xray_670c1f89599943.18060452.jpg', 600.00, 'LICO,BANDAGE', '', '', '', ''),
(50, 409, '2024-10-13', 'Wrist L', 'Tab acivir sp  - Quantity: 6, Cipro 250 - Quantity: 6, Cap Brecal-CT - Quantity: 3, Cap Parirab-DSR - Quantity: 3', 1, 'Frc', 'uploads/xrays/xray_670c1f8aa9c280.61901156.jpg', 600.00, 'LICO,BANDAGE', '', '', '', ''),
(52, 411, '2024-10-13', 'Joint set', 'Tab Brenac-sp - Quantity: 6, Tab Proxicam DT - Quantity: 6, Cap Calcain-CT - Quantity: 3', 0, NULL, '', 300.00, 'LICO,BANDAGE', '', '', '', ''),
(53, 412, '2024-10-14', 'Therapy,precaution and rest', 'Tab Etovit P - Quantity: 10, Tab Proxicam DT - Quantity: 10, Cap Parirab-DSR - Quantity: 5, Tab Gabaret-NT - Quantity: 5, Cap Calomed-D3 Max - Quantity: 5, Gel Brenac - Quantity: 1', 1, 'Old problem in spine', 'uploads/xrays/xray_670d6e7d96ec67.29703562.jpg', 600.00, 'FOOTBOOSTER (FB),TMC', '', '', '', ''),
(54, 409, '2024-10-16', 'Checked the hand and remove bandage \r\nForearm splint for facture bones', 'Tab Brenac-sp - Quantity: 10, Cap Mikowit-CT - Quantity: 5', 0, NULL, '', 1200.00, '', '', '', '', ''),
(55, 413, '2024-10-17', 'Bandage on clavicle \r\nIbugesic plus syrup \r\nOssian D syrup ', '', 1, '', 'uploads/xrays/xray_6711497c2388f6.49382701.jpg', 300.00, 'LICO,BANDAGE', '', '', '', ''),
(56, 414, '2024-10-17', 'Lsspine 7month Madison ', 'Etovit90 - Quantity: 10, Tab Proxicam DT - Quantity: 10, Tab Bremec-plus - Quantity: 5, Gel Orthosis - Quantity: 1', 0, NULL, '', 400.00, 'TENSE+,TMC', '', '', '', ''),
(57, 415, '2024-10-17', 'Right foot ', 'Tab Brenac-sp - Quantity: 6, Cipro 500 - Quantity: 6, Cap Parirab-DSR - Quantity: 3, Cap Brecal-CT - Quantity: 3', 0, NULL, '', 300.00, 'TMC,BANDAGE', '', '', '', ''),
(58, 416, '2024-10-18', 'Swelling on spine right side', 'Tab Etovit P - Quantity: 6, Tab Proxicam DT - Quantity: 6, Cap Brevit - Quantity: 3, Gel Brenac - Quantity: 1', 0, NULL, '', 300.00, 'FOOTBOOSTER (FB),TMC', '', '', '', ''),
(59, 412, '2024-10-20', 'Minor pain in spine after 5 days medicine \r\nImprovement in walking', 'Tab Etovit P - Quantity: 20, Tab Proxicam DT - Quantity: 20, Tab Gabaret-NT - Quantity: 10, Cap Calomed-D3 Max - Quantity: 10, Gel Brenac - Quantity: 1, Cap Enter-D - Quantity: 10', 0, NULL, '', 1100.00, 'FOOTBOOSTER (FB)', '', '', '', ''),
(60, 417, '2024-10-20', 'Counseling \r\nPrecautions \r\nKnee 85% problem \r\nJod Raksha after 1 day', 'Tab Brejod - Quantity: 30, Cap Jod raksha - Quantity: 15, Oil Rosidoll - Quantity: 1, Gel Orthosis - Quantity: 1', 0, NULL, '', 1450.00, 'TENSE+', '', '', '', ''),
(61, 418, '2024-10-20', 'Lsspine 7 month treatment ', 'Tab Etovit-90 - Quantity: 10, Tab Proxicam DT - Quantity: 10, Cap Parirab-DSR - Quantity: 5, Cap Supramax Forte - Quantity: 5, Gel Orthosis - Quantity: 1', 0, NULL, '', 450.00, 'FOOTBOOSTER (FB),TMC', '', '', '', ''),
(62, 419, '2024-10-20', '30dayes treatment  of thumb ', 'Tab Brenac-sp - Quantity: 10, Tab Proxicam DT - Quantity: 10, Cap Calcain-CT - Quantity: 5, Gel Brenac - Quantity: 1', 0, NULL, '', 400.00, '', '', '', '', ''),
(63, 420, '2024-10-20', '7month treatment of knee', 'Tab Dolain-S - Quantity: 10, Tab Proxicam DT - Quantity: 10, Tab Brejod - Quantity: 5, Gel Orthosis - Quantity: 1', 0, NULL, '', 650.00, 'FOOTBOOSTER (FB),TMC', '', '', '', ''),
(64, 421, '2024-10-22', 'precautions and rest ', 'Tab Brenac-sp - Quantity: 6, Tab Paricef-O - Quantity: 6, Cap Parirab-DSR - Quantity: 3', 0, NULL, '', 250.00, '', '', '', '', ''),
(65, 413, '2024-10-22', 'BANDAGE CHANGE\r\n', 'SY OSOPAN D - Quantity: 1', 0, NULL, '', 100.00, 'BANDAGE', '', '', '', ''),
(66, 422, '2024-10-22', 'l hand bandage', 'Tab Dolain-S - Quantity: 6, Tab Cefaclass-200 - Quantity: 6', 1, 'muscle tear', 'uploads/xrays/xray_6717dc0ede9bf6.96682033.heic', 450.00, 'BANDAGE', '', '', '', ''),
(67, 423, '2024-10-22', 'LS spine leg pain hip pain swelling stomach\r\nconsult rest and bed sleep', 'Tab Vanafenic MR - Quantity: 6, Tab Defian-6 - Quantity: 6, Cap Brevit - Quantity: 3, Gel Brenac - Quantity: 1', 0, NULL, '', 300.00, 'FOOTBOOSTER (FB),TMC', '', '', '', ''),
(68, 424, '2024-10-22', 'knee problem \r\nprecautions and rest\r\nold problem of knee', 'Tab Dolain-S - Quantity: 10, Tab Proxicam DT - Quantity: 10, Tab Colzen-C2+ - Quantity: 5, Gel Brenac - Quantity: 1', 0, NULL, '', 400.00, 'TMC', '', '', '', ''),
(69, 425, '2024-10-22', 'clavicle bone problem \r\nonly pain relief \r\nrest', 'Cap Orthodec - Quantity: 10, Tab Acevir-MR - Quantity: 10, Cap Decoflex Plus - Quantity: 5, Gel Brenac - Quantity: 1', 0, NULL, '', 400.00, 'LASER THERAPY', '', '', '', ''),
(70, 426, '2024-10-22', 'precautions and rest \r\nmedicine note given\r\ntab zerodol sp <mn>\r\nprixicam dt <afternoon>\r\nnurokind plus\r\n5 days medicine', 'Oil Rosidoll - Quantity: 1', 1, 'old problem\r\nspace problem in spine', 'uploads/xrays/xray_6717f18aec3456.32969166.HEIC', 300.00, 'FOOTBOOSTER (FB),TMC', '', '', '', ''),
(71, 427, '2024-10-22', 'joint jam \r\nrest', 'Tab Brenac-MR - Quantity: 6, Cipro 250 - Quantity: 6, Cap Brecal-CT - Quantity: 3', 0, NULL, '', 250.00, 'TENSE+,LICO,BANDAGE', '', '', '', ''),
(72, 428, '2024-10-22', 'forearm splint and precautions\r\nmedicine written\r\ntab cipro 250\r\ntab venafenic s\r\n5 days <me>', 'Cap Brecal-CT - Quantity: 5', 0, NULL, '', 950.00, '', '', '', '', ''),
(73, 429, '2024-10-22', 'takor and gel massage\r\nRest \r\n1 month treatment', 'Tab Dolain-S - Quantity: 10, Tab Proxicam DT - Quantity: 10, Cap Brecal-CT - Quantity: 5, Gel Orthosis - Quantity: 1', 0, NULL, '', 400.00, 'TENSE+', '', '', '', ''),
(74, 430, '2024-10-22', 'rest for 15 days ', 'Tab Dolain-S - Quantity: 10, Tab Proxicam DT - Quantity: 10, Tab Osteose-GM - Quantity: 5, Gel Brenac - Quantity: 1', 1, 'knee space problem', 'uploads/xrays/xray_67183b6e50f919.37101152.heic', 500.00, 'TENSE+,TMC', '', '', '', ''),
(75, 431, '2024-10-22', 'rest from work for 3 days\r\nproblem in walking', 'Tab Brenac-sp - Quantity: 6, Tab Proxicam DT - Quantity: 6, Cap Brecal-CT - Quantity: 3', 0, NULL, '', 300.00, 'LICO,BANDAGE', '', '', '', ''),
(76, 420, '2024-10-24', '7month', 'Tab Dolain-S - Quantity: 10, Tab Zyle-100 - Quantity: 10, Cap E Satrate - Quantity: 10, Tab Colzen-C2+ - Quantity: 5, Nano Shot Calcain - Quantity: 1', 0, NULL, '', 450.00, 'TENSE+,TMC', '6.9', '8.7', '46', '256'),
(77, 402, '2024-10-24', 'tfghghgftf', '', 0, NULL, '', 88.00, '', '5', '6', '5', '99'),
(78, 402, '2024-10-24', 'ccggf', '', 0, NULL, '', 656.00, '', '7', '76', '6', '77'),
(79, 426, '2024-10-25', 'same', 'zerodol sp - Quantity: 10, neurokind plus - Quantity: 5, Gel Orthosis - Quantity: 1', 0, NULL, '', 350.00, 'FOOTBOOSTER (FB),TMC', '', '', '', ''),
(82, 402, '2024-10-28', 'cadc', 'Tab Ctz 5mg - Quantity: 12\r\n                            \r\n                            \r\n                            \r\n                            \r\n                            \r\n                            \r\n                            \r\n                            \r\n                            ', 1, 'cxada', NULL, 0.00, 'TENSE+,TMC,LASER THERAPY,LICO', NULL, NULL, NULL, NULL),
(83, 402, '2024-11-02', 'DEMO', 'Tab Acevir-SP - Quantity: 12\r\n                            \r\n                            \r\n                            \r\n                            \r\n                            \r\n                            \r\n                            \r\n                            ', 0, '', NULL, 0.00, 'TENSE+,TMC', '', '0', '', ''),
(84, 402, '2024-11-02', 'demo 3', '', 0, '', NULL, 200.00, 'TENSE+,TMC', '10', '20', '', ''),
(85, 402, '2024-11-02', 'nkjdjasjhd dhiadhjiashji djbasjd bkjbhjbdhiba bdhibiha diasbd ', 'Tab Acevir-SP - Quantity: 12, Tab Ctz 5mg - Quantity: 12, Cap Parirab-DSR - Quantity: 12, Cap Parirab-DSR - Quantity: 12', 0, NULL, NULL, 100.00, 'FOOTBOOSTER (FB),TENSE+,TMC,IFT,JSB', '', '', '', ''),
(87, 402, '2024-11-03', 'ndw', 'Tab Acevir-SP - Quantity: 12, Tab Ctz 5mg - Quantity: 20, Cap Parirab-DSR - Quantity: 30', 0, NULL, NULL, 200.00, '', '', '', '', ''),
(88, 435, '2024-11-05', 'sasas', 'null', 0, NULL, NULL, 200.00, 'TENSE+', '', '', '', ''),
(89, 435, '2024-11-05', 'sa', 'Tab Acevir-SP - Quantity: 10 - Timing: Morning', 0, NULL, NULL, 205.00, '', '', '', '', ''),
(90, 435, '2024-11-05', 'dsadas', '', 0, NULL, NULL, 200.00, '', '', '', '', ''),
(91, 435, '2024-11-05', 'ds', '', 0, NULL, NULL, 100.00, '', '', '', '', ''),
(92, 435, '2024-11-06', 'dsadas', '', 0, NULL, NULL, 200.00, '', '', '', '', ''),
(93, 433, '2024-11-06', 'dsad', '', 0, NULL, NULL, 200.00, '', '', '', '', ''),
(95, 436, '2024-11-10', 'adfa', '', 0, NULL, NULL, 222.00, '', '', '', '', ''),
(96, 436, '2024-11-10', 'wd', 'Tab Acevir-SP - Quantity: 10 - Timing: Morning, Tab Ctz 5mg - Quantity: 12 - Timing: Afternoon-Evening, Tab Vanafenic MR - Quantity: 13 - Timing: Morning-Afternoon-Evening, Gulcosamine-500 - Quantity: 1 - Timing: Evening, Cap Parirab-DSR - Quantity: 1 - Timing: Evening', 0, NULL, NULL, 2000.00, 'FOOTBOOSTER (FB),IFT,LICO', '', '', '', ''),
(97, 437, '2024-11-11', 'food poising', '', 0, NULL, NULL, 5000.00, '', '', '', '', ''),
(98, 437, '2025-03-08', 'vjh', '', 1, '', NULL, 0.00, 'LICO', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `xray_images`
--

CREATE TABLE `xray_images` (
  `id` int(6) UNSIGNED NOT NULL,
  `visit_id` int(6) UNSIGNED NOT NULL,
  `patient_id` int(6) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `xray_images`
--

INSERT INTO `xray_images` (`id`, `visit_id`, `patient_id`, `image_path`, `description`) VALUES
(6, 49, 409, 'uploads/xrays/xray_670c1f89599943.18060452.jpg', 'Frc'),
(7, 50, 409, 'uploads/xrays/xray_670c1f8aa9c280.61901156.jpg', 'Frc'),
(8, 53, 412, 'uploads/xrays/xray_670d6e7d96ec67.29703562.jpg', 'Old problem in spine'),
(9, 55, 413, 'uploads/xrays/xray_6711497c2388f6.49382701.jpg', ''),
(10, 66, 422, 'uploads/xrays/xray_6717dc0ede9bf6.96682033.heic', 'muscle tear'),
(11, 70, 426, 'uploads/xrays/xray_6717f18aec3456.32969166.HEIC', 'old problem\r\nspace problem in spine'),
(12, 74, 430, 'uploads/xrays/xray_67183b6e50f919.37101152.heic', 'knee space problem'),
(19, 83, 402, 'uploads/xrays/xray_67259d16557fe4.00307258.jpg', 'SSS'),
(20, 83, 402, 'uploads/xrays/402_xray_1730519233.jpeg', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `allergic_medicines`
--
ALTER TABLE `allergic_medicines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `subdomain` (`subdomain`);

--
-- Indexes for table `client_login`
--
ALTER TABLE `client_login`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `visits`
--
ALTER TABLE `visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `xray_images`
--
ALTER TABLE `xray_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visit_id` (`visit_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `allergic_medicines`
--
ALTER TABLE `allergic_medicines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `client_login`
--
ALTER TABLE `client_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=438;

--
-- AUTO_INCREMENT for table `visits`
--
ALTER TABLE `visits`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `xray_images`
--
ALTER TABLE `xray_images`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `allergic_medicines`
--
ALTER TABLE `allergic_medicines`
  ADD CONSTRAINT `allergic_medicines_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `visits`
--
ALTER TABLE `visits`
  ADD CONSTRAINT `visits_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `xray_images`
--
ALTER TABLE `xray_images`
  ADD CONSTRAINT `xray_images_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- Add user_roles table
CREATE TABLE IF NOT EXISTS user_roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default roles
INSERT INTO user_roles (role_name) VALUES 
('super_admin'),
('admin'),
('staff');

-- Add role column to users table if it exists
ALTER TABLE users 
ADD COLUMN role_id INT,
ADD FOREIGN KEY (role_id) REFERENCES user_roles(id);

-- Update existing users to have a default role (you may want to change this)
UPDATE users SET role_id = (SELECT id FROM user_roles WHERE role_name = 'staff');
