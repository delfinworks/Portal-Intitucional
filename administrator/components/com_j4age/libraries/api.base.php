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
 *  This class contain JS API methods that body is to big to be in js_JSApiGeneral class
 *
 *  All methods are static
 */
class js_JSApiBase
{
	/**
	 * Get list of Operating Systems that are recognized by JoomlaStats
	 * see also getAvailableSystemListForHuman
	 *
	 * @param out integer $AvailableSystemList_result
	 * @return true on success
	 */
	function getAvailableOperatingSystemList( &$AvailableOperatingSystemList_result ) {
		require_once( dirname(__FILE__) .DS. '..' .DS.'database' .DS.'select.many.rows.php' );
		$JSDbSMR = new js_JSDbSMR();
		$systems_arr = array();
		if ($JSDbSMR->getAvailableOperatingSystemArrForHuman( $systems_arr ) == false)
			return false;
			
		$AvailableOperatingSystemList_result = $this->addUrlsToOperatingSystemList($systems_arr, '');
		return true;
	}
		
	/**
	 * Get list of Operating Systems that are recognized by JoomlaStats
	 * see also getAvailableSystemList
	 *
	 * @param out integer $AvailableSystemList_result
	 * @return true on success
	 */
	function getAvailableOperatingSystemListForHuman( &$AvailableOperatingSystemListForHuman_result ) {
		require_once( dirname(__FILE__) .DS. '..' .DS. 'database' .DS.'select.many.rows.php' );
		$JSDbSMR = new js_JSDbSMR();
		$systems_arr = array();
		if ($JSDbSMR->getAvailableOperatingSystemArrForHuman( $systems_arr ) == false)
			return false;
			
		$AvailableOperatingSystemListForHuman_result = $this->addUrlsToOperatingSystemList($systems_arr, '');
		return true;
	}
	
	
	/** @private 
	 *  $OSDirectoryName eg. 'os-png-16x16-1'; ''; // if '' default directory is used
	 *  $directory_name  
	 */
	function addUrlsToOperatingSystemList( $OperatingSystemListArr, $OSDirectoryName ) {
		require_once( dirname(__FILE__) .DS. 'util.classes.php' );
		if ($OSDirectoryName == '') {
        	$JSSystemConst = new js_JSSystemConst();
        	$OSDirectoryName = $JSSystemConst->defaultPathToImagesOs;
		}
		$JSUtil = new js_JSUtil();
		
		$SystemList_result = array();
		foreach( $OperatingSystemListArr as $row) {
			$this->addUrlsToOSObject($JSUtil, $OSDirectoryName, $row);
			$SystemList_result[] = $row;
			/*
			//$obj = new stdClass();
			$obj = $row;
			$obj->os_img_url      = $JSUtil->getImageWithUrl($obj->os_img, $OSDirectoryName);
			$obj->os_img_html     = '<img src="'.$obj->os_img_url.'" alt="'.$obj->os_name.'" />';
			if (isset($obj->ostype_img))
				$obj->ostype_img_url  = $JSUtil->getImageWithUrl($obj->ostype_img, $OSDirectoryName);
			if (isset($obj->ostype_name))
				$obj->ostype_img_html = '<img src="'.$obj->ostype_img_url.'" alt="'.$obj->ostype_name.'" />';
			
			$SystemList_result[] = $obj;
			*/
		}
		
		return $SystemList_result;
	}
	
	/** @private 
	 *  $OSDirectoryName eg. 'os-png-16x16-1'; // empty string ('') is not allowed!
	 *  $OS_inout object of class js_OS  
	 */
	function addUrlsToOSObject( $JSUtil, $OSDirectoryName, &$OS_inout ) {
		$OS = $OS_inout;
		$OS->os_img_url      = js_JSUtil::getImageWithUrl($OS->os_img, $OSDirectoryName);
		$OS->os_img_html     = js_JSUtil::renderImg(empty($OS->os_img)?'unknown':$OS->os_img, $OSDirectoryName);
		if (isset($OS->ostype_img))
			$OS->ostype_img_url  = $JSUtil->getImageWithUrl($OS->ostype_img, $OSDirectoryName);
		if (isset($OS->ostype_name))
			$OS->ostype_img_html = js_JSUtil::renderImg(empty($OS->ostype_img)?'unknown':$OS->ostype_img, $OSDirectoryName); 

		$OS_inout = $OS;
		
		return true;
	}

	/** @private 
	 *  $BrowserDirectoryName eg. 'browser-png-16x16-1'; // empty string ('') is not allowed!
	 *  $Browser_inout object of class js_Browser  
	 */
	function addUrlsToBrowserObject( $JSUtil, $BrowserDirectoryName, &$Browser_inout ) {
		$Browser = $Browser_inout;
		$Browser->browser_img_url      = $JSUtil->getImageWithUrl($Browser->browser_img, $BrowserDirectoryName);
		$Browser->browser_img_html     = js_JSUtil::renderImg(empty($Browser->browser_img)?'unknown':$Browser->browser_img, $BrowserDirectoryName);
		if (isset($Browser->browsertype_img))
			$Browser->browsertype_img_url  = $JSUtil->getImageWithUrl($Browser->browsertype_img, $BrowserDirectoryName);
		if (isset($Browser->browsertype_name))
			$Browser->browsertype_img_html = js_JSUtil::renderImg(empty($Browser->browsertype_img)?'unknown':$Browser->browsertype_img, $BrowserDirectoryName);

		$Browser_inout = $Browser;
		
		return true;
	}

	/** @private 
	 *  $TldDirectoryName eg. 'tld-png-16x16-1'; // empty string ('') is not allowed!
	 *  $Tld_inout object of class js_Tld  
	 *  
	 * @todo for unknown this function will not work. Probably we should to fix all code (without this function)
	 */
	function addUrlsToTldObject( $JSUtil, $TldDirectoryName, &$Tld_inout ) {
		$Tld = $Tld_inout;
		$Tld->tld_img_url      = $JSUtil->getImageWithUrl($Tld->tld_img, $TldDirectoryName);
		$Tld->tld_img_html     = js_JSUtil::renderImg(empty($Tld->tld_img)?'unknown':$Tld->tld_img, $TldDirectoryName);

		$Tld_inout = $Tld;
		
		return true;
	}

	function getOperatingSystemVisistsArr( $timestamp_from, $timestamp_to, $include_summarized, $OSDirectoryName, &$arr_obj_result, $objecttype = -1  ) {
		if (($include_summarized !== true) && ($include_summarized !== false)) {
			require_once( dirname(__FILE__) .DS. 'libraries'.DS. 'base.classes.php' );
			$JSConf = js_JSConf::getInstance();;
			$include_summarized = $JSConf->include_summarized;
		}
		require_once( dirname(__FILE__) .DS. '..' .DS. 'database' .DS.'select.many.rows.php' );
		$JSDbSMR = new js_JSDbSMR();
		
		$OperatingSystemVisistsArr = array();
		$res = $JSDbSMR->getOperatingSystemVisistsArr( $timestamp_from, $timestamp_to, $include_summarized, $OperatingSystemVisistsArr, $objecttype );
		if ($res == false)
			return false;
		
		$arr_obj_result = $this->addUrlsToOperatingSystemList($OperatingSystemVisistsArr, $OSDirectoryName);
		
		return true;
	}


	/**
	 *  This function return details about user (visitor) that visit page
	 *
	 *  @param in  $OSDirectoryName;      eg.: 'os-png-16x16-1'; '';      //if '' default directory is used
	 *  @param in  $BrowserDirectoryName; eg.: 'browser-png-16x16-1'; ''; //if '' default directory is used
	 *  @param in  $TldDirectoryName;     eg.: 'tld-png-16x11-1'; '';     //if '' default directory is used
	 *
	 *  @return true on success //@todo
	 */
	function getVisitorDetails( $OSDirectoryName, $BrowserDirectoryName, $TldDirectoryName, &$location, &$client ) {
		require_once( dirname(__FILE__) .DS. 'count.classes.php' );
		require_once( dirname(__FILE__) .DS. 'util.classes.php' );

		$JSCountVisitor = new js_JSCountVisitor();
		
		$VisitorUserAgent = $JSCountVisitor->getVisitorUserAgent();

		$VisitorIpStr = null;
		$JSCountVisitor->getVisitorIp( $VisitorIpStr );
		$VisitorIp = js_ip2long($VisitorIpStr);

		//$JSCountVisitor->recognizeVisitor( $VisitorIp, $VisitorUserAgent, $updateTldInJSDatabase, $Visitor );

        $visitor = null;

        $bResult = IPInfoHelper::performLocationCheck( $location, $VisitorIp, true );
        $bResult = $JSCountVisitor->retrieveClientDetails( $VisitorUserAgent, $client );

		//additional members
		$requested_url = $JSCountVisitor->getRequestedUri();

		//$Visitor->RequestedPage = $requested_url;
		//$Visitor->joomla_userid = $JSCountVisitor->GetJoomlaCmsUserId();
		
		
		if ($OSDirectoryName == '') {
        	$JSSystemConst = new js_JSSystemConst();
        	$OSDirectoryName = $JSSystemConst->defaultPathToImagesOs;
		}
		if ($BrowserDirectoryName == '') {
        	$JSSystemConst = new js_JSSystemConst();
        	$BrowserDirectoryName = $JSSystemConst->defaultPathToImagesBrowser;
		}
		if ($TldDirectoryName == '') {
        	$JSSystemConst = new js_JSSystemConst();
        	$TldDirectoryName = $JSSystemConst->defaultPathToImagesTld;
		}

		$JSUtil = new js_JSUtil();
		
        if ($client->OS != null)
    		$this->addUrlsToOSObject( $JSUtil, $OSDirectoryName, $client->OS );
		if ($client->Browser != null)
			$this->addUrlsToBrowserObject( $JSUtil, $BrowserDirectoryName, $client->Browser );
        if ($location->Tld != null)
		    $this->addUrlsToTldObject( $JSUtil, $TldDirectoryName, $location->Tld );
        //todo
		//$Visitor_result = $Visitor;

		return true;
	}
	
	
	/** @private
	 *
	 *  @param $resolution          values - one of: 'day', 'month', 'year'
	 *  @param $arr_obj             contain result from MySql. If in DB were no value for particular day, value for 
	 *                                  that day does not exist in SQL result! This function fills that missing values
	 *
	 *  NOTICE:
	 *     This function is very hard to implemantaion due to many aspects (time, infinite loops, missing end values, 
	 *     leap year, various number of days in months etc.)
	 *     It coulud be optimized a little, but it is very hard to do it!    Think twice before You change anything!
	 *
	 *  TESTS:
	 *     It seams that caling this function 5 times get 0.05[s] - it is very long.
	 */
	function fillMissingDataForResolution( $resolution, $timestamp_from, $timestamp_to, $arr_obj, &$arr_obj_result ) {
		$ts_from = $timestamp_from;
		$ts_to = $timestamp_to;
		if ($ts_from > $ts_to)
			return false;
		
		if (!$arr_obj)
			return true;
			
		if (count($arr_obj)==0) {
			$arr_obj_result = $arr_obj;
			return true;
		}

		$arr_tmp = array();
			
		//create indexed temp array (to sorting)
		if ($resolution == 'day') {
			foreach($arr_obj as $obj)
				$arr_tmp[$obj->year.'-'.$obj->month.'-'.$obj->day] = $obj;
		} else if ($resolution == 'month') {
			foreach($arr_obj as $obj)
				$arr_tmp[$obj->year.'-'.$obj->month] = $obj;
		} else {
			//year
			foreach($arr_obj as $obj)
				$arr_tmp[$obj->year] = $obj;
		}
		
		//fill by valuses and missing values (must be done in that way if we want have result sorted)
		$arr_obj_result = array();
		if ($resolution == 'day') {
			$ts = $ts_from;
			$ts_Ymd = mktime(0, 0, 0, date('n', $ts), date('j', $ts), date('Y', $ts));
			$ts_Ymd_to = mktime(0, 0, 0, date('n', $ts_to), date('j', $ts_to), date('Y', $ts_to));
			while ($ts_Ymd <= $ts_Ymd_to) { //I know that code looks strange, but it works. In other way it could make infinite loop or return wrong results!
				$str_Y = date('Y', $ts);
				$str_m = date('n', $ts);
				$str_d = date('j', $ts);
				$str_Ymd = $str_Y.'-'.$str_m.'-'.$str_d;
				if (isset($arr_tmp[$str_Ymd])) {
					$arr_obj_result[$str_Ymd] = $arr_tmp[$str_Ymd];
				} else {
					$obj = new stdClass();
					$obj->nbr_visitors = 0;
					$obj->year  = $str_Y;
					$obj->month = $str_m;
					$obj->day   = $str_d;
					
					$arr_obj_result[$str_Ymd] = $obj;
				}
				$ts = strtotime('+1 day', $ts);
				$ts_Ymd = mktime(0, 0, 0, date('n', $ts), date('j', $ts), date('Y', $ts));
			}
		} else if ($resolution == 'month') {
			$ts = $ts_from;
			$ts_Ym = mktime(0, 0, 0, date('n', $ts), 1, date('Y', $ts));
			$ts_Ym_to = mktime(0, 0, 0, date('n', $ts_to), 1, date('Y', $ts_to));
			while ($ts_Ym<=$ts_Ym_to) { //I know that code looks strange, but it works. In other way it could make infinite loop or return wrong results!
				$str_Y = date('Y', $ts);
				$str_m = date('n', $ts);
				$str_Ym = $str_Y.'-'.$str_m;
				if (isset($arr_tmp[$str_Ym])) {
					$arr_obj_result[$str_Ym] = $arr_tmp[$str_Ym];
				} else {
					$obj = new stdClass();
					$obj->nbr_visitors = 0;
					$obj->year  = $str_Y;
					$obj->month = $str_m;
					
					$arr_obj_result[$str_Ym] = $obj;
				}
				$ts = strtotime('+1 month', $ts);
				$ts_Ym = mktime(0, 0, 0, date('n', $ts), 1, date('Y', $ts));
			}
		} else {
			//year
			$ts = $ts_from;
			$ts_Y = mktime(0, 0, 0, 1, 1, date('Y', $ts));
			$ts_Y_to = mktime(0, 0, 0, 1, 1, date('Y', $ts_to));
			while ($ts_Y <= $ts_Y_to) { //I know that code looks strange, but it works. In other way it could make infinite loop or return wrong results!
				$str_Y = date('Y', $ts);
				if (isset($arr_tmp[$str_Y])) {
					$arr_obj_result[$str_Y] = $arr_tmp[$str_Y];
				} else {
					$obj = new stdClass();
					$obj->nbr_visitors = 0;
					$obj->year  = $str_Y;
					
					$arr_obj_result[$str_Y] = $obj;
				}
				$ts = strtotime('+1 year', $ts);
				$ts_Y = mktime(0, 0, 0, 1, 1, date('Y', $ts));
			}
		}
		
		return true;
	}
}