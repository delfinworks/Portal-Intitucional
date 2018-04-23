<?php

              
/**
 * @package j4age
 * @copyright Copyright (C) 2009-@THISYEAR@ j4age Team. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * Thank you to the project j4age and it's team, on which roots this project is build on.
 */
             
          
 
if( !defined( '_JEXEC' ) ) {
	die( 'JS: No Direct Access to '.__FILE__ );
}

function js_UpdateJSDatabaseOnInstall( &$installer, $updateFromJSVersion, $JSConfDef ) {
    $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
	//in 2.3.0 we do not support update from version older than 2.2.3!!!     -do not remove below code, it help us to see full path of changes

	if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '2.2.0', '<') == true) {
		$query = array();
		
		//below update could be applayed earlier than in version '2.2.0' (in version '2.2.0' it was applayed for sure)
		$query[] = "RENAME TABLE #__TFS_bots TO #__jstats_bots, #__TFS_browsers TO #__jstats_browsers, #__TFS_configuration TO #__jstats_configuration, #__TFS_ipaddresses TO #__jstats_ipaddresses, #__TFS_iptocountry TO #__jstats_iptocountry, #__TFS_keywords TO #__jstats_keywords, #__TFS_page_request TO #__jstats_page_request, #__TFS_page_request_c TO #__jstats_page_request_c, #__TFS_pages TO #__jstats_pages, #__TFS_referrer TO #__jstats_referrer, #__TFS_search_engines TO #__jstats_search_engines, #__TFS_systems TO #__jstats_systems, #__TFS_topleveldomains TO #__jstats_topleveldomains, #__TFS_visits TO #__jstats_visits";
	
		//below update could be applayed earlier than in version '2.2.0' (in version '2.2.0' it was applayed for sure)
		$query[] = "RENAME TABLE #__tfs_bots TO #__jstats_bots, #__tfs_browsers TO #__jstats_browsers, #__tfs_configuration TO #__jstats_configuration, #__tfs_ipaddresses TO #__jstats_ipaddresses, #__tfs_iptocountry TO #__jstats_iptocountry, #__tfs_keywords TO #__jstats_keywords, #__tfs_page_request TO #__jstats_page_request, #__tfs_page_request_c TO #__jstats_page_request_c, #__tfs_pages TO #__jstats_pages, #__tfs_referrer TO #__jstats_referrer, #__tfs_search_engines TO #__jstats_search_engines, #__tfs_systems TO #__jstats_systems, #__tfs_topleveldomains TO #__jstats_topleveldomains, #__tfs_visits TO #__jstats_visits";
	
		//below update could be applayed earlier than in version '2.2.0' (in version '2.2.0' it was applayed for sure)
		$query[] = "ALTER IGNORE TABLE #__jstats_pages ADD `page_title` VARCHAR( 255 )";

        $installer->appendSQLStep('2.2.0', $query);
        $query = array();
	}
		

	//in 2.3.0 we do not support update from version older than 2.2.3!!!     -do not remove below code, it help us to see full path of changes
	if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '2.2.0', '<') == true) {
		$query = array();
		
		// we added the primairy key description later, because then we could keep the old configuration (in the past the config was reset on every update).
		//below update could be applayed earlier than in version '2.2.0' (in version '2.2.0' it was applayed for sure)
		$query[] = "ALTER TABLE `#__jstats_configuration` ADD PRIMARY KEY (description)";
		
		// this index should realy speed up things...
		//below update could be applayed earlier than in version '2.2.0' (in version '2.2.0' it was applayed for sure) //index duplicated!
		$query[] = "CREATE INDEX visits_id ON `#__jstats_page_request` (`ip_id`)";
		
		//below update could be applayed earlier than in version '2.2.0' (in version '2.2.0' it was applayed for sure) //index duplicated!
		$query[] = "ALTER IGNORE TABLE `#__jstats_page_request` ADD INDEX `index_ip` (ip_id)";
		
		// added user awareness
		//below update could be applayed earlier than in version '2.2.0' (in version '2.2.0' it was applayed for sure)
		$query[] = "ALTER IGNORE TABLE `#__jstats_visits` ADD userid INT NOT NULL AFTER ip_id";

		// before release 2.1.9 additional userid indexes where created unwanted, remove them.
		//below update could be applayed earlier than in version '2.2.0' (in version '2.2.0' it was applayed for sure)
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_2`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_3`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_4`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_5`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_6`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_7`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_8`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_9`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_10`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_11`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_12`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_13`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_14`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_15`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_16`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_17`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_18`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_19`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_20`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_21`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_22`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_23`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_24`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_25`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_26`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_27`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_28`";
		$query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `userid_29`";
		$query[] = "ALTER IGNORE TABLE `#__jstats_visits` ADD INDEX `userid` (userid)";//in database schema it is missing so we remove it later (v2.5.0.301 - details see there)
		
        $installer->appendSQLStep('2.2.0', $query);
        $query = array();
	}

    $columns = $JSDatabaseAccess->js_getTableColumns("#__jstats_ipaddresses");
    $wereColumnsScreenAndWhoisCreated = $JSDatabaseAccess->js_hasTableColumn($columns, "screen"); //this is update install process optimization

	//@todo '2.3.0.130' is not checked (I can do this right now) - it should be checked in which version this was added
	if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '2.3.0.130 dev', '<') == true) {
		$query = array();
		
		// new since 2.3.0: we do not use anymore 'hourdiff' and 'language': delete them
		$query[] = 'DELETE FROM `#__jstats_configuration` WHERE `description` = \'hourdiff\'';
		$query[] = 'DELETE FROM `#__jstats_configuration` WHERE `description` = \'language\'';

		// new since 2.3.0: new field
		if ($wereColumnsScreenAndWhoisCreated == false) {
			//I know this code will be never performed in the future, but in the past it was - it should not be commented nor removed!
			$query[] = "ALTER TABLE `#__jstats_ipaddresses` ADD `whois` TINYINT( 1 ) NOT NULL";
			$query[] = 'ALTER TABLE `#__jstats_ipaddresses` ADD screen varchar(12) NOT NULL COMMENT \'screen resolution\'';
            $wereColumnsScreenAndWhoisCreated = true;
		} else {
			//$wereColumnsScreenAndWhoisCreated = false;
		}
        $installer->appendSQLStep('2.3.0.130 dev', $query);
        $query = array();
	}
	
	if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '2.3.0.167 dev', '<') == true) {
		$query = array();
		
		// new since 2.3.0: we do not use anymore 'purgetime' nor 'last_purge' : delete them
		$query[] = 'DELETE FROM `#__jstats_configuration` WHERE `description` = \'purgetime\'';
		$query[] = 'DELETE FROM `#__jstats_configuration` WHERE `description` = \'last_purge\'';
		$query[] = 'UPDATE IGNORE `#__jstats_configuration` SET `description` = \'show_summarized\' WHERE `description` = \'show_bu\'';
        $installer->appendSQLStep('2.3.0.167 dev', $query);

        $query = array();
		$query[] = "ALTER TABLE `#__jstats_ipaddresses` DROP INDEX `id`";
        $installer->appendSQLStep('2.3.0.167 dev', $query);

        $query = array();
		$query[] = "ALTER TABLE `#__jstats_pages` DROP INDEX `page_id`";
        $installer->appendSQLStep('2.3.0.167 dev', $query);

        $query = array();
		$query[] = "ALTER TABLE `#__jstats_page_request` DROP INDEX `visits_id`";
		$installer->appendSQLStep('2.3.0.167 dev', $query);
        $query = array();
	}
	
	if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '2.3.0.189 dev', '<') == true) {
		$query = array();
		
		// below query is harmless //I do not know if it is necessary (unable to check this) but I want to be SURE that all data are also in column time //duplicated columns (year, month, day, hour) will be deleted in near future!
		$query[] = "UPDATE `#__jstats_visits` SET `time` = CONCAT(`year`, '-', `month`, '-', `day`, ' ', `hour`, ':00:00') WHERE `time` = '0000-00-00 00:00:00'";
		
		// transfer what we have
        $installer->appendSQLStep('2.3.0.189 dev', $query);
        $query = array();
	}
	
	if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '2.3.0.201 dev', '<') == true) {
		$query = array();
		
		//sys_img is not used - change it to sys_type. Add column image
		$query[] = "ALTER TABLE `#__jstats_systems` CHANGE COLUMN `sys_img` `sys_type` tinyint(1) NOT NULL default '0'";
		$query[] = 'ALTER TABLE `#__jstats_systems` ADD `sys_img` varchar(12) NOT NULL default \'noimage\'';
		
		//browser_img is not used - change it to browser_type. Add column image
		$query[] = "ALTER TABLE `#__jstats_browsers` CHANGE COLUMN `browser_img` `browser_type` tinyint(1) NOT NULL default '0'";
		$query[] = 'ALTER TABLE `#__jstats_browsers` ADD `browser_img` varchar(12) NOT NULL default \'noimage\'';
		
		// transfer what we have
		$installer->appendSQLStep('2.3.0.201 dev', $query);
        $query = array();
	}
	
	if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '2.3.0.216 dev', '<') == true) {
		$query = array();

		//rename show_summarized to include_summarized
		$query[] = 'UPDATE IGNORE `#__jstats_configuration` SET `description` = \'include_summarized\' WHERE `description` = \'show_summarized\'';

		//insert new parameter show_summarized. We must set it to 'false' (we do not know if include_summarized is set to true or to false)
		$query[] = "INSERT IGNORE INTO #__jstats_configuration (description, value) VALUES ".
				  "('show_summarized', 'false') ";
		
		// transfer what we have
        $installer->appendSQLStep('2.3.0.216 dev', $query);
        $query = array();
	}

	if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '2.3.0.231 dev', '<') == true) {
		$query = array();

        $query[] = 'ALTER IGNORE TABLE `#__jstats_browsers` CHANGE `browser_id` `browser_id` mediumint NOT NULL';

        /**
         * All entries are regular browsers
         */
        $query[] = "UPDATE `#__jstats_browsers` SET `browser_type` = 1;";
        $installer->appendSQLStep('2.3.0.231 dev', $query);
        $query = array();

        /**
         * todo, that's bad as we do not have now anymore data 
         *
         */
        /*
		$query[] = 'DROP TABLE `#__jstats_browsers`';

		//remove auto_increment option
		$query[] = 'CREATE TABLE IF NOT EXISTS `#__jstats_browsers` ('
		  		. ' `browser_id` mediumint NOT NULL,'
		  		. ' `browser_string` varchar(50) NOT NULL default \'\','
		  		. ' `browser_fullname` varchar(50) NOT NULL default \'\','
		        . ' `browser_type` tinyint NOT NULL default \'0\','
		        . ' `browser_img` varchar(12) NOT NULL default \'noimage\','
		  		. ' PRIMARY KEY  (`browser_id`),'
		  		. ' UNIQUE KEY `browser_string` (`browser_string`)'
		  		. ' ) TYPE=MyISAM';
        */

		/*
        $query[] = 'DROP TABLE `#__jstats_systems`';

		//remove auto_increment option
		$query[] = 'CREATE TABLE IF NOT EXISTS `#__jstats_systems` ('
		        . ' `sys_id` mediumint(9) NOT NULL,'
		        . ' `sys_string` varchar(25) NOT NULL default \'\','
		        . ' `sys_fullname` varchar(25) NOT NULL default \'\','
		        . ' `sys_type` tinyint(1) NOT NULL default \'0\','
		        . ' `sys_img` varchar(12) NOT NULL default \'noimage\','
		        . ' PRIMARY KEY (`sys_id`)'
		        . ' ) TYPE=MyISAM';
        */

        $query[] = "ALTER TABLE `#__jstats_systems` CHANGE `sys_id` `sys_id` mediumint NOT NULL;";

        $installer->appendSQLStep('2.3.0.231 dev', $query);
        $query = array();

        /*
		$query[] = 'DROP TABLE `#__jstats_topleveldomains`';

		//extend size od tld column, remove auto_increment option
		$query[] = 'CREATE TABLE IF NOT EXISTS `#__jstats_topleveldomains` ('
		        . ' `tld_id` mediumint(9) NOT NULL,'
		        . ' `tld` varchar(9) NOT NULL default \'\','
		        . ' `fullname` varchar(255) NOT NULL default \'\','
		        . ' PRIMARY KEY (`tld_id`),'
		        . ' KEY `tld` (`tld`)'
		        . ' ) TYPE=MyISAM';
        */

        $query[] = "ALTER TABLE `#__jstats_topleveldomains` CHANGE `tld_id` `tld_id` mediumint(9) NOT NULL;";
        $query[] = "ALTER TABLE `#__jstats_topleveldomains` CHANGE `tld` `tld` varchar(9) NOT NULL default '';";
        $query[] = "ALTER TABLE `#__jstats_topleveldomains` CHANGE `fullname` `fullname` varchar(255) NOT NULL default '';";

		// transfer what we have
        $installer->appendSQLStep('2.3.0.231 dev', $query);
        $query = array();
	}

	if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '2.3.0.232 dev', '<') == true) {
        $query = array();
		if ($wereColumnsScreenAndWhoisCreated == true) {
			//see task "Remove 'screen' and 'whois' column" for details
			$query[] = 'ALTER IGNORE TABLE `#__jstats_ipaddresses` DROP COLUMN `screen`';
			$query[] = 'ALTER IGNORE TABLE `#__jstats_ipaddresses` DROP COLUMN `whois`';
		}
        $installer->appendSQLStep('2.3.0.232 dev', $query);
        $query = array();
	}

	// #__jstats_browsers, #__jstats_bots
	if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '2.5.0.316 dev', '<') == true) {


        $query = array();
        $query[] = "ALTER TABLE `#__jstats_browsers` CHANGE `browser_type` `browsertype_id` TINYINT UNSIGNED NOT NULL;";
        $query[] = "ALTER TABLE `#__jstats_browsers` CHANGE `browser_string` `browser_key` varchar(50) NOT NULL;";
        $query[] = "ALTER TABLE `#__jstats_browsers` CHANGE `browser_fullname` `browser_name` varchar(50) NOT NULL;";
        $query[] = "ALTER TABLE `#__jstats_browsers` CHANGE `browser_id` `browser_id` SMALLINT UNSIGNED NOT NULL;";

        $installer->appendSQLStep('2.5.0.316 dev', $query, 'Structure updated within browser table');

        $query = array();
        $query[] = "DELETE FROM #__jstats_browsers USING #__jstats_browsers INNER JOIN #__jstats_bots WHERE #__jstats_browsers.browser_key = #__jstats_bots.bot_string;";
        $query[] = "INSERT INTO #__jstats_browsers (`browser_id`,`browser_key`,`browser_name`,`browsertype_id`) SELECT b.bot_id + 1024, b.bot_string, b.bot_fullname, 2 FROM `#__jstats_bots` AS b;";
        $installer->appendSQLStep('2.5.0.316 dev', $query, 'Move Bots to browser table');

        $query = array();
        $query[] = 'DROP TABLE `#__jstats_bots`';
        $installer->appendSQLStep('2.5.0.316 dev', $query, 'Bot table is not longer required');


        /*
		$query = array();
		$query[] = 'DROP TABLE `#__jstats_browsers`';

		$query[] = 'CREATE TABLE IF NOT EXISTS `#__jstats_browsers` ('
		        . ' `browser_id` SMALLINT UNSIGNED NOT NULL,'
		        . ' `browsertype_id` TINYINT UNSIGNED NOT NULL,'
		        . ' `browser_key` varchar(50) NOT NULL,'
		        . ' `browser_name` varchar(50) NOT NULL,'
		        . ' `browser_img` varchar(12) NOT NULL default \'noimage\','
		        . ' PRIMARY KEY (`browser_id`)'
		        . ' ) TYPE=MyISAM';

		// transfer what we have
        $installer->appendSQLStep('2.5.0.316 dev', $query);
        */
        $query = array();
	}

	// #__jstats_page_request -> #__jstats_impressions
	if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '2.5.0.316 dev', '<') == true) {

		$query = array();
        $columns = $JSDatabaseAccess->js_getTableColumns("#__jstats_page_request");
        if(!$JSDatabaseAccess->js_hasTableColumn($columns, "timestamp") )
        {
            $query[] = "ALTER TABLE `#__jstats_page_request` ADD `timestamp` INT(10) UNSIGNED NOT NULL DEFAULT 0;";
        }

        $installer->appendSQLStep('2.5.0.316 dev', $query, 'Add Timestamp to page requests table');

        $timzone_offset = js_getJSTimeZone() * 3600;
        $query[] = "UPDATE `#__jstats_page_request` as pr SET pr.timestamp = ( UNIX_TIMESTAMP(CONCAT(pr.year, '-', pr.month, '-', pr.day, ' ', pr.hour, ':00:00')) - (0) );";
        $installer->appendSQLStep('2.5.0.316 dev', $query, 'Fill new changed_at column with the converted values');
        $query =  array();

		//drop relevant columns, prepare more space in DB
		$query[] = 'ALTER TABLE `#__jstats_page_request` DROP COLUMN `hour`';
        $installer->appendSQLStep('2.5.0.316 dev', $query);
		$query = array();
		$query[] = 'ALTER TABLE `#__jstats_page_request` DROP COLUMN `day`';
        $installer->appendSQLStep('2.5.0.316 dev', $query);
		$query = array();
		$query[] = 'ALTER TABLE `#__jstats_page_request` DROP COLUMN `month`';
        $installer->appendSQLStep('2.5.0.316 dev', $query);
		$query = array();
		$query[] = 'ALTER TABLE `#__jstats_page_request` DROP COLUMN `year`';
        $installer->appendSQLStep('2.5.0.316 dev', $query);
		$query = array();
		$query[] = 'OPTIMIZE TABLE `#__jstats_page_request`';

		// transfer what we have
        $installer->appendSQLStep('2.5.0.316 dev', $query);
		$query = array();

		//create new table, transfer data, delete old table  (droping table is better than transforming it. If some extra indexes or columns exists we remove them)
		$query[] = 'CREATE TABLE IF NOT EXISTS `#__jstats_impressions` ('
		        . ' `page_id` MEDIUMINT UNSIGNED NOT NULL,'
		        . ' `visit_id` MEDIUMINT UNSIGNED NOT NULL,'
		        //. ' `impression_length` SMALLINT UNSIGNED NOT NULL COMMENT \'How long page was viewed. In seconds\',' //curently not implemented
		        //. ' KEY `page_id` (`page_id`),'
		        //. ' KEY `visit_id` (`visit_id`)'
                . ' `timestamp` INT(10) UNSIGNED NOT NULL DEFAULT 0'
		        . ' ) TYPE=MyISAM';
        $installer->appendSQLStep('2.5.0.316 dev', $query);
		$query = array();

		$query[] = 'INSERT INTO `#__jstats_impressions` (`page_id`,`visit_id`, `timestamp`)'
  				. ' SELECT `page_id`, `ip_id`, `timestamp`'
  				. ' FROM `#__jstats_page_request`';
        $installer->appendSQLStep('2.5.0.316 dev', $query);
		$query = array();

		$query[] = 'DROP TABLE `#__jstats_page_request`';
        $installer->appendSQLStep('2.5.0.316 dev', $query);
        $query = array();
	}

	// #__jstats_visits
	if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '2.5.0.316 dev', '<') == true) {

		$query = array();
		$query[] = 'ALTER TABLE `#__jstats_visits` DROP COLUMN `hour`';
        $installer->appendSQLStep('2.5.0.316 dev', $query);
		$query = array();

		$query[] = 'ALTER TABLE `#__jstats_visits` DROP COLUMN `day`';
        $installer->appendSQLStep('2.5.0.316 dev', $query);
		$query = array();

		$query[] = 'ALTER TABLE `#__jstats_visits` DROP COLUMN `month`';
        $installer->appendSQLStep('2.5.0.316 dev', $query);
		$query = array();

		$query[] = 'ALTER TABLE `#__jstats_visits` DROP COLUMN `year`';
        $installer->appendSQLStep('2.5.0.316 dev', $query);
		$query = array();

		$query[] = 'DELETE FROM `#__jstats_visits` WHERE `time` = \'0000-00-00 00:00:00\'';
		$query[] = 'OPTIMIZE TABLE `#__jstats_visits`';
        $installer->appendSQLStep('2.5.0.316 dev', $query);
		$query = array();

		$query[] = 'RENAME TABLE `#__jstats_visits` TO `#__jstats_visits_old`';
        $installer->appendSQLStep('2.5.0.316 dev', $query);
		$query = array();

		$query[] = 'CREATE TABLE IF NOT EXISTS #__jstats_visits ('
				. ' `visit_id` MEDIUMINT UNSIGNED NOT NULL auto_increment,'
				. ' `visitor_id` MEDIUMINT UNSIGNED NOT NULL,'
				. ' `joomla_userid` MEDIUMINT NOT NULL COMMENT \'Joomla CMS UserId\','
				. ' `visit_date` DATE NOT NULL COMMENT \'visit date in Joomla Local time zone\', '
				. ' `visit_time` TIME NOT NULL COMMENT \'visit time in Joomla Local time zone\', '
				. ' PRIMARY KEY (visit_id),'
				. ' KEY `visit_date` (`visit_date`),'
				. ' KEY `visitor_id` (`visitor_id`)'
				. ' ) TYPE=MyISAM';
        $installer->appendSQLStep('2.5.0.316 dev', $query);
		$query = array();

		$query[] = 'INSERT INTO `#__jstats_visits` (`visit_id`,`visitor_id`,`joomla_userid`,`visit_date`,`visit_time`)'
  				. ' SELECT `id`, `ip_id`, `userid`, CONCAT(YEAR(`time`), \'-\', MONTH(`time`), \'-\', DAYOFMONTH(`time`)), CONCAT(HOUR(`time`), \':\', MINUTE(`time`), \':\', SECOND(`time`))'
  				. ' FROM `#__jstats_visits_old`';
        $installer->appendSQLStep('2.5.0.316 dev', $query);
		$query = array();

		$query[] = 'DROP TABLE `#__jstats_visits_old`';
        $installer->appendSQLStep('2.5.0.316 dev', $query);
		$query = array();
	}


	if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '3.0.0.372 dev', '<') == true) {
		$query = array();
		
		$query[] = 'ALTER TABLE `#__jstats_keywords` ADD `visit_id` MEDIUMINT UNSIGNED NOT NULL';
		$query[] = 'ALTER TABLE `#__jstats_referrer` ADD `visit_id` MEDIUMINT UNSIGNED NOT NULL';
		
		// transfer what we have
        $installer->appendSQLStep('3.0.0.372 dev', $query);
        $query = array();
	}

	if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '3.0.0.382 dev', '<') == true) {

        $query = array();
        $query[] = "ALTER TABLE `#__jstats_search_engines` CHANGE `searchid` `searcher_id` MEDIUMINT UNSIGNED NOT NULL;";
        $query[] = "ALTER TABLE `#__jstats_search_engines` CHANGE `description` `searcher_name` varchar(100) NOT NULL;";
        $query[] = "ALTER TABLE `#__jstats_search_engines` CHANGE `search` `searcher_domain` varchar(100) NOT NULL;";
        $query[] = "ALTER TABLE `#__jstats_search_engines` CHANGE `searchvar` `searcher_key` varchar(50) NOT NULL;";
        $query[] = "RENAME TABLE #__jstats_search_engines TO #__jstats_searchers;";
        $installer->appendSQLStep('3.0.0.382 dev', $query, 'Refactoring search_engines table');

        $query = array();
		/*
		$query[] = 'DROP TABLE `#__jstats_search_engines`';

        $installer->appendSQLStep('3.0.0.382 dev', $query);
        $query = array();

		$query[] = 'CREATE TABLE IF NOT EXISTS `#__jstats_searchers` ('
		        . ' `searcher_id` MEDIUMINT UNSIGNED NOT NULL,'//not auto_increment!
		        . ' `searcher_name` varchar(100) NOT NULL,'
		        . ' `searcher_domain` varchar(100) NOT NULL,'
		        . ' `searcher_key` varchar(50) NOT NULL,'
		        . ' PRIMARY KEY (`searcher_id`)'
		        . ' ) TYPE=MyISAM';

        $installer->appendSQLStep('3.0.0.382 dev', $query);
        $query = array();
        */
		$query[] = "ALTER TABLE `#__jstats_keywords` CHANGE COLUMN `searchid` `searcher_id` mediumint(9) NOT NULL default '0'";

        $installer->appendSQLStep('3.0.0.382 dev', $query);
        $query = array();

		$query[] = 'UPDATE `#__jstats_keywords` SET `searcher_id` = 90 WHERE `searcher_id` = 2';
		$query[] = 'UPDATE `#__jstats_keywords` SET `searcher_id` = 91 WHERE `searcher_id` = 3';
		$query[] = 'UPDATE `#__jstats_keywords` SET `searcher_id` = 92 WHERE `searcher_id` = 5';
		$query[] = 'UPDATE `#__jstats_keywords` SET `searcher_id` = 5  WHERE `searcher_id` = 4';
		$query[] = 'UPDATE `#__jstats_keywords` SET `searcher_id` = 3  WHERE `searcher_id` = 1';
        $installer->appendSQLStep('3.0.0.382 dev', $query);
        $query = array();
	}

	if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '3.0.0.393 dev', '<') == true) {
		$query = array();

		//there was bug in version (about) 3.0.0.372 dev - now we remove duplicated rows (the same row is duplicated in two tables). This query fix this problem in 100% and leave db unharmed //only harm is addiotional rows in #__jstats_referrer table
		/**
         * This needs to be reconsidered. We like to see the whole URL to be able to see the page
         */
        //$query[] = 'DELETE IGNORE FROM `#__jstats_referrer` WHERE `visit_id`>0 AND EXISTS (SELECT * FROM `#__jstats_keywords` WHERE #__jstats_keywords.visit_id = #__jstats_referrer.visit_id)';


        $query = array();
	}

	if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '3.0.1.488 dev', '<') == true) {

		$query = array();
		//there are some more entries, but it is hard to define which are wrong. See "[#18970] Keywords are incorrectly recognized" and "[#18344] *** glibc detected *** double free or corruption (fasttop) with AOL search results links"
		$query[] = 'DELETE IGNORE FROM `#__jstats_keywords` WHERE CHAR_LENGTH(`keywords`)<=1';
        $installer->appendSQLStep('3.0.1.488 dev', $query);

        $query = array();
		$query[] = 'UPDATE IGNORE `#__jstats_keywords` SET `keywords` = TRIM(SUBSTRING(`keywords`, 2, CHAR_LENGTH(`keywords`)-2)) WHERE LEFT(`keywords`, 1)=\'\\\'\' AND RIGHT(`keywords`, 1)=\'\\\'\'';
		//$query[] = 'DELETE IGNORE FROM `#__jstats_keywords` WHERE CHAR_LENGTH(`keywords`)<=1'; it will be performed in '3.0.1.495 dev' section
        $installer->appendSQLStep('3.0.1.488 dev', $query);

        $query = array();
		$query[] = 'DELETE IGNORE FROM `#__jstats_keywords` WHERE LEFT(LTRIM(`keywords`),1)=\'&\'';
		//$query[] = 'DELETE IGNORE FROM `#__jstats_keywords` WHERE `keywords` LIKE \'%site:%\''; NO we should not remove those entries
		//$query[] = 'DELETE IGNORE FROM `#__jstats_keywords` WHERE `keywords` LIKE \'%http://%\''; NO we should not remove those entries

		// transfer what we have
        $installer->appendSQLStep('3.0.1.488 dev', $query);
        $query = array();
	}

	if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '3.0.1.495 dev', '<') == true) {
		$query = array();

		//there are some more entries, but it is hard to define which are wrong. See "[#18970] Keywords are incorrectly recognized" and "[#18344] *** glibc detected *** double free or corruption (fasttop) with AOL search results links"
		$query[] = 'DELETE IGNORE FROM `#__jstats_keywords` WHERE CHAR_LENGTH(`keywords`)<=2';
		$query[] = 'DELETE IGNORE FROM `#__jstats_keywords` WHERE CHAR_LENGTH(`keywords`)<=3 AND LEFT(`keywords`, 1)=\'h\'';

		// transfer what we have
        $installer->appendSQLStep('3.0.1.495 dev', $query);
        $query = array();
	}

    /**
     * Everything before is related to the the JoomlaStats upgrades.
     *                              -
     * Everything below is required for the conversion of Joomastats to j4age
     */


    /**
     * Updates for the browsers table based on the new prosed DB structure for 3.1.x
     * @author Andreas Halbig
     */
   if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '4.0.0.521 dev', '<') == true) {
        $query = array();


        /**
         *  Handling for the Browser & System structural update
         */

        $columns = $JSDatabaseAccess->js_getTableColumns("#__jstats_browsers");
        if(!$JSDatabaseAccess->js_hasTableColumn($columns, "ordering") )
        {
            $query[] = "ALTER TABLE `#__jstats_browsers` ADD `ordering` SMALLINT UNSIGNED NOT NULL DEFAULT 0;";
        }

        if(!$JSDatabaseAccess->js_hasTableColumn($columns, "type") )
        {
           $query[] = "ALTER TABLE `#__jstats_browsers` ADD `type` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Browser Types classifies entries \n- 0 = Unknown\n- 1 = Browser\n- 2 = Bot';";
            //This needs to be there, because the DB filled with the "type" data after the update script is done.
        }

        if(!$JSDatabaseAccess->js_hasTableColumn($columns, "location") )
        {
            $query[] = "ALTER TABLE `#__jstats_browsers` ADD `location` TINYINT UNSIGNED NOT NULL DEFAULT 6 COMMENT 'Used to restrict the location!\n\nWe have 8 Bits (= 254)\nValue 1 = Search whole UserAgent Str\nValue 2 = product name\nValue 4 = comment\nValue 8 = version\n=>\nValue \"6\" means, search product name & comment\n';";
        }

        $installer->appendSQLStep('4.0.0.521 dev', $query, 'Refactoring Browser Table'); // #1
        $query = array();

        $query[] = "UPDATE `#__jstats_browsers` SET `ordering` = browser_id WHERE 1 = 1;";
        $query[] = "UPDATE `#__jstats_browsers` SET `type` = 1 WHERE browser_id < 1024;";
        $query[] = "UPDATE `#__jstats_browsers` SET `type` = 2 WHERE browser_id < 65535 AND browser_id > 1023;";
        $installer->appendSQLStep('4.0.0.521 dev', $query, 'Fill data to new columns in Browser Table'); // #2
        $query = array();

        $columns = $JSDatabaseAccess->js_getTableColumns("#__jstats_systems");
        if(!$JSDatabaseAccess->js_hasTableColumn($columns, "ordering") )
        {
            $query[] = "ALTER TABLE `#__jstats_systems` ADD `ordering` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0;";
        }
        if($JSDatabaseAccess->js_hasTableColumn($columns, "sys_id") )
        {
            $query[] = "ALTER TABLE `#__jstats_systems` CHANGE COLUMN `sys_id` `os_id` MEDIUMINT UNSIGNED NOT NULL;";
        }
        if($JSDatabaseAccess->js_hasTableColumn($columns, "sys_string") )
        {
            $query[] = "ALTER TABLE `#__jstats_systems` CHANGE COLUMN `sys_string` `os_key` VARCHAR(25) NOT NULL DEFAULT '';";
        }
        if($JSDatabaseAccess->js_hasTableColumn($columns, "sys_fullname") )
        {
            $query[] = "ALTER TABLE `#__jstats_systems` CHANGE COLUMN `sys_fullname` `os_name` VARCHAR(50) NOT NULL DEFAULT '';";
        }
        if($JSDatabaseAccess->js_hasTableColumn($columns, "sys_type") )
        {
            $query[] = "ALTER TABLE `#__jstats_systems` CHANGE COLUMN `sys_type` `os_type` TINYINT NOT NULL DEFAULT '0';";
        }
        if($JSDatabaseAccess->js_hasTableColumn($columns, "sys_img") )
        {
            $query[] = "ALTER TABLE `#__jstats_systems` CHANGE COLUMN `sys_img` `os_img` varchar(12) NOT NULL default 'noimage';";
        }

        $installer->appendSQLStep('4.0.0.521 dev', $query, 'Refactoring Systems Table'); // #3
        $query = array();

        /**
         *  Handling for the #__jstats_impressions enhancements
         */
        $columns = $JSDatabaseAccess->js_getTableColumns("#__jstats_impressions");
        if(!$JSDatabaseAccess->js_hasTableColumn($columns, "timestamp") )
        {
            $query[] = "ALTER TABLE `#__jstats_impressions` ADD `timestamp` INT(10) UNSIGNED NOT NULL DEFAULT 0;";
        }

        $installer->appendSQLStep('4.0.0.521 dev', $query, 'Add Timestamp to Impression'); // #4
        $query = array();

        /*
        else if( !js_isColumnTypeOf($columns, "timestamp", "timestamp"))
        {
            $query[] = "ALTER TABLE `#__jstats_impressions` ADD `timestamp_new` INT(10) UNSIGNED NOT NULL DEFAULT 0;";
            $query[] = "UPDATE `#__jstats_impressions` SET `timestamp_new` = UNIX_TIMESTAMP( `timestamp` ) WHERE 1 = 1;";
            $query[] = "ALTER TABLE `#__jstats_impressions` DROP `timestamp`;";
            $query[] = "ALTER TABLE `#__jstats_impressions` CHANGE COLUMN `timestamp_new` `timestamp` INT(10) UNSIGNED NOT NULL DEFAULT 0;";

            $query[] = "ALTER TABLE `#__jstats_unique_visitors` CHANGE COLUMN `visitor_id` `visitor_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT;";
            $query[] = "ALTER TABLE `#__jstats_unique_visitors` ADD `note` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Sometimes you know which user is behind a IP. This field enables to assign a note.';";
            $query[] = "ALTER TABLE `#__jstats_unique_visitors` DROP `joomla_userid`;";
            $query[] = "ALTER TABLE `#__jstats_unique_visitors` CHANGE COLUMN `visitor_exclude` `visitor_exclude` TINYINT(1) NOT NULL DEFAULT '0';";

            $query[] = "RENAME TABLE #__jstats_unique_visitors TO #__jstats_visitors;";
        }
        */

        $columns = $JSDatabaseAccess->js_getTableColumns("#__jstats_ipaddresses");
        //if(!js_isColumnTypeOf($columns, "ip", "int") )
        {

            $query[] = "
                CREATE TABLE IF NOT EXISTS `#__jstats_client_to_ip` (
                  `ip` INT(11) UNSIGNED NOT NULL ,
                  `client_id` MEDIUMINT UNSIGNED NOT NULL ,
                  PRIMARY KEY (`ip`, `client_id`) )
                ENGINE = MyISAM;
            ";

            $query[] = "
                 CREATE TABLE IF NOT EXISTS `#__jstats_clients` (
                    `ip` INT(11) UNSIGNED NOT NULL,
                    `client_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `os_id` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
                    `visitor_id` MEDIUMINT UNSIGNED NULL DEFAULT NULL,
                    `browser_id` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT 0,
                    `browser_version` VARCHAR( 25 ) NULL DEFAULT NULL ,
                    `useragent` VARCHAR( 255 ) NOT NULL DEFAULT '',
                    `type` TINYINT UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Values from 0 - 255',
                    `client_exclude` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0',
                    PRIMARY KEY ( `client_id` )
                ) ENGINE = MYISAM
            ";


            $query[] = "
                CREATE  TABLE IF NOT EXISTS `#__jstats_visitors` (
                  `visitor_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
                  `note` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Sometimes you know which user is behind a IP. This field enables to assign a note. ',
                  `visitor_exclude` TINYINT(1) NOT NULL DEFAULT '0' ,
                  PRIMARY KEY (`visitor_id`) )
                ENGINE = MyISAM;
            ";

            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Introduce new Client/Visitor/IP tables'); // #5
            $query = array();

            //$query[] = "ALTER IGNORE TABLE `#__jstats_ipaddresses` ADD `city` VARCHAR(15) NULL;";
            //$query[] = "";
            //$query[] = "ALTER IGNORE TABLE `#__jstats_ipaddresses` CHANGE COLUMN `exclude` `ip_exclude` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ;";

            // ALTER IGNORE TABLE `#__jstats_topleveldomains` CHANGE COLUMN `fullname` `tld_name` varchar(255) NOT NULL default '';


            // SET AUTOCOMMIT=0;
            // START TRANSACTION;

            // TLD is replaced with a foraign key to topleveldomains

            $query[] = "ALTER TABLE `#__jstats_ipaddresses` ADD `tld_id` MEDIUMINT UNSIGNED NULL DEFAULT NULL;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Append new column tld_id to ipaddresses'); // #6
            $query = array();

            $topLevelcolumns = $JSDatabaseAccess->js_getTableColumns("#__jstats_topleveldomains");
            if(!empty($topLevelcolumns))
            {
              $query[] = "UPDATE `#__jstats_ipaddresses` as a, `#__jstats_topleveldomains` as t SET a.tld_id = t.tld_id WHERE a.tld = t.tld;";
            }
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Assign values to column tld_id of ipaddresses'); // #7
            $query = array();


            // IP is stored as int and not longer as string
            $query[] = "ALTER TABLE `#__jstats_ipaddresses` ADD `ip_temp` INT(11) UNSIGNED NOT NULL DEFAULT '0';";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Append new column ip_temp to ipaddresses'); // #8
            $query = array();

            $query[] = "UPDATE `#__jstats_ipaddresses` SET `ip_temp` = INET_ATON( `ip` ) WHERE 1 = 1;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Assign values to ip_temp column of ipaddresses'); // #9
            $query = array();

            $query[] = "ALTER TABLE `#__jstats_ipaddresses` DROP `ip`;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Drop old column ip from ipaddresses'); // #9
            $query = array();

            $query[] = "ALTER TABLE `#__jstats_ipaddresses` CHANGE COLUMN `ip_temp` `ip` INT(11) UNSIGNED NOT NULL DEFAULT '0';";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Rename ip_temp to ip column');  // #10
            $query = array();

            // ALTER IGNORE TABLE `#__jstats_ipaddresses` ADD `city` VARCHAR(15) NULL;

            // TINYINT(1) is not enough space for us as we have a flag for unknown, browser and bot
            $query[] = "ALTER TABLE `#__jstats_ipaddresses` CHANGE COLUMN `type` `type` TINYINT UNSIGNED NOT NULL DEFAULT '0';";

            // exclude needs to be renamed as we now using 3 different kind of exclude (ip, client, visitor)
            $query[] = "ALTER TABLE `#__jstats_ipaddresses` CHANGE COLUMN `exclude` `ip_exclude` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'; ";

            $installer->appendSQLStep('4.0.0.521 dev', $query, 'IP-Addresss table enhancement and refactoring'); // #11
            $query = array();

            $query[] = "ALTER TABLE `#__jstats_ipaddresses` ADD `browser_id` SMALLINT( 5 ) UNSIGNED NULL DEFAULT NULL;";
            $query[] = "ALTER TABLE `#__jstats_ipaddresses` ADD `browser_version` VARCHAR( 25 ) NULL DEFAULT NULL;";

            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Append helper columns browser_id and browser_version to ipaddresses'); // #12

            /**
             * Without index it would take us hours to handle big DBs here
             */
            $query =  array();
            $query[] = "ALTER TABLE `#__jstats_ipaddresses` ADD INDEX (`browser`);";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Add temporary index to column __jstats_browsers'); // #13

            $query =  array();
            $query[] = "ALTER TABLE `#__jstats_ipaddresses` ADD INDEX (`browser_id`);";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Add temporary index to column __jstats_ipaddresses'); // #14

            /**
             * This statement is still terrible slow, if anyone knows a better solution I am happy 
             */
            $query = array();
            $query[] = "UPDATE `#__jstats_ipaddresses` AS a, `#__jstats_browsers` AS br
                        SET a.browser_id = br.browser_id, a.type = br.type, a.browser_version = TRIM(SUBSTR(a.browser, LENGTH(br.Browser_name) + 1 ))
                        WHERE a.browser_id IS NULL AND ( a.browser LIKE CONCAT( br.browser_name, '%') AND br.browser_id > 0 );
            ";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Assign foreign-key between ipaddresses and browser table');// #15

            $query =  array();
            $query[] = "ALTER TABLE `#__jstats_ipaddresses` DROP INDEX `browser`;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Add temporary index to column __jstats_browsers'); // #16

            $query =  array();
            $query[] = "ALTER TABLE `#__jstats_ipaddresses` DROP INDEX `browser_id`;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Add temporary index to column __jstats_ipaddresses'); // #17

            $query = array();

            $query[] = "ALTER TABLE `#__jstats_ipaddresses` DROP `browser`;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Drop not longer required column browser from IP-Addresses');// #18
            $query = array();

            $query[] = "ALTER TABLE `#__jstats_ipaddresses` ADD `os_id` MEDIUMINT UNSIGNED NULL DEFAULT NULL;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Append helper column os_id to ipaddresses'); // #19
            $query = array();

            /**
             * Without index it would take us hours to handle big DBs here
             */
            $query =  array();
            $query[] = "ALTER TABLE `#__jstats_systems` ADD INDEX (`os_name`);";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Add temporary index to column __jstats_systems'); // #20

            $query = array();
            $query[] = "UPDATE `#__jstats_ipaddresses` AS a, `#__jstats_systems` AS sy
                SET a.os_id = sy.os_id
                WHERE a.os_id IS NULL AND sy.os_name = a.system;
            ";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Assign foreign-key between ipaddresses and systems table');// #21

            $query = array();
            $query[] = "ALTER TABLE `#__jstats_systems` DROP INDEX `os_name`;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Drop temporary index from column __jstats_systems');// #22

            $query =  array();
            $query[] = "ALTER TABLE `#__jstats_ipaddresses` DROP `system`;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Drop not longer required column system from IP-Addresses'); // #23
            $query = array();

            /**
             * IP address ID is temporary filled as helper into visitor_id, but it gets cleared later as it is used for something else
             */
            $query[] = "INSERT INTO #__jstats_clients (`ip`,`useragent`,`browser_id`,`os_id`,`type`,`browser_version`, `visitor_id`) SELECT a.ip, a.useragent, a.browser_id, a.os_id, a.type, a.browser_version, a.id FROM `#__jstats_ipaddresses` AS a GROUP BY a.ip, a.useragent;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Move Data from IP-Address to Clients table'); // #24
            $query = array();

            $query[] = "ALTER TABLE `#__jstats_ipaddresses` DROP `browser_id`;";
            $query[] = "ALTER TABLE `#__jstats_ipaddresses` DROP `browser_version`;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Drop not longer required helper column browser_id and browser_version from IP-Addresses'); // #25
            $query = array();

            $query[] = "ALTER TABLE `#__jstats_ipaddresses` DROP `os_id`;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Drop not longer required helper column os_id from IP-Addresses');// #26
            $query = array();

            $query[] = "INSERT INTO #__jstats_client_to_ip (`ip`,`client_id`) SELECT DISTINCT ip, client_id FROM `#__jstats_clients` AS c;";

            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Move Data from IP-Address to Clients_to_ip table');// #27
            $query = array();

            // visitor_id is now not longer correct, so we introduce the client_id
            $query[] = "ALTER TABLE `#__jstats_visits` ADD `client_id` MEDIUMINT UNSIGNED NULL;";

            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Add column client_id to visits table');// #28
            $query = array();

            /*
                This enquiry is the faster solution, but it could leave some entries untouched so that the client_id is NULL
            */
            $query =  array();
            $query[] = "UPDATE `#__jstats_visits` AS v, `#__jstats_clients` AS c
                SET v.client_id = c.client_id
                WHERE v.client_id IS NULL AND v.visitor_id = c.visitor_id;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Link visits table to new client/ip structure');// #28


            /**
             * Without index it would take us hours to handle big DBs here
             */
            $query =  array();
            $query[] = "ALTER TABLE `#__jstats_ipaddresses` ADD INDEX (`useragent`);";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Add temporary index to column __jstats_ipaddresses');// #29
            $query =  array();
            $query[] = "ALTER TABLE `#__jstats_clients` ADD INDEX (`useragent`);";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Add temporary index to column __jstats_clients');// #30
            $query =  array();
            $query[] = "ALTER TABLE `#__jstats_visits` ADD INDEX (`client_id`);";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Add temporary index to column __jstats_visits'); // #31

            /**
             * This enquiry is better, as it is able to fix issues with old data and assign multiple ipaddress entries to one single client
             * We just fix the values, which are still NULL
             */
            $query =  array();
            $query[] = "UPDATE `#__jstats_visits` AS v, `#__jstats_clients` AS c, `#__jstats_ipaddresses` AS a
                SET v.client_id = c.client_id
                WHERE v.client_id IS NULL AND (v.visitor_id = a.id AND c.ip = a.ip AND c.useragent = a.useragent);";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Link visits table to new client/ip structure');// #32

            $query = array();
            $query[] = "ALTER TABLE `#__jstats_visits` DROP INDEX `client_id`;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Add temporary index from column __jstats_visits');// #33
            $query = array();
            $query[] = "ALTER TABLE `#__jstats_ipaddresses` DROP INDEX `useragent`;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Add temporary index from column __jstats_ipaddresses');// #34
            $query =  array();
            $query[] = "ALTER TABLE `#__jstats_clients` DROP INDEX `useragent`;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Add temporary index from column __jstats_clients');// #35

            // The IP in clients was just a helper column, which is now not longer required
            $query = array();
            $query[] = "ALTER TABLE `#__jstats_clients` DROP `ip`;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Drop helper column ip from clients table');// #36

            /**
             * the visitor_id was just temporary filled with IP-address ID. Now we clear it up, as we need this column
             * for the foreign key to jstats_visitors
             */
            $query = array();
            $query[] = "UPDATE `#__jstats_clients` AS c SET c.visitor_id = NULL;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Drop not longer required column tld from IP-Addresses');// #37
            $query = array();

            // We get rid of the now unnecessary columns
            $query[] = "ALTER TABLE `#__jstats_ipaddresses` DROP `tld`;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Drop not longer required column tld from IP-Addresses');// #38
            $query = array();
            $query[] = "ALTER TABLE `#__jstats_ipaddresses` DROP `useragent`;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Drop not longer required column useragent from IP-Addresses');// #39
            $query = array();
            $query[] = "ALTER TABLE `#__jstats_visits` DROP `visitor_id`;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Drop not longer required column visitor_id from IP-Addresses');// #40
            $query = array();

            // We remove all duplicates (we can here already ignore the useragent)
            $query[] = "ALTER TABLE `#__jstats_ipaddresses` ADD `new` TINYINT(1) UNSIGNED NULL DEFAULT '0';";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Add helper column "new" to ipaddresses');
            $query = array();
            $query[] = "INSERT INTO #__jstats_ipaddresses (`ip`,`tld_id`, `ip_exclude`, `nslookup`, `type`, `new`) SELECT DISTINCT(a.`ip`),a.`tld_id`, a.`ip_exclude`, a.`nslookup`, 0, '1' FROM `#__jstats_ipaddresses` AS a GROUP BY a.ip;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Fill filtered entries to ipaddresses');
            $query = array();

            $query[] = "DELETE FROM #__jstats_ipaddresses WHERE `new` = 0;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Clean-up duplicated entries within IP-Addresses');
            $query = array();

            $query[] = "ALTER TABLE `#__jstats_ipaddresses` DROP `new` ;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Drop helper column "new" from ipaddresses');
            $query = array();

            // The IP is now always unique, so we won't need the id column anymore - this would also increase the performance in general
            $query[] = "ALTER TABLE `#__jstats_ipaddresses` DROP `id` ;";
            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Drop column "id" from ipaddresses');
            $query = array();

            $query[] = "ALTER TABLE `#__jstats_ipaddresses` ADD PRIMARY KEY (`ip`);";

            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Enable primary key to insure unique IP entries');
            $query = array();

            //remove some mistakes from old logic
            $query[] = "UPDATE `#__jstats_ipaddresses` SET `nslookup` = '' WHERE `nslookup` =  INET_NTOA(`ip`);";

            $installer->appendSQLStep('4.0.0.521 dev', $query, 'Fix wrong assigned values of nslookup in IP-Address table');
            $query = array();

            // COMMIT;

            //visitors stays empty except the unknown visitor entry, because we do not have this data

        }

        // transfer what we have
        //$installer->appendSQLStep('4.0.0.521 dev', $query);
        //$JSDatabaseAccess->populateSQL( $query );
    }

    if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '4.0.0.600 dev', '<') == true)
    {
		$query = array();

		/**
         * This is going to fix the issue, that we can link a visit to a
         * specific IP once the client is comming in from mutliple ipaddresses
         *
         * #__jstats_client_to_ip is as result not longer required
         *
         * Also included a refactoring of the new introduced columns
         */
        $query[] = "ALTER TABLE `#__jstats_visits` ADD `ip` INT(11) UNSIGNED NOT NULL DEFAULT '0';";
        $installer->appendSQLStep('4.0.0.600 dev', $query, 'Add new column "ip" to visits');
        $query =  array();

        /**
         * Without index it would take us hours to handle big DBs here
         */
        $query[] = "ALTER TABLE `#__jstats_client_to_ip` ADD INDEX (`client_id`);";
        $installer->appendSQLStep('4.0.0.600 dev', $query, 'Add index to column client_to_ip to speed up the update');
        $query =  array();

        $query[] = "UPDATE `#__jstats_visits` as v, `#__jstats_client_to_ip` as ctip SET v.ip = ctip.ip WHERE v.ip = 0 AND ctip.client_id = v.client_id;";
        $installer->appendSQLStep('4.0.0.600 dev', $query, 'Fill column "ip" of visits table');
        $query =  array();

        $query[] = 'DROP TABLE `#__jstats_client_to_ip`';

        $installer->appendSQLStep('4.0.0.600 dev', $query, 'Drop Client_To_Ip table.');
        $query =  array();


        $query[] = "ALTER TABLE `#__jstats_ipaddresses` CHANGE COLUMN `type` `ip_type` TINYINT UNSIGNED NOT NULL DEFAULT '0';";
        $query[] = "ALTER TABLE `#__jstats_browsers` CHANGE COLUMN `ordering` `browser_ordering` SMALLINT UNSIGNED NOT NULL DEFAULT 0;";
        $query[] = "ALTER TABLE `#__jstats_browsers` CHANGE COLUMN `type` `browser_type` TINYINT UNSIGNED NOT NULL DEFAULT 0;";
        $query[] = "ALTER TABLE `#__jstats_browsers` CHANGE COLUMN `location` `browser_location` TINYINT UNSIGNED NOT NULL DEFAULT 6;";
        $query[] = "ALTER TABLE `#__jstats_systems` CHANGE COLUMN `ordering` `os_ordering` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0;";
        //$query[] = "ALTER TABLE `#__jstats_systems` CHANGE COLUMN `type` `os_type` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0;";
        $query[] = "ALTER TABLE `#__jstats_clients` CHANGE COLUMN `type` `client_type` TINYINT UNSIGNED NOT NULL DEFAULT '0';";

		// transfer what we have
        $installer->appendSQLStep('4.0.0.600 dev', $query, 'Refacting for column names in DB');
        $query =  array();
		//$JSDatabaseAccess->populateSQL( $query );
	}

    if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '4.0.0.605 dev', '<') == true)
    {
        $query = array();
        $columns = $JSDatabaseAccess->js_getTableColumns("#__jstats_keywords");
        if(!$JSDatabaseAccess->js_hasTableColumn($columns, "referrer_id") )
        {
            $query[] = "ALTER TABLE `#__jstats_keywords` ADD `referrer_id` MEDIUMINT NULL;";
        }
        $installer->appendSQLStep('4.0.0.605 dev', $query, 'Append column referrer_id to keywords table');

        $query = array();
        $query[] = "ALTER TABLE `#__jstats_keywords` ADD INDEX (`visit_id`);";
        $installer->appendSQLStep('4.0.0.605 dev', $query, 'Append temporary index to keywords table');

        $query = array();
        $query[] = "ALTER TABLE `#__jstats_referrer` ADD INDEX (`visit_id`);";
        $installer->appendSQLStep('4.0.0.605 dev', $query, 'Append temporary index to referrer table');

        /**
         * Assumption, we will find the search engine URL within the referrer entries
         *
         * We have commented the previous "delete" command, so there are already URLs available in some cases
         */
        $query = array();
        $query[] = "UPDATE `#__jstats_keywords` as k, `#__jstats_referrer` as r, `#__jstats_searchers` as s SET k.referrer_id = r.refid WHERE r.visit_id > 0 AND k.visit_id > 0 AND k.referrer_id IS NULL AND  ( k.visit_id = r.visit_id  AND s.searcher_id = k.searcher_id AND r.domain LIKE CONCAT('%',s.searcher_domain,'%'))";
        $installer->appendSQLStep('4.0.0.605 dev', $query, 'Link keywords to referrers');

        $query = array();
        $query[] = "ALTER TABLE `#__jstats_keywords` DROP INDEX `visit_id`;";
        $installer->appendSQLStep('4.0.0.605 dev', $query, 'Append temporary index to keywords table');

        $query = array();
        $query[] = "ALTER TABLE `#__jstats_referrer` DROP INDEX `visit_id`;";
        $installer->appendSQLStep('4.0.0.605 dev', $query, 'Append temporary index to referrer table');
    }


    if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '4.0.0.625 dev', '<') == true)
    {
        $timzone_offset = js_getJSTimeZone() * 3600;

        $query =  array();
        $query[] = "ALTER TABLE `#__jstats_visits` ADD `changed_at` INT(10) UNSIGNED NOT NULL DEFAULT 0;";
        $installer->appendSQLStep('4.0.0.625 dev', $query, 'Add changed_at column to keywords');

        $query =  array();
        $query[] = "ALTER IGNORE TABLE `#__jstats_impressions` ADD INDEX visit_tmp (visit_id)";
        $installer->appendSQLStep('4.0.0.625 dev', $query, 'Add temporary index to column');

        $query =  array();
        $query[] = "UPDATE `#__jstats_visits` as v SET v.changed_at = ( SELECT MAX(i.timestamp) FROM `#__jstats_impressions` as i WHERE v.changed_at = 0 AND i.visit_id = v.visit_id);";
        $installer->appendSQLStep('4.0.0.625 dev', $query, 'Fill changed_at column with the latest impressions timestamp');

        $query =  array();
        $query[] = "UPDATE `#__jstats_visits` as v SET v.changed_at = (UNIX_TIMESTAMP(DATE_ADD(v.visit_date, INTERVAL v.visit_time HOUR_SECOND)) - (0)) WHERE v.changed_at = 0 OR v.changed_at IS NULL;";
        $installer->appendSQLStep('4.0.0.625 dev', $query, 'Fill remaining empty changed_at column entries with the converted values');

        $query =  array();
        $query[] = "ALTER IGNORE TABLE `#__jstats_impressions` DROP INDEX visit_tmp";
        $installer->appendSQLStep('4.0.0.625 dev', $query, 'Drop temporary index from column');

        $query =  array();
        $query[] = "ALTER TABLE `#__jstats_visits` DROP `visit_date` ;";
        $query[] = "ALTER TABLE `#__jstats_visits` DROP `visit_time` ;";
        $installer->appendSQLStep('4.0.0.625 dev', $query, 'Drop no longer required columns');

        $query =  array();
        $query[] = "ALTER IGNORE TABLE `#__jstats_visits` ADD INDEX changed_at (changed_at)";
        $installer->appendSQLStep('4.0.0.625 dev', $query, 'Add index to column');

    }


    if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '4.0.0.650 dev', '<') == true)
    {
        $timzone_offset = js_getJSTimeZone() * 3600;

        $query = array();
        $query[] = "ALTER TABLE `#__jstats_referrer` ADD `timestamp` INT(10) UNSIGNED NOT NULL DEFAULT 0;";
        $installer->appendSQLStep('4.0.0.650 dev', $query, 'Add Timestamp column to referrers');

        /**
         * The dates & time columns are stored using the local timezone, so we have to reverse the date
         */

        /**
         * We fill the timestamp with an corresponding entry from the impressions table. We can only assume that the first visit on this day had the referrer attached
         *
         * Keep the braketes around the timzone offset as it could be negative
         *
         * All timestamps are stored as GMT time and not based on the local timezone!!!
         *
         * @attention the timestamp of referrer should have normally a corresponding entry in the impressions
         */
        $query =  array();
        $query[] = "UPDATE `#__jstats_referrer` as r SET r.timestamp = ( SELECT MIN(i.timestamp) FROM `#__jstats_impressions` as i WHERE r.visit_id = i.visit_id AND i.timestamp >= UNIX_TIMESTAMP( CONCAT( r.year,  '-', r.month,  '-', r.day,  ' 00:00:00' ) ) + ( 0 ) AND i.timestamp <= UNIX_TIMESTAMP( CONCAT( r.year,  '-', r.month,  '-', r.day,  ' 23:59:59' ) ) + ( 0 ) ) WHERE r.timestamp = 0 AND r.visit_id > 0;";
        $installer->appendSQLStep('4.0.0.650 dev', $query, 'Fill new timestamp column with values');
        /**
         * In the case of we have some empty entries, we fill it with at least a timestamp based on the day
         */
        $query =  array();
        $query[] = "UPDATE `#__jstats_referrer` as r SET r.timestamp = ( UNIX_TIMESTAMP(CONCAT(r.year, '-', r.month, '-', r.day)) - (0) ) WHERE r.timestamp = 0 OR r.timestamp IS NULL;";
        $installer->appendSQLStep('4.0.0.650 dev', $query, 'Fill new timestamp column with the converted values');

        $query =  array();
        $query[] = "ALTER TABLE `#__jstats_referrer` DROP `year` ;";
        $query[] = "ALTER TABLE `#__jstats_referrer` DROP `month` ;";
        $query[] = "ALTER TABLE `#__jstats_referrer` DROP `day` ;";
        $installer->appendSQLStep('4.0.0.650 dev', $query, 'Drop no longer required columns');

    }

    if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '4.0.0.700 dev', '<') == true)
    {
        $query =  array();
        $query[] = "ALTER TABLE `#__jstats_keywords` ADD `timestamp` INT(10) UNSIGNED NOT NULL DEFAULT 0;";
        $installer->appendSQLStep('4.0.0.700 dev', $query, 'Add Timestamp column to keywords');

        $query =  array();
        $query[] = "UPDATE `#__jstats_keywords` as k SET k.timestamp = ( SELECT MIN(i.timestamp) FROM `#__jstats_impressions` as i WHERE  k.visit_id = i.visit_id AND k.kwdate = CAST( FROM_UNIXTIME(i.timestamp + (0)) AS Date) GROUP BY i.visit_id ) WHERE k.timestamp = 0 AND k.visit_id > 0;";
        $installer->appendSQLStep('4.0.0.700 dev', $query, 'Fill new timestamp column with values from linked tables');

        $query =  array();
        $query[] = "UPDATE `#__jstats_keywords` as k SET k.timestamp = (UNIX_TIMESTAMP(k.kwdate) - (0)) WHERE k.timestamp = 0 OR k.visit_id > 0;";
        $installer->appendSQLStep('4.0.0.700 dev', $query, 'Fill new timestamp column with the converted values');

        $query =  array();
        $query[] = "ALTER TABLE `#__jstats_keywords` DROP `kwdate` ;";
        $installer->appendSQLStep('4.0.0.700 dev', $query, 'Drop no longer required columns');
    }
    /*
    if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '4.0.0.705 dev', '<') == true)
    {
        $query =  array();
        $query[] = "ALTER TABLE `#__jstats_page_request_c` ADD `timestamp` INT(10) UNSIGNED NOT NULL DEFAULT 0;";
        $installer->appendSQLStep('4.0.0.705 dev', $query, 'Add timestamp column to keywords');

        $query =  array();
        $query[] = "UPDATE `#__jstats_page_request_c` as pr SET pr.timestamp = ( UNIX_TIMESTAMP(CONCAT(pr.year, '-', pr.month, '-', pr.day, ' ', pr.hour, ':00:00')) - (0) ) WHERE pr.timestamp = 0 ;";
        $installer->appendSQLStep('4.0.0.705 dev', $query, 'Fill new changed_at column with the converted values');

        $query =  array();
        $query[] = "ALTER TABLE `#__jstats_page_request_c` DROP `hour` ;";
        $query[] = "ALTER TABLE `#__jstats_page_request_c` DROP `day` ;";
        $query[] = "ALTER TABLE `#__jstats_page_request_c` DROP `month` ;";
        $query[] = "ALTER TABLE `#__jstats_page_request_c` DROP `year` ;";
        $installer->appendSQLStep('4.0.0.705 dev', $query, 'Drop no longer required columns');
        $query =  array();
	}*/

    if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '4.0.0.755 dev', '<') == true)
    {
        $query =  array();
        $query[] = "ALTER TABLE  `#__jstats_configuration` ADD  `params` TEXT NOT NULL DEFAULT  '';";
        $installer->appendSQLStep('4.0.0.755 dev', $query, 'Append params field to configuration');
	}

    /**
     * By introducing multiple ways to determine the country such
     * - by tld
     * - by whois
     * - by ip2nation
     * - ...
     *
     * it makes more sense to add the country code to the DB rather than linking the tld table
     */
    if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '4.0.0.759 dev', '<') == true)
    {
        $query = array();
        $columns = $JSDatabaseAccess->js_getTableColumns("#__jstats_ipaddresses");
        if(!$JSDatabaseAccess->js_hasTableColumn($columns, "country") )
        {
            $query[] = "ALTER IGNORE TABLE `#__jstats_ipaddresses` ADD `country` VARCHAR(4) NULL;";
        }
        $installer->appendSQLStep('4.0.0.760 dev', $query, 'Add country flag to ipaddresses');

        $query =  array();
        $topLevelcolumns = $JSDatabaseAccess->js_getTableColumns("#__jstats_topleveldomains");
        if(!empty($topLevelcolumns))
        {
            $query[] = "UPDATE  `#__jstats_ipaddresses` as a, #__jstats_topleveldomains as t SET a.country = t.tld WHERE  a.tld_id = t.tld_id;";
        }
        $installer->appendSQLStep('4.0.0.760 dev', $query, 'Copy value from tld to country field');

        $query =  array();
        $query[] = "UPDATE  `#__jstats_ipaddresses` as a SET a.country = NULL WHERE a.country IS NOT NULL AND LENGTH(a.country) > 2;";
        $installer->appendSQLStep('4.0.0.760 dev', $query, 'Copy value from tld to country field');
	}

    /**
     * We added once the referrer_id to the keywords, but there are entries without being linked to the referrer table. This sql is going to reconstruct the URL
     */
    if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '4.0.0.761 dev', '<') == true)
    {
        $query = array();
        $query[] = "UPDATE `#__jstats_keywords` as k, `#__jstats_referrer` as r, `#__jstats_searchers` as s SET k.referrer_id = r.refid WHERE r.visit_id > 0 AND k.visit_id > 0 AND k.referrer_id IS NULL AND  ( k.visit_id = r.visit_id  AND s.searcher_id = k.searcher_id AND r.domain LIKE CONCAT('%',s.searcher_domain,'%'))";
        $installer->appendSQLStep('4.0.0.761 dev', $query, 'Link keywords to existing referrers');

        $query = array();
        $query[] = "
            INSERT INTO `#__jstats_referrer` (referrer, domain,visit_id, timestamp)
            (
                SELECT DISTINCT 
                    CONCAT(
                    'http://',
                    IF( LOCATE('www', s.searcher_domain ) = 1 OR ( SUBSTRING_INDEX(s.searcher_domain, '.', -1 ) REGEXP '^[0-9].*') > 0,'','www'),
                    IF( LOCATE('.',s.searcher_domain ) = 1 OR LOCATE('www',s.searcher_domain ) = 1 OR ( SUBSTRING_INDEX(s.searcher_domain, '.', -1 ) REGEXP '^[0-9].*') > 0,'','.'),
                    s.searcher_domain,
                    IF(LENGTH(SUBSTRING_INDEX(s.searcher_domain, '.', -1 )) > 0,'','com'),
                    '?',
                    SUBSTRING_INDEX(s.searcher_key, '|', -1 ),
                    k.keywords
                    ) as referrer,
                    CONCAT(s.searcher_domain, IF(LENGTH(SUBSTRING_INDEX(s.searcher_domain, '.', -1 )) > 0,'','com')),
                    k.visit_id,
                    k.timestamp
                FROM `#__jstats_keywords` as k
                INNER JOIN `#__jstats_searchers` as s ON s.searcher_id = k.searcher_id
                WHERE k.referrer_id IS NULL AND k.visit_id > 0
            )
        ";
        $installer->appendSQLStep('4.0.0.761 dev', $query, 'We create the missing entries in referrer table');

        $query = array();
        $query[] = "UPDATE `#__jstats_keywords` as k, `#__jstats_referrer` as r, `#__jstats_searchers` as s SET k.referrer_id = r.refid WHERE r.visit_id > 0 AND k.visit_id > 0 AND k.referrer_id IS NULL AND  ( k.visit_id = r.visit_id  AND s.searcher_id = k.searcher_id AND r.domain LIKE CONCAT('%',s.searcher_domain,'%'))";
        $installer->appendSQLStep('4.0.0.761 dev', $query, 'Link keywords to new created referrers');
	}

    if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '4.0.0.765 dev', '<') == true)
    {
        $query = array();
        $query[] = "
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
        $installer->appendSQLStep('4.0.0.765 dev', $query, 'Cache table created');

        $query = array();
        $columns = $JSDatabaseAccess->js_getTableColumns("#__jstats_ipaddresses");
        if($JSDatabaseAccess->js_hasTableColumn($columns, "country") )
        {
            $query[] = "ALTER TABLE `#__jstats_ipaddresses` CHANGE COLUMN `country` `code` VARCHAR(4) NULL;";
          }

        $installer->appendSQLStep('4.0.0.765 dev', $query, 'Rename country column to code to prevent conflict with ip2nation');
    }

    if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '4.0.0.790 dev', '<') == true)
    {
        $query = array();
        $query[] = "UPDATE `#__jstats_ipaddresses` as a SET a.ip_type = 0 WHERE a.ip_type != 2;";
        $installer->appendSQLStep('4.0.0.790 dev', $query, 'Fixed wrong assigned initial ip_type flag back to unknown status');
    }


    if (js_JSUtil::JSVersionCompare( $updateFromJSVersion, '4.0.1.6 dev', '<') == true)
    {
        $hasIndexVisitId = $JSDatabaseAccess->hasTableIndexForColumn("#__jstats_impressions","visit_id");
        $hasIndexTimetamp = $JSDatabaseAccess->hasTableIndexForColumn("#__jstats_impressions","timestamp");

        $query =  array();
        if(is_null($hasIndexVisitId)) $query[] = "ALTER IGNORE TABLE `#__jstats_impressions` ADD INDEX visit_id_tmp (`visit_id`);";
        $installer->appendSQLStep('4.0.1.6 dev', $query, 'Add temporary visit_id index to column __jstats_impressions'); // #13

        $query =  array();
        if(is_null($hasIndexTimetamp)) $query[] = "ALTER IGNORE TABLE `#__jstats_impressions` ADD INDEX timestamp_tmp (`timestamp`);";
        $installer->appendSQLStep('4.0.1.6 dev', $query, 'Add temporary timestamp index to column __jstats_impressions'); // #13

        $query = array();
        $query[] = "UPDATE `#__jstats_impressions` as i, #__jstats_visits as v SET i.timestamp = v.changed_at WHERE (i.timestamp = 0 or i.timestamp IS NULL) and i.visit_id = v.visit_id;";
        $installer->appendSQLStep('4.0.1.6 dev', $query, 'Fill empty impressions timestamps using the timestamp of the visit');

        $hasIndexVisitId = $JSDatabaseAccess->hasTableKeyName("#__jstats_impressions","visit_id_tmp");
        $hasIndexTimetamp = $JSDatabaseAccess->hasTableKeyName("#__jstats_impressions","timestamp_tmp");

        $query =  array();
        if($hasIndexVisitId) $query[] = "ALTER IGNORE TABLE `#__jstats_impressions` DROP INDEX visit_id_tmp;";
        $installer->appendSQLStep('4.0.1.6 dev', $query, 'Drop temporary visit_id index from column __jstats_impressions'); // #13
        $query =  array();
        if($hasIndexTimetamp) $query[] = "ALTER IGNORE TABLE `#__jstats_impressions` DROP INDEX timestamp_tmp;";
        $installer->appendSQLStep('4.0.1.6 dev', $query, 'Drop temporary timestamp index from column __jstats_impressions'); // #13
    }
    if(js_JSUtil::JSVersionCompare( $updateFromJSVersion, '4.0.1.7 beta', '<') == true)
    {
        $query =  array();
        $query[] = "UPDATE  `#__jstats_clients` SET client_type = 2 WHERE  ( `useragent` LIKE '%crawl%' OR  `useragent` LIKE '%spider%' OR  `useragent` LIKE '%bot%') AND `client_type` = 1";
        $installer->appendSQLStep('4.0.1.7 beta', $query, 'Fix wrong identified Crawler/Spider/Spider entries');

    }

    if(js_JSUtil::JSVersionCompare( $updateFromJSVersion, '4.0.1.9 beta', '<') == true)
    {
        $query =  array();
        $query[] = "UPDATE  `#__jstats_clients` as c, `#__jstats_ipaddresses` as a, `#__jstats_visits` as v SET a.ip_type = 1, c.client_type = 2, c.browser_id = 1364 WHERE v.client_id = c.client_id and v.ip = a.ip and a.nslookup LIKE '%msnbot%'";
        $installer->appendSQLStep('4.0.1.9 beta', $query, 'Move MSNBot from regular visitors to bots');

        $query =  array();
        $query[] = "UPDATE  `#__jstats_clients` as c SET c.client_type = 2, c.browser_id = 1460 WHERE `useragent` LIKE '%bingbot%'";

        $installer->appendSQLStep('4.0.1.9 beta', $query, 'Check old records for Bing-Bots to bots');

    }
    if(js_JSUtil::JSVersionCompare( $updateFromJSVersion, '4.0.2.1 beta', '<') == true)
    {
        $query =  array();
        $query[] = 'DROP TABLE IF EXISTS `#__jstats_iptocountry`';
        $installer->appendSQLStep('4.0.2.1 beta', $query, 'Drop not anymore used table jstats_iptocountry');
    }


}

/**
 * ===== Here are some SQL statements to perform some clean-up work. It should be only required to call them once, but
 * ===== if any those SQL enquires do change/fix anything again and again, we have something wrong in our logic
 */

/**
 * This statement is merging the same client entries together based on the visitor_id.
 * Multiple entries are ok, but in this case the browser_version needs to be different!!
 *
 * Keep in mind, we have the same useragent in the DB multiple times, because it depends
 * also on the IP and hence we can't remove the visitor_id IS NOT NULL!!
 */

/*
UPDATE `jos_jstats_visits` as v, `jos_jstats_clients` as c SET v.client_id = (
SELECT MAX(sc.client_id)
FROM `jos_jstats_clients` as sc
WHERE sc.visitor_id = c.visitor_id AND sc.useragent LIKE c.useragent
GROUP BY c.visitor_id, c.useragent
)
WHERE c.client_id = v.client_id and c.visitor_id IS NOT NULL
*/

/**
 *  This statement cleansup all ununsed client entries, which are not assigned to any visit. Will happen after the previous statement
 */
    
/*
DELETE FROM `jos_jstats_clients` USING `jos_jstats_clients` LEFT OUTER JOIN `jos_jstats_visits` ON `jos_jstats_clients`.client_id = `jos_jstats_visits`.client_id WHERE `jos_jstats_visits`.visit_id IS NULL
*/
