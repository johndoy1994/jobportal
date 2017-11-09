-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 10, 2016 at 03:36 PM
-- Server version: 5.5.49-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `provalue_group_inc`
--

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE IF NOT EXISTS `cities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `name`, `state_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'City #1', 1, NULL, NULL, NULL),
(2, 'City #2', 1, NULL, NULL, NULL),
(3, 'City #3', 2, NULL, NULL, NULL),
(4, 'City #4', 2, NULL, NULL, NULL),
(5, 'City #5', 3, NULL, NULL, NULL),
(6, 'City #6', 3, NULL, NULL, NULL),
(7, 'City #7', 4, NULL, NULL, NULL),
(8, 'City #8', 4, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'India', NULL, NULL, NULL),
(2, 'Nepal', NULL, NULL, NULL),
(3, 'Bhutan', NULL, NULL, NULL),
(4, 'Bangladesh', NULL, NULL, NULL),
(5, 'Sri Lanka', NULL, NULL, NULL),
(6, 'China', NULL, NULL, NULL),
(7, 'Russia', NULL, NULL, NULL),
(8, 'United States of America', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `degrees`
--

CREATE TABLE IF NOT EXISTS `degrees` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `degrees`
--

INSERT INTO `degrees` (`id`, `name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Cert1', NULL, NULL, NULL),
(2, 'Cert2', NULL, NULL, NULL),
(3, 'Cert3', NULL, NULL, NULL),
(4, 'Cert4', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

CREATE TABLE IF NOT EXISTS `education` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `education`
--

INSERT INTO `education` (`id`, `name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Bachelor Degree', NULL, NULL, NULL),
(2, 'Diploma', NULL, NULL, NULL),
(3, 'High School', NULL, NULL, NULL),
(4, 'Other', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `experiences`
--

CREATE TABLE IF NOT EXISTS `experiences` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `exp_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `experiences`
--

INSERT INTO `experiences` (`id`, `exp_name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, '1 Year', NULL, NULL, NULL),
(2, '2 Year', NULL, NULL, NULL),
(3, '3 Year(s)', NULL, NULL, NULL),
(4, '4 Year(s)', NULL, NULL, NULL),
(5, 'More than 6 years', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `experience_levels`
--

CREATE TABLE IF NOT EXISTS `experience_levels` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `level` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `experience_levels`
--

INSERT INTO `experience_levels` (`id`, `level`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Begineer', NULL, NULL, NULL),
(2, 'Intermediate', NULL, NULL, NULL),
(3, 'Professional', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `industries`
--

CREATE TABLE IF NOT EXISTS `industries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE IF NOT EXISTS `items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`, `price`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'changed', 123.123123, '2016-06-06 01:18:07', '2016-06-06 01:11:29', '2016-06-06 01:18:07'),
(2, 'asdasdasd', 123.123, '2016-06-06 01:18:09', '2016-06-06 01:11:39', '2016-06-06 01:18:09');

-- --------------------------------------------------------

--
-- Table structure for table `job_categories`
--

CREATE TABLE IF NOT EXISTS `job_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `job_categories`
--

INSERT INTO `job_categories` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Job Category 1', NULL, NULL, NULL),
(2, 'Job Category 2', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `job_titles`
--

CREATE TABLE IF NOT EXISTS `job_titles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `job_category_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `job_titles`
--

INSERT INTO `job_titles` (`id`, `job_category_id`, `title`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Job Title 1', NULL, NULL, NULL),
(2, 2, 'Job Title 2', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `job_types`
--

CREATE TABLE IF NOT EXISTS `job_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `job_types`
--

INSERT INTO `job_types` (`id`, `name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Part Time', NULL, NULL, NULL),
(2, 'Full Time', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE IF NOT EXISTS `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2016_06_06_062655_create_items_table', 1),
('2016_06_06_090248_create_users_table', 2),
('2016_06_06_101841_create_user_verifications_table', 2),
('2016_06_06_090646_create_job_types_table', 3),
('2016_06_06_094901_create_job_categories_table', 3),
('2016_06_06_095749_AddSoftDeletesColToJobCategory', 3),
('2016_06_07_090842_create_tags_table', 3),
('2016_06_07_101851_create_industries_table', 3),
('2016_06_07_101930_create_job_titles_table', 3),
('2016_06_07_111829_create_education_table', 3),
('2016_06_07_121312_create_degrees_table', 3),
('2016_06_08_055307_add_col_to_tag_jobtitle', 4),
('2016_06_08_062303_create_salary_types_table', 4),
('2016_06_08_090339_create_salary_ranges_table', 4),
('2016_06_08_102512_create_countries_table', 4),
('2016_06_08_105028_create_states_table', 4),
('2016_06_08_114037_create_experience_levels_table', 4),
('2016_06_08_124344_create_user_profiles_table', 4),
('2016_06_08_125449_create_cities_table', 4),
('2016_06_08_125614_create_person_titles_table', 4),
('2016_06_08_130601_create_user_addresses_table', 4),
('2016_06_08_130611_create_experiences_table', 4),
('2016_06_08_131118_create_user_experiences_table', 4),
('2016_06_08_132923_create_user_certificates_table', 4),
('2016_06_08_133253_create_user_skills_table', 4),
('2016_06_10_084634_create_user_resumes_table', 5);

-- --------------------------------------------------------

--
-- Table structure for table `person_titles`
--

CREATE TABLE IF NOT EXISTS `person_titles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `person_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `person_titles`
--

INSERT INTO `person_titles` (`id`, `person_title`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Mr', NULL, NULL, NULL),
(2, 'Mrs', NULL, NULL, NULL),
(3, 'Ms', NULL, NULL, NULL),
(4, 'Dr', NULL, NULL, NULL),
(5, 'Ph.d', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `salary_ranges`
--

CREATE TABLE IF NOT EXISTS `salary_ranges` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `salary_type_id` int(11) NOT NULL,
  `range_from` int(11) NOT NULL,
  `range_to` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `salary_ranges`
--

INSERT INTO `salary_ranges` (`id`, `salary_type_id`, `range_from`, `range_to`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 10, 20, NULL, NULL, NULL),
(2, 2, 30, 40, NULL, NULL, NULL),
(3, 3, 50, 60, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `salary_types`
--

CREATE TABLE IF NOT EXISTS `salary_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `salary_type_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `salary_types`
--

INSERT INTO `salary_types` (`id`, `salary_type_name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Daily', NULL, NULL, NULL),
(2, 'Monthly', NULL, NULL, NULL),
(3, 'Annually', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE IF NOT EXISTS `states` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `country_id` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`id`, `name`, `country_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Gujarat', 1, NULL, NULL, NULL),
(2, 'Delhi', 1, NULL, NULL, NULL),
(3, 'Mumbai', 1, NULL, NULL, NULL),
(4, 'Haryana', 1, NULL, NULL, NULL),
(5, 'State #1', 2, NULL, NULL, NULL),
(6, 'State #2', 2, NULL, NULL, NULL),
(7, 'State #3', 3, NULL, NULL, NULL),
(8, 'State #4', 3, NULL, NULL, NULL),
(9, 'State #5', 4, NULL, NULL, NULL),
(10, 'State #6', 4, NULL, NULL, NULL),
(11, 'State #7', 5, NULL, NULL, NULL),
(12, 'State #8', 5, NULL, NULL, NULL),
(13, 'State #9', 6, NULL, NULL, NULL),
(14, 'State #10', 6, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `job_title_id` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`, `job_title_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Tag 1', 1, NULL, NULL, NULL),
(2, 'Tag 2', 1, NULL, NULL, NULL),
(3, 'Tag 3', 2, NULL, NULL, NULL),
(4, 'Tag 4', 2, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mobile_number` decimal(10,0) NOT NULL,
  `email_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `level` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `mobile_number`, `email_address`, `password`, `type`, `level`, `status`, `remember_token`, `deleted_at`, `created_at`, `updated_at`) VALUES
(3, 'Kaushik Kothiya', 0, 'kothiyakaushik08@gmail.com', '$2y$10$u0iRt76CAxr9cNjTyoOGP.N8.WRSr6oIC/efGol60dxsBPlkWhB4.', 'JOB_SEEKER', 'FRONTEND', 'ACTIVATED', 'VuZ1vt1OC1P9ZROnQiVGYxvz7cisMylfT0E1bcJRgTMvQHKwefLJxtK5tgW4', NULL, '2016-06-07 04:10:51', '2016-06-07 04:43:05'),
(5, 'Upala Pratik', 9797979764, 'upalapratik@gmail.com', '$2y$10$4HS.qCSaINbgm7AMF4BjSe6E3IoeGmpPXTEyG7/A93ijmnNQpNcQa', 'JOB_SEEKER', 'FRONTEND', 'ACTIVATED', 'mEVZKUqWu3eZC8OBQsxYGfGmUkg6rnxMITMCV0s2uXnHTcbMLkQGMFFph8Fi', NULL, '2016-06-07 04:15:10', '2016-06-10 01:07:27'),
(6, 'Mohan Sharma', 0, 'iammegamohan@gmail.com', '$2y$10$RI9ds4R1jhEreVlE8uh9X.xmxQTTHs2FYql.GDOiQccM04702Nor6', 'JOB_SEEKER', 'FRONTEND', 'ACTIVATED', 'gPV5SMCC7m5EL2ZQPmy0IpG8En9StBQKUM75S0RAGfHCi1TeJJxTqaq8WsJD', NULL, '2016-06-07 04:57:22', '2016-06-10 01:46:47'),
(8, 'Sagar Ramani', 0, 'sagarramani36@gmail.com', '$2y$10$jnZGr6viF3udS3wpTf5OouptDXxxq9KcpBb0CA0S98irksmn8oEKa', 'JOB_SEEKER', 'FRONTEND', 'ACTIVATED', 'NinWLEebKlFpPKSQt4THhhmoqzlUQSr4s28Ti1mz6bdz4KhkVYfsScDtB6WC', NULL, '2016-06-07 23:16:42', '2016-06-08 06:54:55'),
(9, 'xyz man', 0, 'xyzman@gmail.com', '$2y$10$.oP3fWPF3jJzms2JzmZbquIX2A/lrPkSesinxlsESX2O4cTER398S', 'JOB_SEEKER', 'FRONTEND', 'ACTIVATED', '7jE9MDeylMV7tAH1ChM0hwYkvyPnTqRXxf2lvD2dv5YDpTyp1rVtcUIJOS9o', NULL, '2016-06-09 01:27:10', '2016-06-09 01:27:48');

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE IF NOT EXISTS `user_addresses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `postal_code` int(11) NOT NULL,
  `street` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

--
-- Dumping data for table `user_addresses`
--

INSERT INTO `user_addresses` (`id`, `user_id`, `city_id`, `postal_code`, `street`, `type`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 5, 3, 123456, 'City Street', 'residance', NULL, '2016-06-10 01:07:27', '2016-06-10 01:07:27'),
(2, 5, 4, 0, '', 'desired', '2016-06-10 03:58:45', '2016-06-10 01:11:08', '2016-06-10 03:58:45'),
(3, 5, 4, 0, '', 'desired', '2016-06-10 04:06:54', '2016-06-10 03:58:45', '2016-06-10 04:06:54'),
(4, 5, 4, 0, '', 'desired', '2016-06-10 04:09:36', '2016-06-10 04:06:54', '2016-06-10 04:09:36'),
(5, 5, 4, 0, '', 'desired', '2016-06-10 04:12:35', '2016-06-10 04:09:36', '2016-06-10 04:12:35'),
(6, 5, 4, 0, '', 'desired', '2016-06-10 04:14:34', '2016-06-10 04:12:35', '2016-06-10 04:14:34'),
(7, 5, 4, 0, '', 'desired', '2016-06-10 04:18:54', '2016-06-10 04:14:34', '2016-06-10 04:18:54'),
(8, 5, 4, 0, '', 'desired', '2016-06-10 04:21:14', '2016-06-10 04:18:54', '2016-06-10 04:21:14'),
(9, 5, 4, 0, '', 'desired', '2016-06-10 04:21:22', '2016-06-10 04:21:14', '2016-06-10 04:21:22'),
(10, 5, 4, 0, '', 'desired', '2016-06-10 04:21:26', '2016-06-10 04:21:22', '2016-06-10 04:21:26'),
(11, 5, 4, 0, '', 'desired', NULL, '2016-06-10 04:21:26', '2016-06-10 04:21:26');

-- --------------------------------------------------------

--
-- Table structure for table `user_certificates`
--

CREATE TABLE IF NOT EXISTS `user_certificates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `degree_id` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=31 ;

--
-- Dumping data for table `user_certificates`
--

INSERT INTO `user_certificates` (`id`, `user_id`, `degree_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 5, 1, '2016-06-10 01:10:03', '2016-06-10 01:09:34', '2016-06-10 01:10:03'),
(2, 5, 3, '2016-06-10 01:10:03', '2016-06-10 01:09:34', '2016-06-10 01:10:03'),
(3, 5, 1, '2016-06-10 01:10:12', '2016-06-10 01:10:03', '2016-06-10 01:10:12'),
(4, 5, 3, '2016-06-10 01:10:12', '2016-06-10 01:10:03', '2016-06-10 01:10:12'),
(5, 5, 1, '2016-06-10 01:10:45', '2016-06-10 01:10:12', '2016-06-10 01:10:45'),
(6, 5, 3, '2016-06-10 01:10:45', '2016-06-10 01:10:12', '2016-06-10 01:10:45'),
(7, 5, 1, '2016-06-10 01:11:04', '2016-06-10 01:10:45', '2016-06-10 01:11:04'),
(8, 5, 3, '2016-06-10 01:11:04', '2016-06-10 01:10:45', '2016-06-10 01:11:04'),
(9, 5, 1, '2016-06-10 01:11:08', '2016-06-10 01:11:04', '2016-06-10 01:11:08'),
(10, 5, 3, '2016-06-10 01:11:08', '2016-06-10 01:11:04', '2016-06-10 01:11:08'),
(11, 5, 1, '2016-06-10 03:58:45', '2016-06-10 01:11:08', '2016-06-10 03:58:45'),
(12, 5, 3, '2016-06-10 03:58:45', '2016-06-10 01:11:08', '2016-06-10 03:58:45'),
(13, 5, 1, '2016-06-10 04:06:54', '2016-06-10 03:58:45', '2016-06-10 04:06:54'),
(14, 5, 3, '2016-06-10 04:06:54', '2016-06-10 03:58:45', '2016-06-10 04:06:54'),
(15, 5, 1, '2016-06-10 04:09:36', '2016-06-10 04:06:54', '2016-06-10 04:09:36'),
(16, 5, 3, '2016-06-10 04:09:36', '2016-06-10 04:06:54', '2016-06-10 04:09:36'),
(17, 5, 1, '2016-06-10 04:12:35', '2016-06-10 04:09:36', '2016-06-10 04:12:35'),
(18, 5, 3, '2016-06-10 04:12:35', '2016-06-10 04:09:36', '2016-06-10 04:12:35'),
(19, 5, 1, '2016-06-10 04:14:34', '2016-06-10 04:12:35', '2016-06-10 04:14:34'),
(20, 5, 3, '2016-06-10 04:14:34', '2016-06-10 04:12:35', '2016-06-10 04:14:34'),
(21, 5, 1, '2016-06-10 04:18:53', '2016-06-10 04:14:34', '2016-06-10 04:18:53'),
(22, 5, 3, '2016-06-10 04:18:53', '2016-06-10 04:14:34', '2016-06-10 04:18:53'),
(23, 5, 1, '2016-06-10 04:21:14', '2016-06-10 04:18:53', '2016-06-10 04:21:14'),
(24, 5, 3, '2016-06-10 04:21:14', '2016-06-10 04:18:54', '2016-06-10 04:21:14'),
(25, 5, 1, '2016-06-10 04:21:22', '2016-06-10 04:21:14', '2016-06-10 04:21:22'),
(26, 5, 3, '2016-06-10 04:21:22', '2016-06-10 04:21:14', '2016-06-10 04:21:22'),
(27, 5, 1, '2016-06-10 04:21:25', '2016-06-10 04:21:22', '2016-06-10 04:21:25'),
(28, 5, 3, '2016-06-10 04:21:25', '2016-06-10 04:21:22', '2016-06-10 04:21:25'),
(29, 5, 1, NULL, '2016-06-10 04:21:25', '2016-06-10 04:21:25'),
(30, 5, 3, NULL, '2016-06-10 04:21:25', '2016-06-10 04:21:25');

-- --------------------------------------------------------

--
-- Table structure for table `user_experiences`
--

CREATE TABLE IF NOT EXISTS `user_experiences` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `education_id` int(11) NOT NULL,
  `current_job_type_id` int(11) NOT NULL,
  `current_salary_range_id` int(11) NOT NULL,
  `experinece_id` int(11) NOT NULL,
  `experinece_level_id` int(11) NOT NULL,
  `desired_job_title_id` int(11) NOT NULL,
  `desired_salary_range_id` int(11) NOT NULL,
  `recent_job_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `user_experiences`
--

INSERT INTO `user_experiences` (`id`, `user_id`, `education_id`, `current_job_type_id`, `current_salary_range_id`, `experinece_id`, `experinece_level_id`, `desired_job_title_id`, `desired_salary_range_id`, `recent_job_title`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 5, 1, 2, 1, 1, 1, 1, 3, 'Current Job Title', NULL, '2016-06-10 01:09:02', '2016-06-10 01:09:02');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE IF NOT EXISTS `user_profiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `person_title_id` int(11) NOT NULL,
  `about_me` text COLLATE utf8_unicode_ci NOT NULL,
  `profile_privacy` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `user_id`, `person_title_id`, `about_me`, `profile_privacy`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 5, 1, 'Something', 3, NULL, '2016-06-10 01:09:19', '2016-06-10 04:21:25');

-- --------------------------------------------------------

--
-- Table structure for table `user_resumes`
--

CREATE TABLE IF NOT EXISTS `user_resumes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_skills`
--

CREATE TABLE IF NOT EXISTS `user_skills` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=27 ;

--
-- Dumping data for table `user_skills`
--

INSERT INTO `user_skills` (`id`, `user_id`, `tag_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(17, 5, 1, '2016-06-10 04:18:54', '2016-06-10 04:14:34', '2016-06-10 04:18:54'),
(18, 5, 2, '2016-06-10 04:18:54', '2016-06-10 04:14:34', '2016-06-10 04:18:54'),
(19, 5, 1, '2016-06-10 04:21:14', '2016-06-10 04:18:54', '2016-06-10 04:21:14'),
(20, 5, 3, '2016-06-10 04:21:14', '2016-06-10 04:18:54', '2016-06-10 04:21:14'),
(21, 5, 1, '2016-06-10 04:21:22', '2016-06-10 04:21:14', '2016-06-10 04:21:22'),
(22, 5, 3, '2016-06-10 04:21:22', '2016-06-10 04:21:14', '2016-06-10 04:21:22'),
(23, 5, 1, '2016-06-10 04:21:25', '2016-06-10 04:21:22', '2016-06-10 04:21:25'),
(24, 5, 3, '2016-06-10 04:21:25', '2016-06-10 04:21:22', '2016-06-10 04:21:25'),
(25, 5, 1, NULL, '2016-06-10 04:21:25', '2016-06-10 04:21:25'),
(26, 5, 3, NULL, '2016-06-10 04:21:25', '2016-06-10 04:21:25');

-- --------------------------------------------------------

--
-- Table structure for table `user_verifications`
--

CREATE TABLE IF NOT EXISTS `user_verifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `method` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16 ;

--
-- Dumping data for table `user_verifications`
--

INSERT INTO `user_verifications` (`id`, `user_id`, `method`, `token`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 2, 'email', '911e5476daa89fcb216e3f283a241a66', 'VERIFIED', NULL, '2016-06-06 08:13:23', '2016-06-06 23:38:32'),
(2, 2, 'email', 'a6aae41d12071f440ed5ce9e68e9539d', 'NOT_VERIFIED', NULL, '2016-06-06 08:13:29', '2016-06-06 08:13:29'),
(3, 2, 'email', '9f236931a796f18002fe73c7458f0ce4', 'NOT_VERIFIED', NULL, '2016-06-06 08:13:52', '2016-06-06 08:13:52'),
(4, 2, 'email', '56506f91ce04a8cff676cafbbd5e8705', 'NOT_VERIFIED', NULL, '2016-06-06 08:14:19', '2016-06-06 08:14:19'),
(5, 2, 'email', '1b327058731286a01eddd0aed17e18ae', 'NOT_VERIFIED', NULL, '2016-06-06 08:22:05', '2016-06-06 08:22:05'),
(6, 2, 'email', '3ba8d9618248869da875b531cb790e9b', 'NOT_VERIFIED', NULL, '2016-06-06 08:22:06', '2016-06-06 08:22:06'),
(7, 2, 'email', '14b7531f8613a5bdea692ae510c3726d', 'NOT_VERIFIED', NULL, '2016-06-06 08:23:44', '2016-06-06 08:23:44'),
(8, 2, 'email', 'fbb6ed7418af50eeee791cc53999ab41', 'NOT_VERIFIED', NULL, '2016-06-06 08:26:14', '2016-06-06 08:26:14'),
(9, 3, 'through', '575696a303747', 'VERIFIED', NULL, '2016-06-07 04:10:51', '2016-06-07 04:10:51'),
(10, 4, 'through', '5756977ec165a', 'VERIFIED', NULL, '2016-06-07 04:14:30', '2016-06-07 04:14:30'),
(11, 5, 'through', '575697a6864bc', 'VERIFIED', NULL, '2016-06-07 04:15:10', '2016-06-07 04:15:10'),
(12, 6, 'through', '5756a18a166e1', 'VERIFIED', NULL, '2016-06-07 04:57:22', '2016-06-07 04:57:22'),
(13, 7, 'through', '5757a2fd11078', 'VERIFIED', NULL, '2016-06-07 23:15:49', '2016-06-07 23:15:49'),
(14, 8, 'through', '5757a33214b6b', 'VERIFIED', NULL, '2016-06-07 23:16:42', '2016-06-07 23:16:42'),
(15, 9, 'email', 'd85e505270a49c6f8524e7814893c800', 'VERIFIED', NULL, '2016-06-09 01:27:30', '2016-06-09 01:27:42');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
