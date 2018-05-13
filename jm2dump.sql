-- MySQL dump 10.13  Distrib 5.6.35, for Linux (x86_64)
--
-- Host: localhost    Database: jm2
-- ------------------------------------------------------
-- Server version	5.6.35-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `access_log`
--

DROP TABLE IF EXISTS `access_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `access_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `accessed_at` int(11) NOT NULL COMMENT 'アクセスされた日時',
  `job_master_id` int(11) DEFAULT NULL COMMENT 'テーブルjob_masterのカラムid',
  `application_master_id` int(11) DEFAULT NULL COMMENT 'テーブルapplication_masterの応募id',
  `carrier_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'アクセスされた機器',
  `access_url` varchar(255) DEFAULT NULL COMMENT 'アクセスされたURL',
  `access_browser` varchar(255) DEFAULT NULL COMMENT 'アクセスされたブラウザ',
  `access_user_agent` varchar(255) DEFAULT NULL COMMENT 'アクセスされたユーザーエージェント',
  `access_referrer` varchar(255) DEFAULT NULL COMMENT 'アクセスされたリファラー',
  `search_date` date DEFAULT NULL COMMENT '検索用日付',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_access_log_job_master_id` (`job_master_id`),
  KEY `idx_access_log_accessed_at` (`accessed_at`),
  KEY `idx_access_log_search_date` (`search_date`),
  KEY `idx_access_log_1_tenant_id_2_access_referrer` (`tenant_id`,`access_referrer`),
  KEY `idx_access_log_1_tenant_id_2_access_user_agent` (`tenant_id`,`access_user_agent`)
) ENGINE=InnoDB AUTO_INCREMENT=971752 DEFAULT CHARSET=utf8 COMMENT='アクセスログ'
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 35 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `access_log_monthly`
--

DROP TABLE IF EXISTS `access_log_monthly`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `access_log_monthly` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `access_date` date DEFAULT NULL COMMENT 'アクセス日',
  `detail_count_pc` int(11) DEFAULT NULL COMMENT 'PC閲覧数',
  `detail_count_smart` int(11) DEFAULT NULL COMMENT 'スマホ閲覧数',
  `application_count_pc` int(11) DEFAULT NULL COMMENT 'PC応募数',
  `application_count_smart` int(11) DEFAULT NULL COMMENT 'スマホ応募数',
  `member_count_pc` int(11) DEFAULT NULL COMMENT 'PC登録者数',
  `member_count_smart` int(11) DEFAULT NULL COMMENT 'スマホ登録者数',
  PRIMARY KEY (`id`,`tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='アクセスログマンスリー';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admin_column_set`
--

DROP TABLE IF EXISTS `admin_column_set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_column_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `column_no` tinyint(3) unsigned NOT NULL COMMENT 'メニューID',
  `column_name` varchar(30) COLLATE utf8_bin NOT NULL COMMENT 'admin_masterのカラム',
  `label` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `data_type` varchar(10) COLLATE utf8_bin NOT NULL COMMENT '入力方法',
  `max_length` text COLLATE utf8_bin COMMENT '文字数上限',
  `is_must` tinyint(1) DEFAULT NULL COMMENT '入力条件',
  `is_in_list` tinyint(1) DEFAULT NULL COMMENT '検索一覧表示',
  `is_in_search` tinyint(1) DEFAULT NULL COMMENT '検索項目表示',
  `valid_chk` tinyint(1) NOT NULL COMMENT '公開状況',
  PRIMARY KEY (`id`),
  KEY `idx_admin_column_set_1_tenant_id_2_column_name_3_column_no` (`tenant_id`,`column_name`,`column_no`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='管理者情報項目';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admin_column_subset`
--

DROP TABLE IF EXISTS `admin_column_subset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_column_subset` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主キーID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `column_name` varchar(30) COLLATE utf8_bin NOT NULL COMMENT 'admin_masterのカラム名',
  `subset_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '選択肢項目名',
  PRIMARY KEY (`id`),
  KEY `idx_admin_column_subset_1_tenant_id_2_column_name` (`tenant_id`,`column_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='管理者のオプション項目の選択肢';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admin_master`
--

DROP TABLE IF EXISTS `admin_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `admin_no` int(11) DEFAULT NULL,
  `corp_master_id` int(11) DEFAULT NULL COMMENT '代理店ID',
  `login_id` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'ログインID',
  `password` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'パスワード',
  `created_at` int(11) NOT NULL COMMENT '登録日時',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状態',
  `name_sei` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '名前(性)',
  `name_mei` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '名前(名)',
  `tel_no` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '電話番号',
  `client_master_id` int(11) DEFAULT NULL COMMENT '掲載企業ID',
  `mail_address` varchar(254) COLLATE utf8_bin DEFAULT NULL COMMENT 'メールアドレス',
  `option100` text COLLATE utf8_bin COMMENT 'オプション100',
  `option101` text COLLATE utf8_bin COMMENT 'オプション101',
  `option102` text COLLATE utf8_bin COMMENT 'オプション102',
  `option103` text COLLATE utf8_bin COMMENT 'オプション103',
  `option104` text COLLATE utf8_bin COMMENT 'オプション104',
  `option105` text COLLATE utf8_bin COMMENT 'オプション105',
  `option106` text COLLATE utf8_bin COMMENT 'オプション106',
  `option107` text COLLATE utf8_bin COMMENT 'オプション107',
  `option108` text COLLATE utf8_bin COMMENT 'オプション108',
  `option109` text COLLATE utf8_bin COMMENT 'オプション109',
  `job_input_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '求人原稿入力方式',
  PRIMARY KEY (`id`),
  KEY `idx_admin_master_admin_id` (`admin_no`),
  KEY `idx_admin_master_login_id` (`login_id`),
  KEY `idx_admin_master_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=100621 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='管理者';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `application_column_set`
--

DROP TABLE IF EXISTS `application_column_set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application_column_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `column_no` tinyint(3) unsigned NOT NULL COMMENT 'メニューID',
  `column_name` varchar(30) COLLATE utf8_bin NOT NULL COMMENT 'application_masterのカラム',
  `label` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `data_type` varchar(10) COLLATE utf8_bin NOT NULL COMMENT '入力方法',
  `max_length` text COLLATE utf8_bin COMMENT '文字数上限',
  `is_must` tinyint(1) DEFAULT NULL COMMENT '入力条件',
  `is_in_list` tinyint(1) DEFAULT NULL COMMENT '検索一覧表示',
  `is_in_search` tinyint(1) DEFAULT NULL COMMENT '検索項目表示',
  `valid_chk` tinyint(1) NOT NULL COMMENT '公開状況',
  `is_sync` tinyint(1) DEFAULT NULL COMMENT '連携可能項目',
  `sync_target` smallint(4) unsigned DEFAULT NULL COMMENT '連携対象ID',
  `column_explain` varchar(180) COLLATE utf8_bin DEFAULT NULL COMMENT '項目説明文',
  PRIMARY KEY (`id`),
  KEY `idx_application_column_set_1_tenant_id_2_column_name_3_column_no` (`tenant_id`,`column_name`,`column_no`)
) ENGINE=InnoDB AUTO_INCREMENT=134 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='応募者情報項目';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `application_column_subset`
--

DROP TABLE IF EXISTS `application_column_subset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application_column_subset` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主キーID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `column_name` varchar(30) COLLATE utf8_bin NOT NULL COMMENT 'application_masterのカラム名',
  `subset_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '選択肢項目名',
  PRIMARY KEY (`id`),
  KEY `idx_application_column_subset_1_tenant_id_2_column_name` (`tenant_id`,`column_name`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='応募者のオプション項目の選択肢';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `application_master`
--

DROP TABLE IF EXISTS `application_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `application_no` int(11) NOT NULL COMMENT '応募ナンバー',
  `job_master_id` int(11) NOT NULL COMMENT 'テーブルjob_masterのカラムid',
  `name_sei` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '名前(性)',
  `name_mei` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '名前(名)',
  `kana_sei` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'かな(性)',
  `kana_mei` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'かな(名)',
  `sex` tinyint(1) DEFAULT NULL COMMENT '性別',
  `birth_date` date DEFAULT NULL COMMENT '誕生日',
  `pref_id` smallint(6) DEFAULT NULL COMMENT '都道府県コード',
  `address` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '住所',
  `tel_no` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT '電話番号',
  `mail_address` varchar(254) COLLATE utf8_bin DEFAULT NULL COMMENT 'メールアドレス',
  `occupation_id` int(11) DEFAULT NULL COMMENT '属性',
  `self_pr` text COLLATE utf8_bin COMMENT '自己PR',
  `created_at` int(11) NOT NULL COMMENT '応募日時',
  `option100` text COLLATE utf8_bin COMMENT 'オプション項目100',
  `option101` text COLLATE utf8_bin COMMENT 'オプション項目101',
  `option102` text COLLATE utf8_bin COMMENT 'オプション項目102',
  `option103` text COLLATE utf8_bin COMMENT 'オプション項目103',
  `option104` text COLLATE utf8_bin COMMENT 'オプション項目104',
  `option105` text COLLATE utf8_bin COMMENT 'オプション項目105',
  `option106` text COLLATE utf8_bin COMMENT 'オプション項目106',
  `option107` text COLLATE utf8_bin COMMENT 'オプション項目107',
  `option108` text COLLATE utf8_bin COMMENT 'オプション項目108',
  `option109` text COLLATE utf8_bin COMMENT 'オプション項目109',
  `application_status_id` tinyint(4) DEFAULT '0' COMMENT '採用状況',
  `carrier_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '応募機器',
  `application_memo` text COLLATE utf8_bin COMMENT '備考',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_application_master_application_no` (`application_no`),
  KEY `idx_application_master_job_master_id` (`job_master_id`)
) ENGINE=InnoDB AUTO_INCREMENT=150049 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC COMMENT='応募'
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 6 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `application_master_backup`
--

DROP TABLE IF EXISTS `application_master_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application_master_backup` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `application_no` int(11) NOT NULL COMMENT '応募ナンバー',
  `job_master_id` int(11) NOT NULL COMMENT 'テーブルjob_masterのカラムid',
  `name_sei` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '名前(性)',
  `name_mei` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '名前(名)',
  `kana_sei` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'かな(性)',
  `kana_mei` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'かな(名)',
  `sex` tinyint(1) DEFAULT NULL COMMENT '性別',
  `birth_date` date DEFAULT NULL COMMENT '誕生日',
  `pref_id` smallint(6) DEFAULT NULL COMMENT '都道府県コード',
  `address` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '住所',
  `tel_no` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT '電話番号',
  `mail_address` varchar(254) COLLATE utf8_bin DEFAULT NULL COMMENT 'メールアドレス',
  `occupation_id` int(11) DEFAULT NULL COMMENT '属性',
  `self_pr` text COLLATE utf8_bin COMMENT '自己PR',
  `created_at` int(11) NOT NULL COMMENT '応募日時',
  `option100` text COLLATE utf8_bin COMMENT 'オプション項目100',
  `option101` text COLLATE utf8_bin COMMENT 'オプション項目101',
  `option102` text COLLATE utf8_bin COMMENT 'オプション項目102',
  `option103` text COLLATE utf8_bin COMMENT 'オプション項目103',
  `option104` text COLLATE utf8_bin COMMENT 'オプション項目104',
  `option105` text COLLATE utf8_bin COMMENT 'オプション項目105',
  `option106` text COLLATE utf8_bin COMMENT 'オプション項目106',
  `option107` text COLLATE utf8_bin COMMENT 'オプション項目107',
  `option108` text COLLATE utf8_bin COMMENT 'オプション項目108',
  `option109` text COLLATE utf8_bin COMMENT 'オプション項目109',
  `application_status_id` tinyint(4) DEFAULT '0' COMMENT '採用状況',
  `carrier_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '応募機器',
  `application_memo` text COLLATE utf8_bin COMMENT '備考',
  `deleted_at` int(11) NOT NULL COMMENT '削除日時',
  PRIMARY KEY (`id`,`tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='応募者情報完全削除前バックアップ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `application_response_log`
--

DROP TABLE IF EXISTS `application_response_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application_response_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主キーID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `application_id` int(11) NOT NULL COMMENT '応募者ID',
  `admin_id` int(11) NOT NULL COMMENT '管理者ID',
  `application_status_id` smallint(6) DEFAULT NULL COMMENT '状況',
  `mail_send_id` int(11) DEFAULT NULL COMMENT '送信メールID',
  `created_at` int(11) NOT NULL COMMENT '登録日時(システム)',
  PRIMARY KEY (`id`),
  KEY `idx_application_response_log_1_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=150070 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='応募者管理履歴';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `application_status`
--

DROP TABLE IF EXISTS `application_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `application_status_no` tinyint(4) NOT NULL COMMENT '状況コード',
  `application_status` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '状況名',
  `valid_chk` tinyint(1) DEFAULT '1' COMMENT '状態',
  PRIMARY KEY (`id`),
  KEY `idx_application_status_1_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='応募者採用状況';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `area`
--

DROP TABLE IF EXISTS `area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `area` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `area_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'エリア名',
  `valid_chk` tinyint(1) DEFAULT '1' COMMENT '状態',
  `area_tab_name` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'エリアタブ名',
  `sort` int(11) DEFAULT '0' COMMENT '表示順',
  `area_no` int(11) NOT NULL COMMENT 'エリアコード',
  `area_dir` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'エリアURL名',
  PRIMARY KEY (`id`),
  KEY `idx_area_1_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='エリア';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auth_assignment`
--

DROP TABLE IF EXISTS `auth_assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auth_item`
--

DROP TABLE IF EXISTS `auth_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`),
  CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auth_item_child`
--

DROP TABLE IF EXISTS `auth_item_child`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auth_rule`
--

DROP TABLE IF EXISTS `auth_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_charge`
--

DROP TABLE IF EXISTS `client_charge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_charge` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `client_charge_plan_id` tinyint(4) NOT NULL COMMENT '申込みプランID',
  `client_master_id` int(11) NOT NULL DEFAULT '0' COMMENT '掲載企業ID',
  `limit_num` tinyint(3) unsigned DEFAULT NULL COMMENT '枠数',
  PRIMARY KEY (`id`),
  KEY `idx_client_charge_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=300294 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='掲載企業申込みプラン';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_charge_plan`
--

DROP TABLE IF EXISTS `client_charge_plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_charge_plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `client_charge_plan_no` int(11) NOT NULL COMMENT '申込みプランID',
  `client_charge_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '課金タイプ',
  `disp_type_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT '掲載タイプ',
  `plan_name` text COLLATE utf8_bin COMMENT '申込みプラン名',
  `price` int(11) NOT NULL COMMENT '料金',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `period` smallint(6) DEFAULT NULL COMMENT '有効日数',
  PRIMARY KEY (`id`),
  KEY `idx_client_charge_plan_client_charge_plan_id` (`client_charge_plan_no`),
  KEY `idx_client_charge_plan_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='申込みプラン';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_column_set`
--

DROP TABLE IF EXISTS `client_column_set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_column_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `column_no` tinyint(3) unsigned NOT NULL COMMENT 'メニューID',
  `column_name` varchar(30) COLLATE utf8_bin NOT NULL COMMENT 'client_masterのカラム',
  `label` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `data_type` varchar(10) COLLATE utf8_bin NOT NULL COMMENT '入力方法',
  `max_length` text COLLATE utf8_bin COMMENT '文字数上限',
  `is_must` tinyint(1) DEFAULT NULL COMMENT '入力条件',
  `is_in_list` tinyint(1) DEFAULT NULL COMMENT '検索一覧表示',
  `is_in_search` tinyint(1) DEFAULT NULL COMMENT '検索項目表示',
  `valid_chk` tinyint(1) NOT NULL COMMENT '公開状況',
  `freeword_search_flg` tinyint(1) DEFAULT NULL COMMENT 'フリーワード検索フラグ',
  PRIMARY KEY (`id`),
  KEY `idx_client_column_set_1_tenant_id_2_column_name_3_column_no` (`tenant_id`,`column_name`,`column_no`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='掲載企業情報項目';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_column_subset`
--

DROP TABLE IF EXISTS `client_column_subset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_column_subset` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主キーID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `column_name` varchar(30) COLLATE utf8_bin NOT NULL COMMENT 'client_masterのカラム名',
  `subset_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '選択肢項目名',
  PRIMARY KEY (`id`),
  KEY `idx_client_column_subset_1_tenant_id_2_column_name` (`tenant_id`,`column_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='掲載企業のオプション項目の選択肢';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_disp`
--

DROP TABLE IF EXISTS `client_disp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_disp` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `column_name` varchar(255) DEFAULT NULL COMMENT '掲載企業項目カラム名',
  `sort_no` int(11) NOT NULL COMMENT '表示順',
  `disp_type_id` int(11) NOT NULL COMMENT '掲載タイプ',
  PRIMARY KEY (`id`),
  KEY `idx_client_disp_1_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=295 DEFAULT CHARSET=utf8 COMMENT='掲載企業詳細-掲載タイプ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_master`
--

DROP TABLE IF EXISTS `client_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `client_no` int(11) NOT NULL COMMENT '掲載企業ID',
  `corp_master_id` int(11) NOT NULL COMMENT '代理店ID',
  `client_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '掲載企業名',
  `client_name_kana` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '掲載企業名カナ',
  `tel_no` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT '電話番号',
  `address` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '住所',
  `tanto_name` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '担当者名',
  `created_at` int(11) NOT NULL COMMENT '登録日',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '取引状態',
  `client_business_outline` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '事業内容',
  `client_corporate_url` varchar(2000) COLLATE utf8_bin DEFAULT NULL COMMENT 'ホームページ',
  `admin_memo` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '運営元メモ',
  `option100` text COLLATE utf8_bin COMMENT 'オプション100',
  `option101` text COLLATE utf8_bin COMMENT 'オプション101',
  `option102` text COLLATE utf8_bin COMMENT 'オプション102',
  `option103` text COLLATE utf8_bin COMMENT 'オプション103',
  `option104` text COLLATE utf8_bin COMMENT 'オプション104',
  `option105` text COLLATE utf8_bin COMMENT 'オプション105',
  `option106` text COLLATE utf8_bin COMMENT 'オプション106',
  `option107` text COLLATE utf8_bin COMMENT 'オプション107',
  `option108` text COLLATE utf8_bin COMMENT 'オプション108',
  `option109` text COLLATE utf8_bin COMMENT 'オプション109',
  PRIMARY KEY (`id`),
  KEY `idx_client_master_client_id` (`client_no`),
  KEY `idx_client_master_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=50015 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='掲載企業';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `complete_mail_domain`
--

DROP TABLE IF EXISTS `complete_mail_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `complete_mail_domain` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `mail_domain` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'オートコンプリートするメールドメイン',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状態',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='メールドメインオートコンプリート';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `corp_column_set`
--

DROP TABLE IF EXISTS `corp_column_set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `corp_column_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `column_no` tinyint(3) unsigned NOT NULL COMMENT 'メニューID',
  `column_name` varchar(30) COLLATE utf8_bin NOT NULL COMMENT 'corp_masterのカラム',
  `label` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `data_type` varchar(10) COLLATE utf8_bin NOT NULL COMMENT '入力方法',
  `max_length` text COLLATE utf8_bin COMMENT '文字数上限',
  `is_must` tinyint(1) DEFAULT NULL COMMENT '入力条件',
  `is_in_list` tinyint(1) DEFAULT NULL COMMENT '検索一覧表示',
  `is_in_search` tinyint(1) DEFAULT NULL COMMENT '検索項目表示',
  `valid_chk` tinyint(1) NOT NULL COMMENT '公開状況',
  PRIMARY KEY (`id`),
  KEY `idx_corp_column_set_1_tenant_id_2_column_name_3_column_no` (`tenant_id`,`column_name`,`column_no`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='代理店情報項目';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `corp_column_subset`
--

DROP TABLE IF EXISTS `corp_column_subset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `corp_column_subset` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主キーID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `column_name` varchar(30) COLLATE utf8_bin NOT NULL COMMENT 'corp_masterのカラム名',
  `subset_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '選択肢項目名',
  PRIMARY KEY (`id`),
  KEY `idx_corp_column_subset_1_tenant_id_2_column_name` (`tenant_id`,`column_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='代理店のオプション項目の選択肢';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `corp_master`
--

DROP TABLE IF EXISTS `corp_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `corp_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `corp_no` int(11) NOT NULL COMMENT '代理店ID',
  `corp_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '代理店名',
  `created_at` int(11) NOT NULL COMMENT '登録日時',
  `tel_no` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `tanto_name` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '担当者名',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '取引状態',
  `option100` text COLLATE utf8_bin COMMENT 'オプション100',
  `option101` text COLLATE utf8_bin COMMENT 'オプション101',
  `option102` text COLLATE utf8_bin COMMENT 'オプション102',
  `option103` text COLLATE utf8_bin COMMENT 'オプション103',
  `option104` text COLLATE utf8_bin COMMENT 'オプション104',
  `option105` text COLLATE utf8_bin COMMENT 'オプション105',
  `option106` text COLLATE utf8_bin COMMENT 'オプション106',
  `option107` text COLLATE utf8_bin COMMENT 'オプション107',
  `option108` text COLLATE utf8_bin COMMENT 'オプション108',
  `option109` text COLLATE utf8_bin COMMENT 'オプション109',
  PRIMARY KEY (`id`),
  KEY `idx_corp_master_corp_id` (`corp_no`),
  KEY `idx_corp_master_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='代理店';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `custom_field`
--

DROP TABLE IF EXISTS `custom_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custom_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主キー',
  `tenant_id` int(11) DEFAULT NULL COMMENT 'テナントID',
  `custom_no` int(11) DEFAULT NULL COMMENT 'カスタムNo',
  `detail` text COMMENT '表示内容',
  `url` varchar(2000) DEFAULT NULL COMMENT 'URL',
  `pict` varchar(255) DEFAULT NULL COMMENT '画像',
  `valid_chk` tinyint(1) DEFAULT '1' COMMENT '公開状況',
  `created_at` int(11) DEFAULT NULL COMMENT '登録日時',
  `updated_at` int(11) DEFAULT NULL COMMENT '更新日時',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10022 DEFAULT CHARSET=utf8 COMMENT='カスタムフィールド';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_type`
--

DROP TABLE IF EXISTS `disp_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `disp_type_no` tinyint(4) NOT NULL COMMENT '掲載タイプコード',
  `disp_type_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '掲載タイプ名',
  `valid_chk` tinyint(1) DEFAULT '1' COMMENT '状態',
  PRIMARY KEY (`id`),
  KEY `idx_disp_type_1_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='掲載タイプ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dist`
--

DROP TABLE IF EXISTS `dist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dist` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pref_no` int(11) NOT NULL COMMENT 'テーブルpref_cdのカラムid',
  `dist_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '市区町村名',
  `dist_sub_cd` int(11) DEFAULT '1' COMMENT '市区町村サブコード',
  `dist_cd` int(11) NOT NULL COMMENT '市区町村コード',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1898 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='市区町村';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `featured_job_set`
--

DROP TABLE IF EXISTS `featured_job_set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `featured_job_set` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `widget_layout_id` int(11) DEFAULT NULL COMMENT 'ウィジェットレイアウト ID',
  `list_orderby` varchar(200) DEFAULT NULL COMMENT '優先表示順定義',
  `disp_type_id` varchar(20) DEFAULT NULL COMMENT '掲載タイプ ID',
  PRIMARY KEY (`id`,`tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `header_footer`
--

DROP TABLE IF EXISTS `header_footer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `header_footer` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主キーID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `logo_file_name` varchar(30) COLLATE utf8_bin NOT NULL COMMENT 'ロゴ画像',
  `tel_no` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '電話番号',
  `header_text1` varchar(20) COLLATE utf8_bin DEFAULT '' COMMENT 'ヘッダーリンク1テキスト',
  `header_text2` varchar(20) COLLATE utf8_bin DEFAULT '' COMMENT 'ヘッダーリンク2テキスト',
  `header_text3` varchar(20) COLLATE utf8_bin DEFAULT '' COMMENT 'ヘッダーリンク3テキスト',
  `header_text4` varchar(20) COLLATE utf8_bin DEFAULT '' COMMENT 'ヘッダーリンク4テキスト',
  `header_text5` varchar(20) COLLATE utf8_bin DEFAULT '' COMMENT 'ヘッダーリンク5テキスト',
  `header_text6` varchar(20) COLLATE utf8_bin DEFAULT '' COMMENT 'ヘッダーリンク6テキスト',
  `header_text7` varchar(20) COLLATE utf8_bin DEFAULT '' COMMENT 'ヘッダーリンク7テキスト',
  `header_text8` varchar(20) COLLATE utf8_bin DEFAULT '' COMMENT 'ヘッダーリンク8テキスト',
  `header_text9` varchar(20) COLLATE utf8_bin DEFAULT '' COMMENT 'ヘッダーリンク9テキスト',
  `header_text10` varchar(20) COLLATE utf8_bin DEFAULT '' COMMENT 'ヘッダーリンク10テキスト',
  `header_url1` text COLLATE utf8_bin COMMENT 'ヘッダーリンク1URL',
  `header_url2` text COLLATE utf8_bin COMMENT 'ヘッダーリンク2URL',
  `header_url3` text COLLATE utf8_bin COMMENT 'ヘッダーリンク3URL',
  `header_url4` text COLLATE utf8_bin COMMENT 'ヘッダーリンク4URL',
  `header_url5` text COLLATE utf8_bin COMMENT 'ヘッダーリンク5URL',
  `header_url6` text COLLATE utf8_bin COMMENT 'ヘッダーリンク6URL',
  `header_url7` text COLLATE utf8_bin COMMENT 'ヘッダーリンク7URL',
  `header_url8` text COLLATE utf8_bin COMMENT 'ヘッダーリンク8URL',
  `header_url9` text COLLATE utf8_bin COMMENT 'ヘッダーリンク9URL',
  `header_url10` text COLLATE utf8_bin COMMENT 'ヘッダーリンク10URL',
  `footer_text1` varchar(20) COLLATE utf8_bin DEFAULT '' COMMENT 'フッターリンク1テキスト',
  `footer_text2` varchar(20) COLLATE utf8_bin DEFAULT '' COMMENT 'フッターリンク2テキスト',
  `footer_text3` varchar(20) COLLATE utf8_bin DEFAULT '' COMMENT 'フッターリンク3テキスト',
  `footer_text4` varchar(20) COLLATE utf8_bin DEFAULT '' COMMENT 'フッターリンク4テキスト',
  `footer_text5` varchar(20) COLLATE utf8_bin DEFAULT '' COMMENT 'フッターリンク5テキスト',
  `footer_text6` varchar(20) COLLATE utf8_bin DEFAULT '' COMMENT 'フッターリンク6テキスト',
  `footer_text7` varchar(20) COLLATE utf8_bin DEFAULT '' COMMENT 'フッターリンク7テキスト',
  `footer_text8` varchar(20) COLLATE utf8_bin DEFAULT '' COMMENT 'フッターリンク8テキスト',
  `footer_text9` varchar(20) COLLATE utf8_bin DEFAULT '' COMMENT 'フッターリンク9テキスト',
  `footer_text10` varchar(20) COLLATE utf8_bin DEFAULT '' COMMENT 'フッターリンク10テキスト',
  `footer_url1` text COLLATE utf8_bin COMMENT 'フッターリンク1URL',
  `footer_url2` text COLLATE utf8_bin COMMENT 'フッターリンク2URL',
  `footer_url3` text COLLATE utf8_bin COMMENT 'フッターリンク3URL',
  `footer_url4` text COLLATE utf8_bin COMMENT 'フッターリンク4URL',
  `footer_url5` text COLLATE utf8_bin COMMENT 'フッターリンク5URL',
  `footer_url6` text COLLATE utf8_bin COMMENT 'フッターリンク6URL',
  `footer_url7` text COLLATE utf8_bin COMMENT 'フッターリンク7URL',
  `footer_url8` text COLLATE utf8_bin COMMENT 'フッターリンク8URL',
  `footer_url9` text COLLATE utf8_bin COMMENT 'フッターリンク9URL',
  `footer_url10` text COLLATE utf8_bin COMMENT 'フッターリンク10URL',
  `copyright` varchar(200) COLLATE utf8_bin DEFAULT '' COMMENT 'コピーライト',
  `tel_text` varchar(50) COLLATE utf8_bin DEFAULT '' COMMENT '電話番号テキスト',
  PRIMARY KEY (`id`),
  KEY `idx_header_footer_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hot_job`
--

DROP TABLE IF EXISTS `hot_job`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hot_job` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主キーID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `title` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'タイトル',
  `disp_amount` int(11) NOT NULL DEFAULT '4' COMMENT '表示する求人原稿数',
  `disp_type_ids` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT '1,2,3' COMMENT '表示する掲載タイプ',
  `text1` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'テキスト1に求人原稿の何を表示するか',
  `text2` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'テキスト2に求人原稿の何を表示するか',
  `text3` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'テキスト3に求人原稿の何を表示するか',
  `text4` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'テキスト4に求人原稿の何を表示するか',
  `text1_length` int(11) NOT NULL DEFAULT '30' COMMENT 'テキスト1の文字数制限',
  `text2_length` int(11) NOT NULL DEFAULT '30' COMMENT 'テキスト2の文字数制限',
  `text3_length` int(11) NOT NULL DEFAULT '30' COMMENT 'テキスト3の文字数制限',
  `text4_length` int(11) NOT NULL DEFAULT '30' COMMENT 'テキスト4の文字数制限',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hot_job_priority`
--

DROP TABLE IF EXISTS `hot_job_priority`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hot_job_priority` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主キーID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `hot_job_id` int(11) NOT NULL COMMENT 'リレーションID',
  `item` varchar(30) COLLATE utf8_bin NOT NULL COMMENT 'レイアウトの優先項目名',
  `disp_priority` int(11) NOT NULL COMMENT '優先順位',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inquiry_column_set`
--

DROP TABLE IF EXISTS `inquiry_column_set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inquiry_column_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `column_no` tinyint(3) unsigned NOT NULL COMMENT '表示用主キー',
  `column_name` varchar(30) NOT NULL COMMENT 'inquiry_masterのカラム',
  `label` varchar(255) NOT NULL COMMENT '項目名',
  `data_type` varchar(255) NOT NULL COMMENT '入力方法',
  `max_length` text COMMENT '長さ',
  `is_must` tinyint(1) DEFAULT NULL COMMENT '入力条件（必須かどうか）',
  `is_in_list` tinyint(1) DEFAULT NULL COMMENT '検索一覧表示',
  `is_in_search` tinyint(1) DEFAULT NULL COMMENT '検索項目表示',
  `valid_chk` tinyint(1) NOT NULL COMMENT '公開状況',
  PRIMARY KEY (`id`),
  KEY `idx_inquiry_column_set_1_tenant_id_2_column_name_3_column_no` (`tenant_id`,`column_name`,`column_no`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8 COMMENT='掲載の問いあわせ項目';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inquiry_column_subset`
--

DROP TABLE IF EXISTS `inquiry_column_subset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inquiry_column_subset` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主キーID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `column_name` varchar(30) COLLATE utf8_bin NOT NULL COMMENT 'inquiry_masterのカラム名',
  `subset_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '選択肢項目名',
  PRIMARY KEY (`id`),
  KEY `idx_inquiry_column_subset_1_tenant_id_2_column_name` (`tenant_id`,`column_name`)
) ENGINE=InnoDB AUTO_INCREMENT=193 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='問い合わせのオプション項目の選択肢';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_access_recommend`
--

DROP TABLE IF EXISTS `job_access_recommend`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_access_recommend` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主キー',
  `job_master_id` int(11) NOT NULL COMMENT '仕事ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `accessed_job_master_id_1` int(11) DEFAULT NULL COMMENT '閲覧した求人原稿の仕事ID1',
  `accessed_job_master_id_2` int(11) DEFAULT NULL COMMENT '閲覧した求人原稿の仕事ID2',
  `accessed_job_master_id_3` int(11) DEFAULT NULL COMMENT '閲覧した求人原稿の仕事ID3',
  `accessed_job_master_id_4` int(11) DEFAULT NULL COMMENT '閲覧した求人原稿の仕事ID4',
  `accessed_job_master_id_5` int(11) DEFAULT NULL COMMENT '閲覧した求人原稿の仕事ID5',
  PRIMARY KEY (`id`),
  KEY `idx_job_access_recommend_1_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=167 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_column_set`
--

DROP TABLE IF EXISTS `job_column_set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_column_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `column_no` tinyint(3) unsigned NOT NULL COMMENT 'メニューID',
  `column_name` varchar(30) COLLATE utf8_bin NOT NULL COMMENT 'job_masterのカラム',
  `label` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `data_type` varchar(10) COLLATE utf8_bin NOT NULL COMMENT '入力方法',
  `max_length` text COLLATE utf8_bin COMMENT '文字数上限',
  `is_must` tinyint(1) DEFAULT NULL COMMENT '入力条件',
  `is_in_list` tinyint(1) DEFAULT NULL COMMENT '検索一覧表示',
  `is_in_search` tinyint(1) DEFAULT NULL COMMENT '検索項目表示',
  `valid_chk` tinyint(1) NOT NULL COMMENT '公開状況',
  `freeword_search_flg` tinyint(1) DEFAULT NULL COMMENT 'フリーワード検索フラグ',
  `short_display` tinyint(4) DEFAULT NULL COMMENT '簡易表示フラグ兼表示順',
  `search_result_display` tinyint(4) DEFAULT NULL COMMENT '検索結果表示フラグ兼表示順',
  `column_explain` varchar(1000) COLLATE utf8_bin DEFAULT NULL COMMENT '項目説明文',
  PRIMARY KEY (`id`),
  KEY `idx_job_column_set_1_tenant_id_2_column_name_3_column_no` (`tenant_id`,`column_name`,`column_no`)
) ENGINE=InnoDB AUTO_INCREMENT=301 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='求人情報項目';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_column_subset`
--

DROP TABLE IF EXISTS `job_column_subset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_column_subset` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主キーID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `column_name` varchar(30) COLLATE utf8_bin NOT NULL COMMENT 'job_masterのカラム名',
  `subset_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '選択肢項目名',
  PRIMARY KEY (`id`),
  KEY `idx_job_column_subset_1_tenant_id_2_column_name` (`tenant_id`,`column_name`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='求人原稿のオプション項目の選択肢';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_dist`
--

DROP TABLE IF EXISTS `job_dist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_dist` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL COMMENT 'テーブルjob_masterのカラムid',
  `dist_id` int(11) NOT NULL COMMENT 'テーブルdist_cdのカラムid',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_dist_job_master_id_dist_id` (`job_master_id`,`dist_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1239792 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='仕事-市区町村関連'
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_master`
--

DROP TABLE IF EXISTS `job_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `job_no` int(11) NOT NULL COMMENT '仕事ナンバー',
  `client_master_id` int(11) NOT NULL COMMENT 'テーブルclient_masterのカラムid',
  `corp_name_disp` text COLLATE utf8_bin COMMENT '会社名',
  `job_pr` text COLLATE utf8_bin COMMENT 'メインキャッチ',
  `main_copy` text COLLATE utf8_bin COMMENT 'コメント',
  `job_comment` text COLLATE utf8_bin COMMENT 'PR',
  `job_type_text` text COLLATE utf8_bin COMMENT '職種（テキスト）',
  `work_place` text COLLATE utf8_bin COMMENT '勤務地（テキスト）',
  `station` text COLLATE utf8_bin COMMENT '最寄り駅',
  `transport` text COLLATE utf8_bin COMMENT '交通',
  `wage_text` text COLLATE utf8_bin COMMENT '給与',
  `requirement` text COLLATE utf8_bin COMMENT '応募資格',
  `conditions` text COLLATE utf8_bin COMMENT '待遇',
  `holidays` text COLLATE utf8_bin COMMENT '休日・休暇',
  `work_period` text COLLATE utf8_bin COMMENT '就労期間',
  `work_time_text` text COLLATE utf8_bin COMMENT '勤務期間（テキスト）',
  `application` text COLLATE utf8_bin COMMENT '応募方法',
  `application_tel_1` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT '連絡先電話番号1',
  `application_tel_2` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT '連絡先電話番号2',
  `application_mail` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '応募先メールアドレス',
  `application_place` text COLLATE utf8_bin COMMENT '面接地',
  `application_staff_name` text COLLATE utf8_bin COMMENT '受付担当者',
  `agent_name` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '営業担当者',
  `disp_start_date` int(11) NOT NULL,
  `disp_end_date` int(11) DEFAULT NULL COMMENT '掲載終了日',
  `created_at` int(11) NOT NULL COMMENT '登録日時',
  `valid_chk` tinyint(1) DEFAULT '1' COMMENT '状態',
  `job_search_number` text COLLATE utf8_bin COMMENT 'お仕事No',
  `job_pict_text_3` text COLLATE utf8_bin COMMENT '画像２（キャプション）',
  `job_pict_text_4` text COLLATE utf8_bin COMMENT '画像３（キャプション）',
  `map_url` text COLLATE utf8_bin COMMENT 'MAPをみる-URL',
  `mail_body` text COLLATE utf8_bin COMMENT '通知メール文面',
  `updated_at` int(11) NOT NULL COMMENT '更新日時',
  `job_pict_text_5` text COLLATE utf8_bin COMMENT '画像４（キャプション）',
  `main_copy2` text COLLATE utf8_bin COMMENT 'コメント2',
  `job_pr2` text COLLATE utf8_bin COMMENT 'メインキャッチ2',
  `option100` text COLLATE utf8_bin COMMENT 'オプション100',
  `option101` text COLLATE utf8_bin COMMENT 'オプション101',
  `option102` text COLLATE utf8_bin COMMENT 'オプション102',
  `option103` text COLLATE utf8_bin COMMENT 'オプション103',
  `option104` text COLLATE utf8_bin COMMENT 'オプション104',
  `option105` text COLLATE utf8_bin COMMENT 'オプション105',
  `option106` text COLLATE utf8_bin COMMENT 'オプション106',
  `option107` text COLLATE utf8_bin COMMENT 'オプション107',
  `option108` text COLLATE utf8_bin COMMENT 'オプション108',
  `option109` text COLLATE utf8_bin COMMENT 'オプション109',
  `import_site_job_id` int(11) DEFAULT NULL COMMENT 'インポートサイト仕事ID',
  `client_charge_plan_id` smallint(6) NOT NULL COMMENT 'テーブルclient_charge_planのカラムid',
  `review_flg` tinyint(4) DEFAULT '3' COMMENT '審査フラグ(0=改変、1=審査依頼中、2=審査NG、3=審査OK)',
  `disp_type_sort` int(11) NOT NULL COMMENT '掲載タイプID',
  `media_upload_id_1` int(11) DEFAULT NULL COMMENT 'テーブルmedia_uploadのカラムid',
  `media_upload_id_2` int(11) DEFAULT NULL COMMENT 'テーブルmedia_uploadのカラムid',
  `media_upload_id_3` int(11) DEFAULT NULL COMMENT 'テーブルmedia_uploadのカラムid',
  `media_upload_id_4` int(11) DEFAULT NULL COMMENT 'テーブルmedia_uploadのカラムid',
  `media_upload_id_5` int(11) DEFAULT NULL COMMENT 'テーブルmedia_uploadのカラムid',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_master_job_no` (`job_no`),
  KEY `idx_job_master_client_master_id` (`client_master_id`)
) ENGINE=InnoDB AUTO_INCREMENT=201732 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC COMMENT='仕事'
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 6 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_master_backup`
--

DROP TABLE IF EXISTS `job_master_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_master_backup` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `job_no` int(11) NOT NULL COMMENT '仕事ナンバー',
  `client_master_id` int(11) NOT NULL COMMENT 'テーブルclient_masterのカラムid',
  `corp_name_disp` text COLLATE utf8_bin COMMENT '会社名',
  `job_pr` text COLLATE utf8_bin COMMENT 'メインキャッチ',
  `main_copy` text COLLATE utf8_bin COMMENT 'コメント',
  `job_comment` text COLLATE utf8_bin COMMENT 'PR',
  `job_type_text` text COLLATE utf8_bin COMMENT '職種（テキスト）',
  `work_place` text COLLATE utf8_bin COMMENT '勤務地（テキスト）',
  `station` text COLLATE utf8_bin COMMENT '最寄り駅',
  `transport` text COLLATE utf8_bin COMMENT '交通',
  `wage_text` text COLLATE utf8_bin COMMENT '給与',
  `requirement` text COLLATE utf8_bin COMMENT '応募資格',
  `conditions` text COLLATE utf8_bin COMMENT '待遇',
  `holidays` text COLLATE utf8_bin COMMENT '休日・休暇',
  `work_period` text COLLATE utf8_bin COMMENT '就労期間',
  `work_time_text` text COLLATE utf8_bin COMMENT '勤務期間（テキスト）',
  `application` text COLLATE utf8_bin COMMENT '応募方法',
  `application_tel_1` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT '連絡先電話番号1',
  `application_tel_2` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT '連絡先電話番号2',
  `application_mail` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '応募先メールアドレス',
  `application_place` text COLLATE utf8_bin COMMENT '面接地',
  `application_staff_name` text COLLATE utf8_bin COMMENT '受付担当者',
  `agent_name` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '営業担当者',
  `disp_start_date` int(11) NOT NULL COMMENT '掲載開始日',
  `disp_end_date` int(11) DEFAULT NULL COMMENT '掲載終了日',
  `created_at` int(11) DEFAULT NULL COMMENT '登録日時',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状態',
  `job_search_number` text COLLATE utf8_bin COMMENT 'お仕事No',
  `job_pict_text_3` text COLLATE utf8_bin COMMENT '画像２（キャプション）',
  `job_pict_text_4` text COLLATE utf8_bin COMMENT '画像３（キャプション）',
  `map_url` text COLLATE utf8_bin COMMENT 'MAPをみる-URL',
  `mail_body` text COLLATE utf8_bin COMMENT '通知メール文面',
  `updated_at` int(11) DEFAULT NULL COMMENT '更新日時',
  `job_pict_text_5` text COLLATE utf8_bin COMMENT '画像４（キャプション）',
  `main_copy2` text COLLATE utf8_bin COMMENT 'コメント2',
  `job_pr2` text COLLATE utf8_bin COMMENT 'メインキャッチ2',
  `option100` text COLLATE utf8_bin COMMENT 'オプション100',
  `option101` text COLLATE utf8_bin COMMENT 'オプション101',
  `option102` text COLLATE utf8_bin COMMENT 'オプション102',
  `option103` text COLLATE utf8_bin COMMENT 'オプション103',
  `option104` text COLLATE utf8_bin COMMENT 'オプション104',
  `option105` text COLLATE utf8_bin COMMENT 'オプション105',
  `option106` text COLLATE utf8_bin COMMENT 'オプション106',
  `option107` text COLLATE utf8_bin COMMENT 'オプション107',
  `option108` text COLLATE utf8_bin COMMENT 'オプション108',
  `option109` text COLLATE utf8_bin COMMENT 'オプション109',
  `import_site_job_id` int(11) DEFAULT NULL COMMENT 'インポートサイト仕事ID',
  `client_charge_plan_id` smallint(6) NOT NULL COMMENT 'テーブルclient_charge_planのカラムid',
  `review_flg` tinyint(4) DEFAULT '3' COMMENT '審査フラグ(0=改変、1=審査依頼中、2=審査NG、3=審査OK)',
  `deleted_at` int(11) NOT NULL COMMENT '削除日時',
  `media_upload_id_1` int(11) DEFAULT NULL COMMENT 'テーブルmedia_uploadのカラムid',
  `media_upload_id_2` int(11) DEFAULT NULL COMMENT 'テーブルmedia_uploadのカラムid',
  `media_upload_id_3` int(11) DEFAULT NULL COMMENT 'テーブルmedia_uploadのカラムid',
  `media_upload_id_4` int(11) DEFAULT NULL COMMENT 'テーブルmedia_uploadのカラムid',
  `media_upload_id_5` int(11) DEFAULT NULL COMMENT 'テーブルmedia_uploadのカラムid',
  `disp_type_sort` int(11) NOT NULL COMMENT 'おすすめ順',
  PRIMARY KEY (`id`,`tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='求人情報完全削除前バックアップ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_pref`
--

DROP TABLE IF EXISTS `job_pref`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_pref` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL COMMENT 'テーブルjob_masterのカラムid',
  `pref_id` smallint(6) NOT NULL COMMENT '都道府県コード',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_pref_job_master_id_pref_id` (`job_master_id`,`pref_id`)
) ENGINE=InnoDB AUTO_INCREMENT=936731 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='仕事-都道府県関連'
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_searchkey_item1`
--

DROP TABLE IF EXISTS `job_searchkey_item1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_searchkey_item1` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_id` int(11) NOT NULL COMMENT '外部キー',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_searchkey_item1_tenant_id` (`tenant_id`),
  KEY `idx_job_searchkey_item1_job_id` (`job_master_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1056065 DEFAULT CHARSET=utf8 COLLATE=utf8_bin
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_searchkey_item10`
--

DROP TABLE IF EXISTS `job_searchkey_item10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_searchkey_item10` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_id` int(11) NOT NULL COMMENT '外部キー',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_searchkey_item10_tenant_id` (`tenant_id`),
  KEY `idx_job_searchkey_item10_job_id` (`job_master_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_searchkey_item11`
--

DROP TABLE IF EXISTS `job_searchkey_item11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_searchkey_item11` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_id` int(11) NOT NULL COMMENT '外部キー',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_searchkey_item11_tenant_id` (`tenant_id`),
  KEY `idx_job_searchkey_item11_job_id` (`job_master_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1021385 DEFAULT CHARSET=utf8 COLLATE=utf8_bin
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_searchkey_item12`
--

DROP TABLE IF EXISTS `job_searchkey_item12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_searchkey_item12` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_id` int(11) NOT NULL COMMENT '外部キー',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_searchkey_item12_tenant_id` (`tenant_id`),
  KEY `idx_job_searchkey_item12_job_id` (`job_master_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1034191 DEFAULT CHARSET=utf8 COLLATE=utf8_bin
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_searchkey_item13`
--

DROP TABLE IF EXISTS `job_searchkey_item13`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_searchkey_item13` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_id` int(11) NOT NULL COMMENT '外部キー',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_searchkey_item13_tenant_id` (`tenant_id`),
  KEY `idx_job_searchkey_item13_job_id` (`job_master_id`)
) ENGINE=InnoDB AUTO_INCREMENT=989661 DEFAULT CHARSET=utf8 COLLATE=utf8_bin
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_searchkey_item14`
--

DROP TABLE IF EXISTS `job_searchkey_item14`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_searchkey_item14` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_id` int(11) NOT NULL COMMENT '外部キー',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_searchkey_item14_tenant_id` (`tenant_id`),
  KEY `idx_job_searchkey_item14_job_id` (`job_master_id`)
) ENGINE=InnoDB AUTO_INCREMENT=989663 DEFAULT CHARSET=utf8 COLLATE=utf8_bin
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_searchkey_item15`
--

DROP TABLE IF EXISTS `job_searchkey_item15`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_searchkey_item15` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_id` int(11) NOT NULL COMMENT '外部キー',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_searchkey_item15_tenant_id` (`tenant_id`),
  KEY `idx_job_searchkey_item15_job_id` (`job_master_id`)
) ENGINE=InnoDB AUTO_INCREMENT=989660 DEFAULT CHARSET=utf8 COLLATE=utf8_bin
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_searchkey_item16`
--

DROP TABLE IF EXISTS `job_searchkey_item16`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_searchkey_item16` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_id` int(11) NOT NULL COMMENT '外部キー',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_searchkey_item16_tenant_id` (`tenant_id`),
  KEY `idx_job_searchkey_item16_job_id` (`job_master_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_searchkey_item17`
--

DROP TABLE IF EXISTS `job_searchkey_item17`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_searchkey_item17` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_id` int(11) NOT NULL COMMENT '外部キー',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_searchkey_item17_tenant_id` (`tenant_id`),
  KEY `idx_job_searchkey_item17_job_id` (`job_master_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_searchkey_item18`
--

DROP TABLE IF EXISTS `job_searchkey_item18`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_searchkey_item18` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_id` int(11) NOT NULL COMMENT '外部キー',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_searchkey_item18_tenant_id` (`tenant_id`),
  KEY `idx_job_searchkey_item18_job_id` (`job_master_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_searchkey_item19`
--

DROP TABLE IF EXISTS `job_searchkey_item19`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_searchkey_item19` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_id` int(11) NOT NULL COMMENT '外部キー',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_searchkey_item19_tenant_id` (`tenant_id`),
  KEY `idx_job_searchkey_item19_job_id` (`job_master_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_searchkey_item2`
--

DROP TABLE IF EXISTS `job_searchkey_item2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_searchkey_item2` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_id` int(11) NOT NULL COMMENT '外部キー',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_searchkey_item2_tenant_id` (`tenant_id`),
  KEY `idx_job_searchkey_item2_job_id` (`job_master_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1056165 DEFAULT CHARSET=utf8 COLLATE=utf8_bin
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_searchkey_item20`
--

DROP TABLE IF EXISTS `job_searchkey_item20`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_searchkey_item20` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_id` int(11) NOT NULL COMMENT '外部キー',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_searchkey_item20_tenant_id` (`tenant_id`),
  KEY `idx_job_searchkey_item20_job_id` (`job_master_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_searchkey_item3`
--

DROP TABLE IF EXISTS `job_searchkey_item3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_searchkey_item3` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_id` int(11) NOT NULL COMMENT '外部キー',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_searchkey_item3_tenant_id` (`tenant_id`),
  KEY `idx_job_searchkey_item3_job_id` (`job_master_id`)
) ENGINE=InnoDB AUTO_INCREMENT=923573 DEFAULT CHARSET=utf8 COLLATE=utf8_bin
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_searchkey_item4`
--

DROP TABLE IF EXISTS `job_searchkey_item4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_searchkey_item4` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_id` int(11) NOT NULL COMMENT '外部キー',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_searchkey_item4_tenant_id` (`tenant_id`),
  KEY `idx_job_searchkey_item4_job_id` (`job_master_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1055889 DEFAULT CHARSET=utf8 COLLATE=utf8_bin
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_searchkey_item5`
--

DROP TABLE IF EXISTS `job_searchkey_item5`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_searchkey_item5` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_id` int(11) NOT NULL COMMENT '外部キー',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_searchkey_item5_tenant_id` (`tenant_id`),
  KEY `idx_job_searchkey_item5_job_id` (`job_master_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1057841 DEFAULT CHARSET=utf8 COLLATE=utf8_bin
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_searchkey_item6`
--

DROP TABLE IF EXISTS `job_searchkey_item6`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_searchkey_item6` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_id` int(11) NOT NULL COMMENT '外部キー',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_searchkey_item6_tenant_id` (`tenant_id`),
  KEY `idx_job_searchkey_item6_job_id` (`job_master_id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8 COLLATE=utf8_bin
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_searchkey_item7`
--

DROP TABLE IF EXISTS `job_searchkey_item7`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_searchkey_item7` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_id` int(11) NOT NULL COMMENT '外部キー',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_searchkey_item7_tenant_id` (`tenant_id`),
  KEY `idx_job_searchkey_item7_job_id` (`job_master_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_bin
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_searchkey_item8`
--

DROP TABLE IF EXISTS `job_searchkey_item8`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_searchkey_item8` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_id` int(11) NOT NULL COMMENT '外部キー',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_searchkey_item8_tenant_id` (`tenant_id`),
  KEY `idx_job_searchkey_item8_job_id` (`job_master_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_searchkey_item9`
--

DROP TABLE IF EXISTS `job_searchkey_item9`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_searchkey_item9` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_id` int(11) NOT NULL COMMENT '外部キー',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_searchkey_item9_tenant_id` (`tenant_id`),
  KEY `idx_job_searchkey_item9_job_id` (`job_master_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_station_info`
--

DROP TABLE IF EXISTS `job_station_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_station_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL COMMENT 'テーブルjob_masterのカラムid',
  `station_id` int(11) NOT NULL COMMENT 'テーブルstation_cdのカラムstation_cd',
  `transport_type` tinyint(4) NOT NULL COMMENT '交通手段(0=徒歩、1=バス)',
  `transport_time` int(11) DEFAULT '1' COMMENT '駅からの所要時間',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_station_info_job_master_id` (`job_master_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1046896 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='仕事-駅関連'
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_type`
--

DROP TABLE IF EXISTS `job_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL COMMENT 'テーブルjob_masterのカラムid',
  `job_type_small_id` int(11) NOT NULL COMMENT 'テーブルjob_type_small_cdのカラムid',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_type_job_master_id` (`job_master_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1542503 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='職種小';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_type_big`
--

DROP TABLE IF EXISTS `job_type_big`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_type_big` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `job_type_big_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '職種大名',
  `valid_chk` tinyint(1) DEFAULT '1' COMMENT '状態',
  `sort` int(11) DEFAULT '0' COMMENT '表示順',
  `job_type_category_id` int(11) DEFAULT NULL COMMENT 'テーブルjob_type_categoryのカラムid',
  `job_type_big_no` int(11) NOT NULL COMMENT '職種大コード',
  PRIMARY KEY (`id`),
  KEY `idx_job_type_big_1_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=151 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='職種大コード';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_type_category`
--

DROP TABLE IF EXISTS `job_type_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_type_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `job_type_category_cd` int(11) NOT NULL COMMENT '職種カテゴリコード',
  `name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '職種カテゴリ名',
  `sort` int(11) NOT NULL COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状態',
  PRIMARY KEY (`id`),
  KEY `idx_job_type_category_1_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='職種カテゴリ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_type_small`
--

DROP TABLE IF EXISTS `job_type_small`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_type_small` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `job_type_small_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '職種小名',
  `job_type_big_id` int(11) NOT NULL COMMENT 'テーブルjob_type_big_cdのカラムid',
  `valid_chk` tinyint(1) DEFAULT '1' COMMENT '状態',
  `sort` int(11) DEFAULT NULL COMMENT '表示順',
  `job_type_small_no` int(11) NOT NULL COMMENT '職種小コード',
  PRIMARY KEY (`id`),
  KEY `idx_job_type_small_1_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1501 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='職種小コード';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_wage`
--

DROP TABLE IF EXISTS `job_wage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_wage` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `job_master_id` int(11) NOT NULL COMMENT 'テーブルjob_masterのカラムid',
  `wage_item_id` int(11) NOT NULL COMMENT 'テーブルwage_masterのカラムid',
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `idx_job_wage_job_master_id` (`job_master_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1640881 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='仕事-給与関連'
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `list_disp`
--

DROP TABLE IF EXISTS `list_disp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `list_disp` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `column_name` varchar(30) COLLATE utf8_bin NOT NULL COMMENT 'job_masterのカラム',
  `sort_no` int(11) NOT NULL COMMENT '表示順',
  `disp_type_id` int(11) NOT NULL COMMENT '掲載タイプ',
  PRIMARY KEY (`id`),
  KEY `idx_list_disp_1_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1468 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='詳細順番-掲載タイプ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mail_send`
--

DROP TABLE IF EXISTS `mail_send`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_send` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `mail_type_id` int(11) NOT NULL DEFAULT '0',
  `entity_id` int(11) NOT NULL DEFAULT '0',
  `mail_title` varchar(200) COLLATE utf8_bin NOT NULL DEFAULT '',
  `mail_body` text COLLATE utf8_bin NOT NULL,
  `from_name` varchar(200) COLLATE utf8_bin NOT NULL DEFAULT '',
  `from_mail_address` varchar(200) COLLATE utf8_bin NOT NULL DEFAULT '',
  `bcc_mail_address` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `send_pc_chk` tinyint(1) NOT NULL DEFAULT '0',
  `send_mobile_chk` tinyint(1) NOT NULL DEFAULT '0',
  `send_start_time` int(11) NOT NULL DEFAULT '0',
  `draft_chk` tinyint(1) NOT NULL DEFAULT '0',
  `send_count` int(11) NOT NULL DEFAULT '0',
  `no_send_count` int(11) NOT NULL DEFAULT '0',
  `send_status` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`,`tenant_id`),
  KEY `ix_mail_send_1_mail_type_id_2_entity_id` (`mail_type_id`,`entity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3700 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mail_send_user`
--

DROP TABLE IF EXISTS `mail_send_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_send_user` (
  `tenant_id` int(11) NOT NULL DEFAULT '0',
  `mail_send_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `pc_mail_address` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `mobile_mail_address` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `send_pc_chk` tinyint(1) NOT NULL DEFAULT '0',
  `send_mobile_chk` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`tenant_id`,`mail_send_id`,`user_id`),
  KEY `ix_mail_send_user_1_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mail_send_user_log`
--

DROP TABLE IF EXISTS `mail_send_user_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_send_user_log` (
  `tenant_id` int(11) NOT NULL DEFAULT '0',
  `mail_send_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `from_user_id` int(11) NOT NULL DEFAULT '0',
  `from_name` varchar(200) COLLATE utf8_bin NOT NULL DEFAULT '',
  `from_mail_address` varchar(200) COLLATE utf8_bin NOT NULL DEFAULT '',
  `mail_title` varchar(200) COLLATE utf8_bin NOT NULL DEFAULT '',
  `mail_body` text COLLATE utf8_bin NOT NULL,
  `pc_mail_address` varchar(200) COLLATE utf8_bin NOT NULL DEFAULT '',
  `mobile_mail_address` varchar(200) COLLATE utf8_bin NOT NULL DEFAULT '',
  `send_pc_status` int(11) NOT NULL DEFAULT '0',
  `send_mobile_status` int(11) NOT NULL DEFAULT '0',
  `send_date` int(11) NOT NULL DEFAULT '0',
  `result` text COLLATE utf8_bin,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`tenant_id`,`mail_send_id`,`user_id`),
  KEY `ix_mail_send_user_log_1_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `main_disp`
--

DROP TABLE IF EXISTS `main_disp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `main_disp` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `main_disp_name` varchar(20) COLLATE utf8_bin NOT NULL COMMENT '詳細メイン名',
  `disp_type_id` tinyint(4) NOT NULL COMMENT '掲載タイプ',
  `column_name` varchar(30) COLLATE utf8_bin NOT NULL COMMENT 'job_masterのカラム',
  `disp_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '表示チェック',
  PRIMARY KEY (`id`),
  KEY `idx_main_disp_1_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=226 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='詳細メイン-掲載タイプ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manage_menu_category`
--

DROP TABLE IF EXISTS `manage_menu_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manage_menu_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主キーID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `title` varchar(20) COLLATE utf8_bin DEFAULT NULL COMMENT '管理メニュー大項目名',
  `sort` smallint(6) DEFAULT NULL COMMENT '表示順',
  `icon_key` varchar(20) COLLATE utf8_bin DEFAULT NULL COMMENT 'アイコン表示用class',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状態',
  `manage_menu_category_no` int(11) NOT NULL DEFAULT '0' COMMENT 'カテゴリNo',
  PRIMARY KEY (`id`),
  KEY `idx_manage_menu_category_1_tenant_id_2_sort` (`tenant_id`,`sort`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='管理メニュー大項目';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manage_menu_main`
--

DROP TABLE IF EXISTS `manage_menu_main`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manage_menu_main` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `manage_menu_category_id` int(11) DEFAULT NULL COMMENT '管理画面大メニューID',
  `title` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'タイトル',
  `href` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'URL',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状態',
  `sort` smallint(6) NOT NULL DEFAULT '0' COMMENT '表示順',
  `icon_key` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `permitted_role` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `exception` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_manage_menu_main_manage_menu_category_id` (`manage_menu_category_id`),
  KEY `idx_manage_menu_main_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=321 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='管理者メニュー';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manager_session`
--

DROP TABLE IF EXISTS `manager_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manager_session` (
  `id` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'SESSION ID',
  `admin_id` int(11) DEFAULT NULL COMMENT '管理者ID',
  `expire` int(11) DEFAULT NULL COMMENT '有効期限',
  `data` text COLLATE utf8_bin COMMENT 'データ',
  PRIMARY KEY (`id`),
  KEY `idx_manager_session_admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `media_upload`
--

DROP TABLE IF EXISTS `media_upload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media_upload` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `save_file_name` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '保存用ファイル名',
  `updated_at` int(11) NOT NULL COMMENT '更新日時',
  `admin_master_id` int(11) DEFAULT NULL COMMENT 'テーブルadmin_masterのカラムid',
  `client_master_id` int(11) DEFAULT NULL COMMENT 'テーブルclient_masterのカラムid',
  `file_size` int(11) DEFAULT NULL COMMENT 'ファイルサイズ(Byte)',
  `created_at` int(11) NOT NULL COMMENT '登録日時',
  `disp_file_name` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '表示用ファイル名',
  `tag` varchar(50) COLLATE utf8_bin DEFAULT NULL COMMENT '画像検索用タグ',
  PRIMARY KEY (`id`),
  KEY `idx_media_upload_1_tenant_id` (`tenant_id`),
  KEY `idx_media_upload_client_master_id` (`client_master_id`),
  KEY `idx_media_upload_tag` (`tag`)
) ENGINE=InnoDB AUTO_INCREMENT=250225 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='サンプル画像';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `member_master`
--

DROP TABLE IF EXISTS `member_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `member_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `member_no` int(11) NOT NULL COMMENT '登録者ナンバー',
  `login_id` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'ログインＩＤ',
  `password` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'パスワード',
  `name_sei` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '名前(性)',
  `name_mei` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '名前(名)',
  `kana_sei` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'かな(性)',
  `kana_mei` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'かな(名)',
  `sex_type` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '性別',
  `birth_date` date DEFAULT NULL COMMENT '誕生日',
  `mail_address_flg` tinyint(4) DEFAULT NULL COMMENT 'メールアドレス判別フラグ',
  `mail_address` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `occupation_id` smallint(6) DEFAULT NULL COMMENT '属性コード',
  `area_id` smallint(6) DEFAULT NULL COMMENT 'エリアコード',
  `option100` text COLLATE utf8_bin COMMENT 'オプション項目100',
  `option101` text COLLATE utf8_bin COMMENT 'オプション項目101',
  `option102` text COLLATE utf8_bin COMMENT 'オプション項目102',
  `option103` text COLLATE utf8_bin COMMENT 'オプション項目103',
  `option104` text COLLATE utf8_bin COMMENT 'オプション項目104',
  `option105` text COLLATE utf8_bin COMMENT 'オプション項目105',
  `option106` text COLLATE utf8_bin COMMENT 'オプション項目106',
  `option107` text COLLATE utf8_bin COMMENT 'オプション項目107',
  `option108` text COLLATE utf8_bin COMMENT 'オプション項目108',
  `option109` text COLLATE utf8_bin COMMENT 'オプション項目109',
  `created_at` int(11) NOT NULL COMMENT '登録日時',
  `updated_at` int(11) NOT NULL COMMENT '更新日時',
  `carrier_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '登録機器',
  PRIMARY KEY (`id`,`tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='登録'
/*!50100 PARTITION BY HASH (tenant_id)
PARTITIONS 3 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `message_convert`
--

DROP TABLE IF EXISTS `message_convert`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_convert` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主キー',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `content` text COLLATE utf8_bin COMMENT '変換パターンJSON',
  `is_active` tinyint(1) DEFAULT '1' COMMENT '有効フラグ',
  `created_at` int(11) unsigned NOT NULL COMMENT '作成日時',
  `updated_at` int(11) unsigned DEFAULT NULL COMMENT '更新日時',
  PRIMARY KEY (`id`),
  KEY `message_convert-tenant_id-tenant-tenant_id` (`tenant_id`),
  CONSTRAINT `message_convert-tenant_id-tenant-tenant_id` FOREIGN KEY (`tenant_id`) REFERENCES `tenant` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration` (
  `version` varchar(180) COLLATE utf8_bin NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `name_master`
--

DROP TABLE IF EXISTS `name_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `name_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `name_id` int(11) NOT NULL COMMENT '名前ID',
  `change_name` varchar(200) COLLATE utf8_bin NOT NULL COMMENT '変更後名称',
  `default_name` varchar(200) COLLATE utf8_bin NOT NULL COMMENT '初期名称',
  PRIMARY KEY (`id`),
  KEY `idx_name_master_name_id` (`name_id`),
  KEY `idx_name_master_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='名前';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `occupation`
--

DROP TABLE IF EXISTS `occupation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `occupation` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `occupation_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '属性名',
  `valid_chk` tinyint(1) DEFAULT '1' COMMENT '状態',
  `sort` int(11) DEFAULT NULL COMMENT '表示順',
  `occupation_no` int(11) NOT NULL COMMENT '属性コード',
  PRIMARY KEY (`id`),
  KEY `idx_occupation_1_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='属性';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `password_reminder`
--

DROP TABLE IF EXISTS `password_reminder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reminder` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `key_id` int(11) NOT NULL COMMENT '会員・管理者ID',
  `collation_key` varchar(200) COLLATE utf8_bin NOT NULL COMMENT '照合キー',
  `created_at` int(11) NOT NULL COMMENT '申請日時',
  `key_flg` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'アカウントフラグ',
  PRIMARY KEY (`id`),
  KEY `idx_password_reminder_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `policy`
--

DROP TABLE IF EXISTS `policy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `policy` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主キーID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `policy_no` int(11) NOT NULL COMMENT '規約番号',
  `policy_name` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '規約名',
  `description` varchar(50) COLLATE utf8_bin DEFAULT NULL COMMENT 'ディスクリプション',
  `page_type` tinyint(1) DEFAULT '0' COMMENT 'ページ',
  `from_type` tinyint(1) DEFAULT '0' COMMENT 'カテゴリ',
  `policy` text COLLATE utf8_bin NOT NULL COMMENT '規約',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '0' COMMENT '公開状況',
  PRIMARY KEY (`id`),
  KEY `idx_policy_1_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pref`
--

DROP TABLE IF EXISTS `pref`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pref` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `pref_no` int(11) NOT NULL COMMENT '都道府県コード',
  `pref_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '都道府県名',
  `area_id` int(11) DEFAULT NULL COMMENT 'テーブルare_cdのカラムid',
  `sort` int(11) DEFAULT '0' COMMENT '表示順',
  PRIMARY KEY (`id`),
  KEY `idx_pref_1_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=241 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='都道府県';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pref_dist`
--

DROP TABLE IF EXISTS `pref_dist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pref_dist` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `pref_dist_master_id` int(11) NOT NULL COMMENT 'テーブルpref_dist_masterのカラムid',
  `dist_id` int(11) NOT NULL COMMENT 'テーブルdist_cdのカラムid',
  PRIMARY KEY (`id`),
  KEY `idx_pref_dist_1_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21284 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='都道府県-市区町村関連';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pref_dist_master`
--

DROP TABLE IF EXISTS `pref_dist_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pref_dist_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `pref_id` int(11) NOT NULL COMMENT 'テーブルpref_cdのカラムid',
  `pref_dist_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '地域名',
  `valid_chk` tinyint(1) DEFAULT '1' COMMENT '状態',
  `sort` smallint(6) DEFAULT NULL COMMENT '表示順',
  `pref_dist_master_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_pref_dist_master_1_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9490 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='地域';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_category1`
--

DROP TABLE IF EXISTS `searchkey_category1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_category1` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_category_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'カテゴリ名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_category_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_category1_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_category1_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_category10`
--

DROP TABLE IF EXISTS `searchkey_category10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_category10` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_category_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'カテゴリ名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_category_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_category10_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_category10_valid_chk` (`valid_chk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_category2`
--

DROP TABLE IF EXISTS `searchkey_category2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_category2` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_category_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'カテゴリ名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_category_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_category2_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_category2_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_category3`
--

DROP TABLE IF EXISTS `searchkey_category3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_category3` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_category_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'カテゴリ名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_category_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_category3_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_category3_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_category4`
--

DROP TABLE IF EXISTS `searchkey_category4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_category4` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_category_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'カテゴリ名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_category_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_category4_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_category4_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_category5`
--

DROP TABLE IF EXISTS `searchkey_category5`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_category5` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_category_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'カテゴリ名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_category_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_category5_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_category5_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_category6`
--

DROP TABLE IF EXISTS `searchkey_category6`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_category6` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_category_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'カテゴリ名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_category_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_category6_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_category6_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_category7`
--

DROP TABLE IF EXISTS `searchkey_category7`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_category7` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_category_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'カテゴリ名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_category_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_category7_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_category7_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_category8`
--

DROP TABLE IF EXISTS `searchkey_category8`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_category8` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_category_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'カテゴリ名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_category_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_category8_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_category8_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_category9`
--

DROP TABLE IF EXISTS `searchkey_category9`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_category9` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_category_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'カテゴリ名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_category_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_category9_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_category9_valid_chk` (`valid_chk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_item1`
--

DROP TABLE IF EXISTS `searchkey_item1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_item1` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_category_id` int(11) DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_item_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_item1_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_item1_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=167 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_item10`
--

DROP TABLE IF EXISTS `searchkey_item10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_item10` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_category_id` int(11) DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_item_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_item10_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_item10_valid_chk` (`valid_chk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_item11`
--

DROP TABLE IF EXISTS `searchkey_item11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_item11` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_item_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_item_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_item11_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_item11_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_item12`
--

DROP TABLE IF EXISTS `searchkey_item12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_item12` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_item_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_item_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_item12_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_item12_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_item13`
--

DROP TABLE IF EXISTS `searchkey_item13`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_item13` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_item_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_item_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_item13_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_item13_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_item14`
--

DROP TABLE IF EXISTS `searchkey_item14`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_item14` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_item_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_item_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_item14_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_item14_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_item15`
--

DROP TABLE IF EXISTS `searchkey_item15`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_item15` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_item_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_item_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_item15_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_item15_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_item16`
--

DROP TABLE IF EXISTS `searchkey_item16`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_item16` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_item_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_item_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_item16_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_item16_valid_chk` (`valid_chk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_item17`
--

DROP TABLE IF EXISTS `searchkey_item17`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_item17` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_item_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_item_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_item17_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_item17_valid_chk` (`valid_chk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_item18`
--

DROP TABLE IF EXISTS `searchkey_item18`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_item18` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_item_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_item_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_item18_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_item18_valid_chk` (`valid_chk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_item19`
--

DROP TABLE IF EXISTS `searchkey_item19`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_item19` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_item_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_item_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_item19_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_item19_valid_chk` (`valid_chk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_item2`
--

DROP TABLE IF EXISTS `searchkey_item2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_item2` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_category_id` int(11) DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_item_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_item2_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_item2_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=566 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_item20`
--

DROP TABLE IF EXISTS `searchkey_item20`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_item20` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_item_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_item_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_item20_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_item20_valid_chk` (`valid_chk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_item3`
--

DROP TABLE IF EXISTS `searchkey_item3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_item3` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_category_id` int(11) DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_item_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_item3_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_item3_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_item4`
--

DROP TABLE IF EXISTS `searchkey_item4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_item4` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_category_id` int(11) DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_item_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_item4_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_item4_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_item5`
--

DROP TABLE IF EXISTS `searchkey_item5`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_item5` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_category_id` int(11) DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_item_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_item5_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_item5_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_item6`
--

DROP TABLE IF EXISTS `searchkey_item6`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_item6` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_category_id` int(11) DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_item_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_item6_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_item6_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_item7`
--

DROP TABLE IF EXISTS `searchkey_item7`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_item7` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_category_id` int(11) DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_item_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_item7_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_item7_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_item8`
--

DROP TABLE IF EXISTS `searchkey_item8`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_item8` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_category_id` int(11) DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_item_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_item8_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_item8_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_item9`
--

DROP TABLE IF EXISTS `searchkey_item9`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_item9` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_category_id` int(11) DEFAULT '0' COMMENT '外部キー',
  `searchkey_item_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '項目名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `searchkey_item_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_item9_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_item9_valid_chk` (`valid_chk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `searchkey_master`
--

DROP TABLE IF EXISTS `searchkey_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchkey_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主キー',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `searchkey_no` int(11) NOT NULL DEFAULT '0' COMMENT '表示用主キー',
  `table_name` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'テーブル名',
  `searchkey_name` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '検索キー名',
  `first_hierarchy_cd` varchar(10) COLLATE utf8_bin DEFAULT NULL COMMENT '第一階層URLコード',
  `second_hierarchy_cd` varchar(10) COLLATE utf8_bin DEFAULT NULL COMMENT '第二階層URLコード',
  `third_hierarchy_cd` varchar(10) COLLATE utf8_bin DEFAULT NULL COMMENT '第三階層URLコード',
  `is_category_label` tinyint(1) DEFAULT NULL COMMENT 'カテゴリラベル',
  `is_and_search` tinyint(1) DEFAULT NULL COMMENT '検索条件',
  `sort` tinyint(1) DEFAULT NULL COMMENT '表示順',
  `search_input_tool` tinyint(1) DEFAULT NULL COMMENT '表示タイプ',
  `is_on_top` tinyint(1) DEFAULT NULL COMMENT '表示ページ',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `job_relation_table` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT 'job_masterとの中間テーブル',
  `icon_flg` tinyint(1) DEFAULT NULL COMMENT 'アイコン表示フラグ',
  `principal_flg` tinyint(1) DEFAULT NULL COMMENT '優先キーフラグ',
  PRIMARY KEY (`id`),
  KEY `idx_searchkey_master_tenant_id` (`tenant_id`),
  KEY `idx_searchkey_master_table_name` (`table_name`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='検索キーマスター';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `send_mail_set`
--

DROP TABLE IF EXISTS `send_mail_set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `send_mail_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主キーID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `from_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '差出人名',
  `from_address` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '差出人メールアドレス',
  `subject` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '件名',
  `contents` varchar(2000) COLLATE utf8_bin NOT NULL COMMENT 'メール文面',
  `mail_sign` varchar(1000) COLLATE utf8_bin NOT NULL COMMENT '署名',
  `mail_to` tinyint(3) unsigned NOT NULL COMMENT '対象者',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状態',
  `mail_name` varchar(20) COLLATE utf8_bin NOT NULL COMMENT 'メール名称',
  `sort` tinyint(3) unsigned DEFAULT '0' COMMENT '表示順',
  `mail_type` varchar(20) COLLATE utf8_bin NOT NULL COMMENT 'メール種別',
  `mail_type_id` tinyint(4) NOT NULL COMMENT 'メールのタイプ',
  `notification_address` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `mail_to_description` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `send_mail_set_PKI` (`id`),
  KEY `idx_send_mail_set_1_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site_html`
--

DROP TABLE IF EXISTS `site_html`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_html` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主キーID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `header_html` text COLLATE utf8_bin COMMENT 'ヘッダーHTML',
  `footer_html` text COLLATE utf8_bin COMMENT 'フッターHTML',
  `updated_at` int(11) NOT NULL COMMENT '更新日時',
  `analytics_html` text COLLATE utf8_bin COMMENT 'アナリティクスタグHTML',
  `conversion_html` text COLLATE utf8_bin COMMENT 'コンバージョンタグHTML',
  `remarketing_html` text COLLATE utf8_bin COMMENT 'リマーケティングタグHTML',
  `another_html` text COLLATE utf8_bin COMMENT 'その他解析タグHTML',
  PRIMARY KEY (`id`),
  KEY `idx_site_html_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site_master`
--

DROP TABLE IF EXISTS `site_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `site_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'サイト名',
  `site_title` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'PCサイトタイトル',
  PRIMARY KEY (`id`),
  KEY `idx_site_master_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='サイト設定';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `social_button`
--

DROP TABLE IF EXISTS `social_button`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `social_button` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `option_social_button_no` int(11) NOT NULL COMMENT 'ソーシャルボタンナンバー',
  `social_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'ソーシャル名',
  `social_script` text COLLATE utf8_bin NOT NULL COMMENT 'スクリプト',
  `social_meta` text COLLATE utf8_bin NOT NULL COMMENT 'メタタグ',
  `valid_chk` tinyint(1) DEFAULT '1' COMMENT '状態',
  PRIMARY KEY (`id`),
  KEY `idx_social_button_1_tenant_id` (`tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='ソーシャルボタン';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `station`
--

DROP TABLE IF EXISTS `station`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `station` (
  `railroad_company_cd` int(11) NOT NULL,
  `railroad_company_name` varchar(100) COLLATE utf8_bin NOT NULL,
  `route_cd` int(11) NOT NULL,
  `route_name` varchar(100) COLLATE utf8_bin NOT NULL,
  `station_no` int(11) NOT NULL,
  `station_name` varchar(100) COLLATE utf8_bin NOT NULL,
  `station_name_kana` varchar(100) COLLATE utf8_bin NOT NULL,
  `sort_no` int(11) NOT NULL,
  `pref_no` int(11) NOT NULL,
  PRIMARY KEY (`railroad_company_cd`,`route_cd`,`station_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tenant`
--

DROP TABLE IF EXISTS `tenant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tenant` (
  `tenant_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'テナントID',
  `tenant_code` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'テナントコード(ドメイン名)',
  `kyujin_detail_dir` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '求人詳細ディレクトリ名',
  `tenant_name` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'テナント名(サイト名)',
  `company_name` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '会社名',
  `language_code` char(2) COLLATE utf8_bin NOT NULL COMMENT '言語コード',
  `created_at` int(11) NOT NULL COMMENT '作成日時',
  `updated_at` int(11) NOT NULL COMMENT '更新日時',
  `del_chk` tinyint(1) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
  PRIMARY KEY (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='テナント';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tool_master`
--

DROP TABLE IF EXISTS `tool_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tool_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主キーID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `tool_no` int(11) NOT NULL COMMENT 'タグNo',
  `page_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'ページ名',
  `title` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'title',
  `description` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'description',
  `keywords` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'keywords',
  `h1` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'h1',
  PRIMARY KEY (`id`),
  KEY `idx_tool_master_tool_no` (`tool_no`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_session`
--

DROP TABLE IF EXISTS `user_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_session` (
  `id` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'SESSION ID',
  `expire` int(11) DEFAULT NULL COMMENT '有効期限',
  `data` text COLLATE utf8_bin COMMENT 'データ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wage_category`
--

DROP TABLE IF EXISTS `wage_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wage_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `wage_category_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'カテゴリ名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '表示順',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `wage_category_no` int(11) NOT NULL COMMENT '検索URLに表示されるID',
  PRIMARY KEY (`id`),
  KEY `idx_wage_category_tenant_id` (`tenant_id`),
  KEY `idx_wage_category_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wage_item`
--

DROP TABLE IF EXISTS `wage_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wage_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL DEFAULT '0' COMMENT 'テナントID',
  `wage_category_id` int(11) NOT NULL COMMENT '外部キー',
  `wage_item_no` int(11) NOT NULL COMMENT '給与ナンバー',
  `wage_item_name` int(11) NOT NULL COMMENT '項目名',
  `valid_chk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公開状況',
  `disp_price` varchar(20) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '表示金額',
  PRIMARY KEY (`id`),
  KEY `idx_wage_item_tenant_id` (`tenant_id`),
  KEY `idx_wage_item_valid_chk` (`valid_chk`)
) ENGINE=InnoDB AUTO_INCREMENT=168 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `widget`
--

DROP TABLE IF EXISTS `widget`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `widget` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `widget_no` int(11) NOT NULL COMMENT 'ウィジェットナンバー',
  `widget_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'ウィジェット名',
  `element1` tinyint(4) DEFAULT NULL COMMENT 'コンテンツ内で1番目に表示させる要素',
  `element2` tinyint(4) DEFAULT NULL COMMENT 'コンテンツ内で2番目に表示させる要素',
  `element3` tinyint(4) DEFAULT NULL COMMENT 'コンテンツ内で3番目に表示させる要素',
  `widget_layout_id` smallint(6) DEFAULT NULL COMMENT 'ウィジェットレイアウトID',
  `sort` smallint(6) DEFAULT NULL COMMENT '同じレイアウトIDに登録した場合のウィジェットの表示順',
  `is_disp_widget_name` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'ウィジェット名の表示チェック(0=非表示,1=表示)',
  `style_pc` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'PCでのwidget_data表示スタイル',
  `style_sp` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'SPでのwidget_data表示スタイル',
  `data_per_line_pc` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'PCでの一行あたりのwidget_data表示件数',
  `data_per_line_sp` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'SPでの一行あたりのwidget_data表示件数',
  `is_slider` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'スライド機能ON/OFF',
  PRIMARY KEY (`id`),
  KEY `idx_widget_1_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='ウィジェット';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `widget_data`
--

DROP TABLE IF EXISTS `widget_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `widget_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `widget_id` smallint(6) NOT NULL COMMENT 'テーブルwidgetのカラムid',
  `title` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'タイトル',
  `pict` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '画像',
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'ディスクリプション',
  `sort` int(11) NOT NULL COMMENT '表示順',
  `disp_start_date` int(11) DEFAULT NULL COMMENT '掲載開始日',
  `disp_end_date` int(11) DEFAULT NULL COMMENT '掲載終了日',
  `valid_chk` tinyint(1) DEFAULT '1' COMMENT '状態',
  PRIMARY KEY (`id`),
  KEY `idx_widget_data_1_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=325 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='コンテンツ管理';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `widget_data_area`
--

DROP TABLE IF EXISTS `widget_data_area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `widget_data_area` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主キーID',
  `tenant_id` int(11) DEFAULT NULL COMMENT 'テナントID',
  `widget_data_id` int(11) DEFAULT NULL COMMENT 'widget_dataのID',
  `area_id` int(11) DEFAULT NULL COMMENT 'areaのID',
  `url` varchar(2000) DEFAULT NULL COMMENT 'URL',
  `movie_tag` varchar(255) DEFAULT NULL COMMENT '動画タグ',
  PRIMARY KEY (`id`),
  KEY `idx_widget_data_area_1_tenant_id_2_widget_data_id_3_area_id` (`tenant_id`,`widget_data_id`,`area_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1175 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `widget_layout`
--

DROP TABLE IF EXISTS `widget_layout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `widget_layout` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `area_flg` tinyint(4) DEFAULT NULL COMMENT '全国、エリア判別(全国TOP：0、各エリアTOP共通レイアウト:1)',
  `widget_layout_no` tinyint(4) DEFAULT NULL COMMENT 'ウィジェットレイアウトナンバー(1～6)',
  PRIMARY KEY (`id`),
  KEY `idx_widget_layout_1_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='ウィジェットレイアウト';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-09-11 16:38:50
