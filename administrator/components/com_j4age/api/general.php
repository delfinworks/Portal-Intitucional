<?php

              
/**
 * @package j4age
 * @copyright Copyright (C) 2009-@THISYEAR@ j4age Team. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * Thank you to the project j4age and it's team, on which roots this project is build on.
 */
             
          

if( !defined( '_JS_STAND_ALONE' ) && !defined( '_JEXEC' ) ) {
	die( 'JS: No Direct Access to '.__FILE__ );
}

/**
 *  This class contain API (application programming interface) to JoomlaStats.
 *
 *  Eg. of including JoomlaStats API
 * 	  require_once( dirname(__FILE__) .DIRECTORY_SEPARATOR. 'joomla' .DIRECTORY_SEPARATOR. 'administrator' .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_j4age' .DIRECTORY_SEPARATOR. 'api' .DIRECTORY_SEPARATOR. 'general.php' );
 *
 *
 *  NOTICE: If You are looking for method and it is not here, please contact with 'JoomlaStats Team' (http://joomlastats.org) or directly to Andrzej Trelinski (atrel.devel1@gmail.com)
 *             We try to add method as soon as it is possible.
 *
 *
 *
 *  All methods are static
 */
class js_JSApiGeneral
{
////////
//
// Visitors    section
//
////////

	/**
	 *       MAIN FUNCTION TO SELECT NUMBER OF VISITORS - ALL KINDS
	 *
	 * Gets number of visitors
	 *
	 * @param in  $visitors_type           values - one of: 'all'; _JS_DB_IPADD__TYPE_NOT_IDENTIFIED_VISITOR; _JS_DB_IPADD__TYPE_REGULAR_VISITOR; _JS_DB_IPADD__TYPE_BOT_VISITOR; // inlclude db.constants.php file to have access to defines 		require_once( dirname(__FILE__) .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'database' .DIRECTORY_SEPARATOR. 'db.constants.php' );
	 * @param in  $include_summarized      values: true; false; 'auto'; //true - results are with summarized/purged data; false - results are without summarized/purged data; 'auto' - value is taken from current JS configuration; //if You have object of class js_JSConf, it is recomended using '$JSConf->include_summarized' instead of 'auto' for performance
	 * @param in  $date...                 formats: ''; '2009-03-25'; '2009-3-9'; '2009-03-25 16:42:56' (NOT RECOMENDED); //use '' to omit time limitation //both date are inclusive ( $date_from =< result =< $date_to)
	 * @param out $VisitorsNumber_result   return number of visitors (users that visit all pages) in respect of input criteria
	 *
	 * NOTICE:
	 *     $date arguments are insecure. Do not share them on front site without validation!
	 *
	 * @return true on success
	 */
	function getVisitorsNumber( $visitors_type, $include_summarized, $timestamp_from, $timestamp_to, &$VisitorsNumber_result ) {
		require_once( dirname(__FILE__) .DS. '..' .DS. 'database' .DS. 'select.one.value.php' );
		$JSDbSOV = new js_JSDbSOV();
		
		if (($include_summarized !== true) && ($include_summarized !== false)) {
			require_once( dirname(__FILE__) .DS. '..'.DS. 'libraries' .DS. 'base.classes.php' );
			$JSConf = js_JSConf::getInstance();
			$include_summarized = $JSConf->include_summarized;
		}
		
		$buid = null;
		if ($include_summarized == false) {
			$JSDbSOV->getBuid($buid);
		}
		
		require_once( dirname(__FILE__) .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'database' .DIRECTORY_SEPARATOR. 'db.constants.php' );
		if ( ($visitors_type !== _JS_DB_IPADD__TYPE_NOT_IDENTIFIED_VISITOR) && ($visitors_type !== _JS_DB_IPADD__TYPE_REGULAR_VISITOR) && ($visitors_type !== _JS_DB_IPADD__TYPE_BOT_VISITOR) ) {
			$visitors_type = '';//all will be selected
		}
		
		return $JSDbSOV->selectNumberOfVisitors( $visitors_type, $include_summarized, $buid, $timestamp_from, $timestamp_to, $VisitorsNumber_result );
	}


	/**
	 *       MAIN FUNCTION TO SELECT NUMBER OF VISITORS WITH RESOLUTION- ALL KINDS
	 *
	 * Gets number of visitors
	 *
	 * @param in  $resolution          values - one of: 'day', 'month', 'year'
	 * @param in  $visitors_type       values - one of: 'all'; _JS_DB_IPADD__TYPE_NOT_IDENTIFIED_VISITOR; _JS_DB_IPADD__TYPE_REGULAR_VISITOR; _JS_DB_IPADD__TYPE_BOT_VISITOR; // inlclude db.constants.php file to have access to defines 		require_once( dirname(__FILE__) .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'database' .DIRECTORY_SEPARATOR. 'db.constants.php' );
	 * @param in  $include_summarized  values: true; false; 'auto'; //true - results are with summarized/purged data; false - results are without summarized/purged data; 'auto' - value is taken from current JS configuration; //if You have object of class js_JSConf, it is recomended using '$JSConf->include_summarized' instead of 'auto' for performance
	 * @param in  $date...             formats: ''; '2009-03-25'; '2009-3-9'; '2009-03-25 16:42:56' (NOT RECOMENDED); //use '' to omit time limitation //both date are inclusive ( $date_from =< result =< $date_to)
	 * @param out $arr_obj_result      Result. Array of objects, sorted ascending, all values filled, indexed.
	 *
	 * NOTICE:
	 *     $date arguments are insecure. Do not share them on front site without validation!
	 *
	 * @param out $VisitorsNumber_result integer
	 * @return true on success
	 */
	function getAmountOfVisitsByResolution( $resolution, $visitors_type, $timestamp_from, $timestamp_to, &$arr_obj_result, $cacheKey = null ) {
		require_once( dirname(__FILE__) .DS. '..' .DS. 'database' .DS. 'select.many.rows.php' );
		require_once( dirname(__FILE__) .DS. '..'.DS. 'libraries' .DS. 'api.base.php' );

		require_once( dirname(__FILE__) .DS. '..' .DS. 'database' .DS. 'db.constants.php' );
		if ( ($visitors_type !== _JS_DB_IPADD__TYPE_NOT_IDENTIFIED_VISITOR) && ($visitors_type !== _JS_DB_IPADD__TYPE_REGULAR_VISITOR) && ($visitors_type !== _JS_DB_IPADD__TYPE_BOT_VISITOR) ) {
			$visitors_type = '';//all will be selected
		}
		
		$JSDbSMR = new js_JSDbSMR();
		$db_arr_obj_result = array();
		$bResult  = $JSDbSMR->selectNumberOfVisitorsWithResolution( $resolution, $visitors_type, null, $timestamp_from, $timestamp_to, $db_arr_obj_result, $cacheKey );
		
		$JSApiBase = new js_JSApiBase();
		$bResult &= $JSApiBase->fillMissingDataForResolution( $resolution, $timestamp_from, $timestamp_to, $db_arr_obj_result, $arr_obj_result );
		
		return $bResult;
	}
	
////////
//
// END: Visitors    section
//
////////

////////
//
// Page Impression    section
//
////////
	

	/**
	 *
	 * @param in  $date...               formats: ''; '2009-03-25'; '2009-3-9'; '2009-03-25 16:42:56' (NOT RECOMENDED); //use '' to omit time limitation //both date are inclusive ( $date_from =< result =< $date_to)
	 * @param in  $include_summarized    values: true; false; 'auto'; //true - results are with summarized/purged data; false - results are without summarized/purged data; 'auto' - value is taken from current JS configuration; //if You have object of class js_JSConf, it is recomended using '$JSConf->include_summarized' instead of 'auto' for performance
	 * @param out $obj_result            result. Object that contains many data about page impressions
	 *
	 * NOTICE:
	 *     $date arguments are insecure. Do not share them on front site without validation!
	 *
	 * @return true on success
	 */
	function getPagesImpressionsSums( $date_from, $date_to, &$obj_result ) {
		require_once( dirname(__FILE__) .DS. '..' .DS. 'database' .DS. 'select.one.row.php' );
		$JSDbSOR = new js_JSDbSOR();
		
        return $JSDbSOR->getPagesImpressionsSums( $date_from, $date_to, $obj_result );
	}
	
////////
//
// END: Page Impression    section
//
////////

////////
//
// Operating Systems    section
//
////////

	/**
	 *  Get list of 'Operating Systems' that visits page
	 *
	 *  Only Operating Systems for regular users (excluding bots/spiders) is returned
	 * 
	 *  @param in  $date_from; Eg. '2009-03-24', ''
	 *  @param in  $date_to; Eg. '2009-06-24', ''
	 *  @param in  $include_summarized - true, false, 'auto'; //true - results are with summarized/purged data, false - results are without summarized/purged data, 'auto' - value is taken from current JS configuration; //if You have object of class js_JSConf, it is recomended using '$JSConf->include_summarized' instead of 'auto' for performance
	 *  @param in  $OSDirectoryName; Eg. 'browser-png-16x16-1'; ''; //if '' default directory is used
	 *  @param out $arr_obj_result
	 *  @return true on success
	 */
	function getOperatingSystemVisistsArr( $date_from, $date_to, $include_summarized = false, $OSDirectoryName, &$arr_obj_result, $objecttype = -1  ) {
        require_once( dirname(__FILE__) .DS. '..'.DS. 'libraries' .DS. 'api.base.php' );
		$JSApiBase = new js_JSApiBase();
		return $JSApiBase->getOperatingSystemVisistsArr( $date_from, $date_to, false, $OSDirectoryName, $arr_obj_result, $objecttype );
	}
	


////////
//
// END: Operating Systems    section
//
////////

////////
//
// Visitors and Page Impressions Counting   section
//
////////


	/**
	 *  This function return details about user (visitor) that visit page (about 35 varius data). Details are returned 
	 *    in object of js_VisitorEx class. See file base.classes.php file for details or use PHP print_r() method
	 *
	 *  @param in  $OSDirectoryName;      eg.: 'os-png-16x16-1'; '';      //if '' default directory is used
	 *  @param in  $BrowserDirectoryName; eg.: 'browser-png-16x16-1'; ''; //if '' default directory is used
	 *  @param in  $TldDirectoryName;     eg.: 'tld-png-16x11-1'; '';     //if '' default directory is used
	 *  @param out $Visitor_result;       Result. Visitor object that contain all data about visitor (like: country, system, web browser, IP etc.)
	 *
	 *  @return true on success
	 */
	function getVisitorDetails( $OSDirectoryName, $BrowserDirectoryName, $TldDirectoryName, &$location, &$client ) {
        require_once( dirname(__FILE__) .DS. '..'.DS. 'libraries' .DS. 'api.base.php' );
		$JSApiBase = new js_JSApiBase();
		return $JSApiBase->getVisitorDetails( $OSDirectoryName, $BrowserDirectoryName, $TldDirectoryName, $location, $client );
	}

////////
//
// END: Visitors and Page Impressions Counting   section
//
////////

////////
//
// miscellaneous    section
//
////////

	/**
	 * Gets JoomlaStats database size
	 *
	 * @param out integer $JSDatabaseSize - in bytes
	 * @return true on success
	 */
	function getJSDatabaseSize(&$JSDatabaseSize_result) {
		require_once( dirname( __FILE__ ) .DS. '..' .DS. 'database' .DS. 'select.one.value.php' );
		$JSDbSOV = new js_JSDbSOV();
		return $JSDbSOV->getJSDatabaseSize($JSDatabaseSize_result);
	}

	/**
	 * Retrun JoomlaStats version
	 *
	 * NOTICE:
	 *   Function could return false in case when PHP files are from different 
	 *   version than database.
	 *
	 * @param out string $JSVersion_result - eg.: '2.3.0.113 dev' (in case of development snapshot), '2.2.3.150' (in case of release) 
	 * @return true on success, false when something is wrong - see notice above.
	 */
	function getJSVersion(&$JSVersion_result) {
		require_once( dirname( __FILE__ ) .DS. '..' .DS. 'database' .DS. 'select.one.value.php' );
		require_once( dirname( __FILE__ ) .DS. '..'.DS. 'libraries' .DS. 'base.classes.php' );
		$JSDatabaseVersion = '';
		$JSPHPVersion = '';
		
		$JSConfDef = new js_JSConfDef();
		$JSPHPVersion = $JSConfDef->BuildVersion;
		
		$JSDbSOV = new js_JSDbSOV();
		$bResult = $JSDbSOV->getJSDatabaseVersion($JSDatabaseVersion);
		
		if ($bResult == false)
			return false;
			
		if (strlen($JSPHPVersion) == 0)
			return false;
			
		if ($JSDatabaseVersion == $JSPHPVersion) {
			$JSVersion_result = $JSDatabaseVersion;
			return true;
		}
		return false;
	}
////////
//
// END: miscellaneous    section
//
////////
}


