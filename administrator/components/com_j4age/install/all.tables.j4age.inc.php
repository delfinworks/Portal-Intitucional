<?php

              
/**
 * @package j4age
 * @copyright Copyright (C) 2009-@THISYEAR@ j4age Team. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * Thank you to the project j4age and it's team, on which roots this project is build on.
 */
             
          

if( !defined( '_JS_STAND_ALONE' ) && !defined( '_JEXEC' ) )
{
	die( 'JS: No Direct Access to '.__FILE__ );
}


/* ############### create tables and insert configuration ############# */


/**
 * New DB as proposed for 3.1.0
 * @author Andreas Halbig
 *
 * Number range (browser_type)
 * 0 = unknown
 * 1 = regular client
 * 2 = Bot
 * 3 = unknown regular client
 * 4 = unknown bot
 */
$quer[] = "
CREATE  TABLE IF NOT EXISTS `#__jstats_browsers` (
  `browser_id` SMALLINT UNSIGNED NOT NULL ,
  `browsertype_id` TINYINT UNSIGNED NOT NULL ,
  `browser_key` VARCHAR(50) NOT NULL ,
  `browser_name` VARCHAR(50) NOT NULL ,
  `browser_img` VARCHAR(12) NOT NULL DEFAULT 'noimage' ,
  `browser_ordering` SMALLINT UNSIGNED NOT NULL DEFAULT 0 ,
  `browser_type` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Browser Types classifies entries \n- 0 = Unknown\n- 1 = Browser\n- 2 = Bot' ,
  `browser_location` TINYINT UNSIGNED NOT NULL DEFAULT 6 COMMENT 'Used to restrict the location!\n\nWe have 8 Bits (= 254)\nValue 1 = Search whole UserAgent Str\nValue 2 = product name\nValue 4 = comment\nValue 8 = version\n=>\nValue \"6\" means, search product name & comment\n' ,
  PRIMARY KEY (`browser_id`) )
ENGINE = MyISAM;";


$quer[] = 'CREATE TABLE IF NOT EXISTS `#__jstats_configuration` ('
        . ' `description` varchar(250) NOT NULL default \'-\','
        . ' `value` varchar(250) default NULL,'
        . ' `params` text default \'\','
        . ' PRIMARY KEY (`description`)'
        . ' ) TYPE=MyISAM';


/**
 * New DB as proposed for 3.1.0
 * @since v3.1.0
 * @author Andreas Halbig
 */
$quer[] = "
CREATE  TABLE IF NOT EXISTS `#__jstats_ipaddresses` (
  `ip` INT(11)  UNSIGNED NOT NULL DEFAULT '0' ,
  `nslookup` VARCHAR(255) NULL DEFAULT NULL ,
  `ip_type` TINYINT UNSIGNED NOT NULL DEFAULT '0',
  `ip_exclude` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `code` VARCHAR(4) NULL ,
  PRIMARY KEY (`ip`) )
ENGINE = MyISAM;
";


/**
 * New DB as proposed for 3.1.0
 *
 *  #__jstats_clients represents a single application such as a browser / bot
 *
 * @since v3.1.0
 * @author Andreas Halbig
 */
$quer[] = "
     CREATE TABLE IF NOT EXISTS `#__jstats_clients` (
        `client_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
        `os_id` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
        `visitor_id` MEDIUMINT UNSIGNED NULL DEFAULT NULL,
        `browser_id` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT 0,
        `browser_version` VARCHAR( 25 ) NULL DEFAULT NULL ,
        `useragent` VARCHAR( 255 ) NOT NULL DEFAULT '',
        `client_type` TINYINT UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Values from 0 - 255',
        `client_exclude` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0',
        PRIMARY KEY ( `client_id` )
    ) ENGINE = MYISAM
";

/**
 * New DB as proposed for 3.1.0
 *
 *  #__jstats_visitors represents one unique Visitor, which allows to link multiple clients to unique visitor.
 *
 * Attention, there is currently no logic available to automatically link multiple clients installed on different machines to one single visitor, so this
 * would require a new view, which gives users the change to bundle clients to one visitor.
 *
 * Possible scenarios for an automatic behaviour is an change of an useragent string such as after an upgrade.
 *
 * @since v3.1.0
 * @author Andreas Halbig
 */
$quer[] = "
CREATE  TABLE IF NOT EXISTS `#__jstats_visitors` (
  `visitor_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `note` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Sometimes you know which user is behind a IP. This field enables to assign a note. ',
  `visitor_exclude` TINYINT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`visitor_id`) )
ENGINE = MyISAM;
";


/*$quer[] = 'CREATE TABLE IF NOT EXISTS `#__jstats_iptocountry` ('
        . ' `IP_FROM` bigint(20) NOT NULL default \'0\','
        . ' `IP_TO` bigint(20) NOT NULL default \'0\','
        . ' `COUNTRY_CODE2` char(2) NOT NULL default \'\','
        . ' `COUNTRY_NAME` varchar(50) NOT NULL default \'\','
        . ' PRIMARY KEY (`IP_FROM`)'
        . ' ) TYPE=MyISAM'; */

/** #__jstats_keywords table will be replaced by #__jstats_keywords - see below
 *
 *  NOTICE: visit_id was introduced in v3.0.0.372 - old data were NOT converted to this value, so it can not be used!! It is introduced to collect data for the future!! (not all data could be converted to new format, that is why now we duplicate data!)
 *
 *  since v3.0.0.382 searchid => searcher_id
 */
$quer[] = 'CREATE TABLE IF NOT EXISTS `#__jstats_keywords` ('
        . ' `timestamp` INT(10) UNSIGNED NOT NULL DEFAULT 0,'
        . ' `searcher_id` mediumint NOT NULL default \'0\','
        . ' `keywords` varchar(255) NOT NULL default \'\','
		. ' `visit_id` MEDIUMINT UNSIGNED NOT NULL,'
        . ' `referrer_id` MEDIUMINT NULL'
        . ' ) TYPE=MyISAM';

/**
 * New proposed DB structure
 *
 * We do not use a automatic SQL Timestamp, because it might be not on line with the PHP time
 * => Might result into different dates on the screen
 *
 * Timestamp only should hold the GTM Timestamp and not consider the current timezone of the machine.
 * The shift in the timezone has to be considered on the presentation layer!!
 *
 * @since v3.1.0
 * @author Andreas Halbig
 */
$quer[] = "
CREATE  TABLE IF NOT EXISTS `#__jstats_impressions` (
  `page_id` MEDIUMINT UNSIGNED NOT NULL ,
  `visit_id` MEDIUMINT UNSIGNED NOT NULL ,
  `timestamp` INT(10) UNSIGNED NOT NULL DEFAULT 0
  )
ENGINE = MyISAM;
";
        
$quer[] = 'CREATE TABLE IF NOT EXISTS `#__jstats_pages` ('
        . ' `page_id` MEDIUMINT UNSIGNED NOT NULL auto_increment,'
        . ' `page` text NOT NULL,'
        . ' `page_title` varchar(255) default NULL,'
        . ' PRIMARY KEY (`page_id`)'
        . ' ) TYPE=MyISAM';

/** #__jstats_referrer table will be replaced by #__jstats_referrers - see below 
 *
 *  NOTICE: visit_id was introduced in v3.0.0.372 - old data were NOT converted to this value, so it can not be used!! It is introduced to collect data for the future!! (not all data could be converted to new format, that is why now we duplicate data!)
 */
$quer[] = 'CREATE TABLE IF NOT EXISTS `#__jstats_referrer` ('
        . ' `referrer` varchar(255) NOT NULL default \'\','
        . ' `domain` varchar(100) NOT NULL default \'unknown\','
        . ' `refid` mediumint(9) NOT NULL auto_increment,'
        . ' `timestamp` INT(10) UNSIGNED NOT NULL DEFAULT 0,'
		. ' `visit_id` MEDIUMINT UNSIGNED NOT NULL,'
        . ' PRIMARY KEY (`refid`),'
        . ' KEY `referrer` (`referrer`),'
        . ' KEY `timestamp` (`timestamp`)'
        . ' ) TYPE=MyISAM';

$quer[] = 'CREATE TABLE IF NOT EXISTS `#__jstats_searchers` ('
        . ' `searcher_id` MEDIUMINT UNSIGNED NOT NULL,'//not auto_increment!
        . ' `searcher_name` varchar(100) NOT NULL,'
        . ' `searcher_domain` varchar(100) NOT NULL,'
        . ' `searcher_key` varchar(50) NOT NULL,'
        . ' PRIMARY KEY (`searcher_id`)'
        . ' ) TYPE=MyISAM';

/**
 * New DB as proposed for 3.1.0
 * @author Andreas Halbig
 */
$quer[] = "
CREATE  TABLE IF NOT EXISTS `#__jstats_systems` (
  `os_id` MEDIUMINT UNSIGNED NOT NULL ,
  `os_key` VARCHAR(25) NOT NULL DEFAULT '' ,
  `os_name` VARCHAR(50) NOT NULL DEFAULT '' ,
  `os_type` TINYINT NOT NULL DEFAULT '0' ,
  `os_img` VARCHAR(12) NOT NULL DEFAULT 'noimage' ,
  `os_ordering` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`os_id`) )
ENGINE = MyISAM
";

//
//  VIRTUAL TABLE  - this table exist, but it is in php code (for performance)
//



/**
 *  Structure of this table changed in version v2.5.0.301 (this table is updated to DB v3.0.0)
 *
 *  visit_id          - prevoiusly id
 *  visitor_id        - prevoiusly ip_id
 *  joomla_userid     - prevoiusly userid  //Joomla CMS use type INT (I know, it is not nice, mediumint is enough) //User ID if user is logged into 'Joomla CMS'. If user is not logged value is 0
 *
 *  visit_date        - Yes, without default!
 *  visit_time        - Yes, without default!   time never should be indexed!!! - it has no sense
 *
 *  Not using defaults - we exacly know what we insert. Moreover inserting with 0 make rows that are not connected with other data and make only confusion. 0 create unusable data 
 *  new from v2.5.0.313
 */
$quer[] = 'CREATE TABLE IF NOT EXISTS #__jstats_visits ('
		. ' `visit_id` MEDIUMINT UNSIGNED NOT NULL auto_increment,'
		. ' `client_id` MEDIUMINT UNSIGNED NULL,'
        . ' `ip` INT(11) UNSIGNED NOT NULL DEFAULT 0,'
		. ' `joomla_userid` MEDIUMINT NOT NULL COMMENT \'Joomla CMS UserId\','
		. ' `changed_at` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT \'visit date in Joomla Local time zone\', '
		. ' PRIMARY KEY (visit_id),'
		. ' KEY `changed_at` (`changed_at`),'
		. ' KEY `client_id` (`client_id`)'
		. ' ) TYPE=MyISAM';


$quer[] = "
CREATE  TABLE IF NOT EXISTS  `#__jstats_cache` (
          `cache_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
          `domain` VARCHAR(45) NULL DEFAULT 'j4age',
          `type` VARCHAR(45) NULL DEFAULT 'default',
          `key` VARCHAR(45) NULL,
          `value` LONGBLOB NULL,
          `timestamp` INT(10) UNSIGNED NOT NULL,
          `ttl` INT(10) UNSIGNED NOT NULL DEFAULT 86400,
          `query` TEXT NULL,
          PRIMARY KEY (`cache_id`) )
";

/**
 * Experimental DB Section
 */

/**
$quer[] = "
CREATE  TABLE IF NOT EXISTS  `#__jstats_domain` (
          `domain_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
          `name` VARCHAR(45) NOT NULL,
          `type` `browser_type` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Browser Types classifies entries \n- 0 = Undefined\n- 1 = Own\n- 2 = Visitor\n- 4 = Referrer' ,
          PRIMARY KEY (`id`, `type`) ),
          KEY `name` (`name`)'
";


$quer[] = "
CREATE  TABLE IF NOT EXISTS  `#__jstats_domain_pages` (
          `page_id` MEDIUMINT UNSIGNED NOT NULL,
          `domain_id` MEDIUMINT UNSIGNED NOT NULL
          PRIMARY KEY (`page_id`, `domain_id`) )
";
 */

