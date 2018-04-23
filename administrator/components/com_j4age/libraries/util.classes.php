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

require_once( dirname(__FILE__) .DS. 'base.classes.php' );// mic: not again !!! //Yes, again!!. It not working without it!



/**
 * This class contain utility methods that are used by many JS parts of code.
 * Utility methods are more complex than base methods.
 *
 * Maybe this class should be divided to 2 classes. 1-class with access to database, 2-that operate on texts, colors etc.
 */
class js_JSUtil
{

	/**
	 * @static Because of PHP 4.0 this is not explicit defined as static
     *
     * Formats a given integer - here used to format the dabase size
	 * mic: reworked since 2.3.x
	 *
	 * @return string
	 */
	function getJSDatabaseSizeHtmlCode() {
		require_once( dirname( __FILE__ ) .DS. '..'.DS. 'database' .DS. 'select.one.value.php' );
		$JSDbSOV = new js_JSDbSOV();//we create object to not rise PHP notice
		$JSDatabaseSize = 0;
		$JSDbSOV->getJSDatabaseSize($JSDatabaseSize);
		
		$color = 'green';
		if( ( $JSDatabaseSize > '10485760' ) && ( $JSDatabaseSize <= '31457280' ) ) {
			$color = 'blue';
		}
		if( $JSDatabaseSize > '31457280' ) {
			$color = 'red';
		}

		return '<span style="color:' . $color . '">' . round( ( ( $JSDatabaseSize / 1024 ) / 1024 ), 2 ) . '</span>';
	}

	
	/**
	 * Optimize all JS tables
	 *
	 * @return bool - true on success
	 */
	function optimizeAllJSTables() {
		$bResult = true;
		
		$JSSystemConst = new js_JSSystemConst();
		
		require_once( dirname( __FILE__ ) .DS. '..'.DS. 'database' .DS. 'select.one.value.php' );
		$JSDbSOV = new js_JSDbSOV();//we create object to not rise PHP notice
		
		foreach( $JSSystemConst->allJSDatabaseTables as $db_table_name) {
			$bResult &= $JSDbSOV->optimizeTable($db_table_name);
		}
		
		return $bResult;
	}

    	/**
	 * Optimize all JS tables
	 *
	 * @return bool - true on success
	 */
	function dropData($tablename, $datecolumn, $days)
    {

        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
        $now = time();
        $timestamp = $now - (60 * 60 * 24 * $days);
        $query = "DELETE FROM $tablename WHERE $datecolumn < $timestamp;";

        $JSDatabaseAccess->db->setQuery( $query );
        if( !$JSDatabaseAccess->db->query() ) {
            return $JSDatabaseAccess->db->getErrorMsg();
        }

		return true;
	}

    /**
	 * Optimize all JS tables
	 *
	 * @return bool - true on success
	 */
	function releaseUnusedData($targettable, $usedByTable, $linkTarget, $linkUsedBy)
    {

        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
        $query = "DELETE FROM $targettable USING $targettable LEFT OUTER JOIN $usedByTable ON $targettable.$linkTarget = $usedByTable.$linkUsedBy WHERE $usedByTable.$linkUsedBy IS NULL ;";

        $JSDatabaseAccess->db->setQuery( $query );
        if( !$JSDatabaseAccess->db->query() ) {
            return $JSDatabaseAccess->db->getErrorMsg();
        }

		return true;
	}

	function getUrlToImages() {
		if( defined( '_JS_STAND_ALONE' ) )
			return '';
		else
			return str_replace( 'administrator/', '', JURI::base() ) . 'components/com_j4age/images/';
	}

	/** 
	 *  $image_name      eg. 'explorer' without extension, mainly value from column 'sys_img' or 'browser_img'
	 *  $directory_name  eg. 'browser-png-16x16-1'
	 *  return path that could be used in <img src="... tag
	 *
	 *  Example:
	 *    getImageWithUrl('explorer', 'browser-png-16x16-1') -> '/components/com_j4age/images/browser-png-16x16-1/explorer.png'
	 */
	function getImageWithUrl($image_name, $directory_name) {
        $parts = explode('-', $directory_name);
        
        return js_JSUtil::getUrlToImages().$directory_name.'/'.$image_name.'.'.$parts[1];//$parts[1] is images_extension
	}
	
	
	/**
	 *  Compare JS versions.
	 *  It is higly recomended to use this function as is it shown in examples.
	 *
	 *  NOTICE: This function return bool! 
	 *
	 *  results (examples):
	 *  JSVersionCompare('4.0.4.10',  '4.0.4.11', '<')  -> true
	 *  JSVersionCompare('4.0.5.10',  '4.0.4.11', '<')  -> false
	 *  
	 *  JSVersionCompare('2.2.3',     '2.2.3.113', '<') -> true - that is reason that we always should use 4 sections JS version numeration
	 *  JSVersionCompare('2.2.0.83',  '2.2.3.113', '<') -> true
	 *  JSVersionCompare('2.3.2.176', '2.2.3.113', '<') -> false
	 *  JSVersionCompare('2.2.2.168', '2.2.3.113', '<') -> true
	 *  JSVersionCompare('',          '2.2.3.113', '<') -> true
	 *
	 *  JSVersionCompare('2.2.3.113',     '2.2.3.113',     '<') -> false
	 *  JSVersionCompare('2.2.3.113 dev', '2.2.3.113',     '<') -> true
	 *  JSVersionCompare('2.2.3.113',     '2.2.3.113 dev', '<') -> false
	 *  JSVersionCompare('2.3.0.216 dev', '2.3.0.217',     '<') -> true
	 *  JSVersionCompare('2.3.0.216 dev', '2.3.0.215',     '<') -> false
	 */	
	function JSVersionCompare( $JSversion1, $JSversion2, $operator ) {
		if (version_compare( $JSversion1, $JSversion2, $operator ))
			return true;
		else
			return false;
	}

    /**
     * @param  $period string
     * @param  $fromDate based on GMT time
     * @param  $toDate based on GMT time
     * @param  $d
     * @param  $m
     * @param  $y
     * @param  $step
     * @return void
     */
    function resolvePeriodIndicator($period, &$fromDate, &$toDate, $d = null, $m = null, $y = null, $step = null)
    {
        //get the GMT Time
        $gmt_timestamp = js_getJSNowTimeStamp();
        $timezone = js_getJSTimeZone();
        $tz_offset = $timezone * 3600; //get the seconds for the hour offset

        //Get the timestamp of the current timezone
        //$timestamp = $gmt_timestamp + $tz_offset;

		if (substr($period, 0, 15) == 'for_last_d-h-m_') {
			$xml_day_minute_str = substr($period, 15);
			$day_minute_arr = explode('-', $xml_day_minute_str);

			$nbr_of_days = (int)$day_minute_arr[0];
			$nbr_of_hours = (int)$day_minute_arr[1];
			$nbr_of_minutes = (int)$day_minute_arr[2];

            $period_in_seconds = 0;
            $period_in_seconds += $nbr_of_days * 86400;
            $period_in_seconds += $nbr_of_hours * 3600;
            $period_in_seconds += $nbr_of_minutes * 60;

            $toDate = $gmt_timestamp;
            $fromDate = $toDate - $period_in_seconds;

            //Set values based on local timezone
            $d = gmdate( 'j', $fromDate );
            $m = gmdate( 'n', $fromDate );
            $y = gmdate( 'Y', $fromDate );

            if($period_in_seconds < 31*86400)
            {
                $step = 'day';
            }
            else
            {
                $step = 'month';
            }
		}
        else
        {
            $period_in_seconds = 0;
            switch($period)
            {
                case "today"  :
                    {

                        //Time is stored in DB as GMT
                        $fromDate = mktime(0,0,0) + $tz_offset;
                        $toDate = mktime(23,59,59) + $tz_offset;

                        //Set values based on local timezone
                        $d = gmdate( 'j', $fromDate );
                        $m = gmdate( 'n', $fromDate );
                        $y = gmdate( 'Y', $fromDate );

                        $step = 'day' ;
                        break;
                    }
                case "7-days"  :
                    {
                        $period_in_seconds = 7*86400;
                        $toDate = mktime(23,59,59) + $tz_offset;
                        $fromDate = $toDate - $period_in_seconds;

                        //Set values based on local timezone
                        $d = 'all';
                        $m = gmdate( 'n', $fromDate );
                        $y = gmdate( 'Y', $fromDate );
                        $step = 'day' ;
                        break;
                    }
                case "14-days"  :
                    {
                        $period_in_seconds = 14*86400;
                        $toDate = mktime(23,59,59) + $tz_offset;
                        $fromDate = $toDate - $period_in_seconds;

                        //Set values based on local timezone
                        $d = 'all';
                        $m = gmdate( 'n', $fromDate );
                        $y = gmdate( 'Y', $fromDate );
                        $step = 'day' ;
                        break;
                    }
                case "28-days"  :
                    {
                        $period_in_seconds = 28*86400;
                        $toDate = mktime(23,59,59) + $tz_offset;
                        $fromDate = $toDate - $period_in_seconds;

                        //Set values based on local timezone
                        $d = 'all';
                        $m = gmdate( 'n', $fromDate );
                        $y = gmdate( 'Y', $fromDate );
                        $step = 'day' ;
                        break;
                    }
                case 'last_92_days':
                case '92-days':
                    {
                        $period_in_seconds = 92*86400;
                        $toDate = mktime(23,59,59) + $tz_offset;
                        $fromDate = $toDate - $period_in_seconds;

                        //Set values based on local timezone
                        $d = 'all';
                        $m = 'all';
                        $y = gmdate( 'Y', $fromDate );
                        $step = 'month' ;
                        break;
                    }
                case "365-days"  :
                    {
                        $period_in_seconds = 365*86400;
                        $toDate = mktime(23,59,59) + $tz_offset;
                        $fromDate = $toDate - $period_in_seconds;

                        //Set values based on local timezone
                        $d = 'all';
                        $m = 'all';
                        $y = gmdate( 'Y', $fromDate );
                        $step = 'month' ;
                        break;
                    }
                case 'this_week'  :
                case "this-week"  :
                    {
                        //We calculate the date of the monday of this week
                        $todaysWeekday = date('w', mktime(0,0,0));
                        $weekdayOffset = 0;
                        if($todaysWeekday > 0)
                        {
                            $weekdayOffset = $todaysWeekday-1; //to get the monday
                        }

                        $fromDate = strtotime("-$weekdayOffset day", mktime(0,0,0) + $tz_offset );
                        $toDate = mktime(23,59,59) + $tz_offset;
                        //Set values based on local timezone
                        $d = 'all';
                        $m = gmdate( 'n', $fromDate );
                        $y = gmdate( 'Y', $fromDate );
                        $step = 'day' ;
                        break;
                    }
                case "last-week"  :
                {
                        //We calculate the date of the monday of last week

                        $todaysWeekday = date('w', mktime(23,59,59));
                        $weekdayOffset = 0;
                        if($todaysWeekday > 0)
                        {
                            $weekdayOffset = ($todaysWeekday-1); //to get the monday
                        }

                        $toDate = strtotime("-$weekdayOffset day", mktime(23,59,59)) + $tz_offset;

                        //$weekdayOffset = $weekdayOffset + 7;

                        $fromDate = strtotime("-7 day", $toDate);
                        //$toDate = strtotime("+7 day", $fromDate);

                        //Values might be used somewhere to indicate the requested month or year, so we set them
                        $d = 'all';
                        $m = gmdate( 'n', $fromDate );
                        $y = gmdate( 'Y', $fromDate );
                        $step = 'day' ;
                        break;
                    }
                case 'this_month'  :
                case "this-month"  :
                    {
                        $fromDate = mktime(0,0,0,gmdate( 'n', $gmt_timestamp ),1,gmdate( 'Y', $gmt_timestamp )) + $tz_offset;
                        $toDate = js_getJSNowTimeStamp();

                        $d = 'all';
                        $m = gmdate( 'n', $fromDate );
                        $y = gmdate( 'Y', $fromDate );

                        $step = 'day' ;
                        break;
                    }
                case 'this_year'  :
                case "this-year"  :
                    {
                        $fromDate = mktime(0,0,0,1,1,gmdate( 'Y', $gmt_timestamp )) + $tz_offset;
                        $toDate = mktime(23, 59, 59) + $tz_offset;

                        $d = 'all';
                        $m = 'all';
                        $y = gmdate( 'Y', $fromDate );

                        $step = 'month' ;
                        break;
                    }
                case "last-2-years"  :
                    {
                        $fromDate = mktime(0,0,0, 1, 1, date("Y") -1 )+ $tz_offset;
                        $toDate = mktime(23, 59, 59) + $tz_offset;

                        $d = 'all';
                        $m = 'all';
                        $y = gmdate( 'Y', $fromDate );

                        $step = 'month' ;
                        break;
                    }
                default :
                    {
                        $fromDate = mktime(0,0,0) + $tz_offset;
                        $toDate = mktime(23,59,59) + $tz_offset;

                        $d = gmdate( 'j', $fromDate );
                        $m = gmdate( 'n', $fromDate );
                        $y = gmdate( 'Y', $fromDate );

                        $step = 'day';
                        break;
                    }
            }
        }
    }

    function renderImg($image, $directory, $placeholder = '', $forceShowIcon = false)
    {
        if($image == '01') return '';
        if(!$forceShowIcon)
        {
            $JSConf = js_JSConf::getInstance();
            if($JSConf->show_icons == false) return $placeholder;
        }
        $url =  js_JSUtil::getImageWithUrl($image, $directory);
        return '<img src="'.$url.'" border="0" />';
    }
}

class js_Cache
{
    function getCachedResult( $key, $query,  $domain = 'j4age' )
    {
        if($domain == null)
        {
            $domain = 'j4age';
        }
        
        $JSDatabaseAccess =& js_JSDatabaseAccess::getInstance();

        $cacheQuery = '';
        /**
         * Use the query as key, if no keyword specified
         */
        if(empty($key))
        {
            $cacheQuery = "SELECT value, timestamp, ttl FROM #__jstats_cache WHERE `domain` = '$domain' AND `query` = ".$JSDatabaseAccess->db->Quote( $query);
        }
        else
        {
            $cacheQuery = "SELECT value, timestamp, ttl FROM #__jstats_cache WHERE `domain` = '$domain' AND `key` = ".$JSDatabaseAccess->db->Quote( $key);
        }
         //perform to retrieve cache

        $JSDatabaseAccess->db->setQuery( $cacheQuery );
        $result = $JSDatabaseAccess->db->loadObject();

        if ($JSDatabaseAccess->db->getErrorNum() > 0)
        {
            js_echoJSDebugInfo("".$JSDatabaseAccess->db->getErrorMsg(), '');
        }
        $now = js_getJSNowTimeStamp();
        if(!empty($result))
        {
          $timeout = $result->timestamp + $result->ttl;
          if($result->ttl > 0 && $timeout < $now)
          {
              js_echoJSDebugInfo('Delete all old cache entries');

             //delete row as it is too old
             /**
              * We delete all old entries, this would automatically also clean-up forgotten trash
              */
              $deleteQuery = "DELETE FROM #__jstats_cache WHERE ttl > 0 AND (timestamp + ttl) < $now";

              $JSDatabaseAccess->db->setQuery( $deleteQuery );
              if (!$JSDatabaseAccess->db->query())
              {
                js_echoJSDebugInfo("".$JSDatabaseAccess->db->getErrorMsg(), '');
              }
              return null;
          }
          else
          {
              js_echoJSDebugInfo('Retrieve data from cache');
              return $result->value;
          }
        }
        return null;
    }

    function &temporaryCachedQuery( $key, $query, $from = -1, $to = -1, $domain = 'j4age')
    {
        /**
         * 
         */
        $now = js_getJSNowTimeStamp();

        /**
         * 5 minutes
         */
        $ttl = 300;

        /**
         * We cache enquires in the past longer as enquires, which do include the current time
         */
        if($from < $now && $to < ($now - 60) &&  $to > 0)
        {
          //15 minutes
          $ttl = 900;
        }
        return js_Cache::cachedQuery( $key, $query, $from, $to, $ttl,  $domain);
    }

    /**
     * @param  $query
     * @param  $key
     * @param int $ttl
     *          0 = for ever
     *          negative = dynamic based on provided from and to timestamp
     *          positive = time in seconds for how long it is cached
     * @param  $from the start of the time interval used for the query
     * @param  $to  the end of the time interval used for the query
     * @param string $domain
     * @return #Funserialize|#M#P#VJSDatabaseAccess.db.loadObjectList|?
     */
    function &cachedQuery( $key, $query, $from = -1, $to = -1, $ttl = -1,  $domain = 'j4age')
    {
       $JSDatabaseAccess =& js_JSDatabaseAccess::getInstance();

       $result = js_Cache::getCachedResult($key,$query, $domain);
       $resultSet = unserialize($result);

       if($resultSet != null)
       {
          return $resultSet;
       }

       $JSDatabaseAccess->db->setQuery( $query );
       $resultSet = $JSDatabaseAccess->db->loadObjectList();

       if ($JSDatabaseAccess->db->getErrorNum() > 0)
       {
           js_echoJSDebugInfo("".$JSDatabaseAccess->db->getErrorMsg(), '');
           return $resultSet;
       }

       $serializedSet = serialize($resultSet);
       js_Cache::storeInCache($key,$query, $from, $to, $ttl, $domain, $serializedSet, 'array');

       return $resultSet;
    }

    /**
     * @param  $query
     * @param  $key
     * @param int $ttl
     *          0 = for ever
     *          negative = dynamic based on provided from and to timestamp
     *          positive = time in seconds for how long it is cached
     * @param  $from the start of the time interval used for the query
     * @param  $to  the end of the time interval used for the query
     * @param string $domain
     * @return #Funserialize|#M#P#VJSDatabaseAccess.db.loadObjectList|?
     */
    function &cachedObjectQuery( $key, $query, $from = -1, $to = -1, $ttl = -1,  $domain = 'j4age')
    {
       $JSDatabaseAccess =& js_JSDatabaseAccess::getInstance();

       $result = js_Cache::getCachedResult($key,$query, $domain);
       $resultSet = unserialize($result);

       if($resultSet != null)
       {
          return $resultSet;
       }

       $JSDatabaseAccess->db->setQuery( $query );
       $resultSet = $JSDatabaseAccess->db->loadObject();

       if ($JSDatabaseAccess->db->getErrorNum() > 0)
       {
           js_echoJSDebugInfo("".$JSDatabaseAccess->db->getErrorMsg(), '');
           return $resultSet;
       }

       $serializedSet = serialize($resultSet);
       js_Cache::storeInCache($key,$query, $from, $to, $ttl, $domain, $serializedSet, 'object');

       return $resultSet;
    }

    function storeInCache($key, $query, $from = -1, $to = -1, $ttl = -1,  $domain = 'j4age', $cacheValue, $type = 'string')
    {
        if($domain == null)
        {
            $domain = 'j4age';
        }
       $now = js_getJSNowTimeStamp();

       if($ttl < 0)
       {
           /**
            * If everything is in the past, we can cache the result basically forever
            */
          if($from < $now && $to < ($now - 60) &&  $to > 0)
          {
             $ttl = 0;
          }
          else
          {
             //30 minutes
             $ttl = 1800;
          }
       }

        $JSDatabaseAccess =& js_JSDatabaseAccess::getInstance();
        $insertQuery = "INSERT INTO #__jstats_cache (domain, type, `key`, timestamp, ttl, value, query ) VALUES ('$domain', '$type', '$key', $now, $ttl,".$JSDatabaseAccess->db->Quote( $cacheValue).",".$JSDatabaseAccess->db->Quote( $query)." )";
        $JSDatabaseAccess->db->setQuery( $insertQuery );
        if (!$JSDatabaseAccess->db->query())
        {
             js_echoJSDebugInfo("".$JSDatabaseAccess->db->getErrorMsg(), '');
            return false;
        }
        js_echoJSDebugInfo('Store data in cache');
        return true;
    }


}


//support for stand alone version
/*
if (!class_exists('JText')) {

	class JText
	{
		function _($string, $jsSafe = false) {
			return $string;
		}

		function sprintf($string) {
			return $string;//@todo
		}
	}
}
*/
