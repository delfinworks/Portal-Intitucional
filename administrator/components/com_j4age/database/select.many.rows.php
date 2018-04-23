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


require_once( dirname(__FILE__) .DIRECTORY_SEPARATOR. 'access.php' );
require_once( dirname(__FILE__) .DIRECTORY_SEPARATOR. 'db.constants.php' );





/**
 * This class contain database query selects that return many rows
 *
 * All methods are static
 * 
 * js_JSDbSMR JoomlaStats Database Select Many Row 
 */
class js_JSDbSMR extends js_JSDatabaseAccess
{
	/** constructor initialize database access */
	function __construct() {
		parent::__construct();
	}

		
	/**       MAIN FUNCTION TO SELECT NUMBER OF VISITORS WITH RESOLUTION- ALL KINDS
	 *            ALL JS SHOULD GET DATA THROUGH THIS FUNCTION
	 *                     (directly or indirectly)
	 *
	 *
	 * Gets number of visitors
	 *
	 * @todo I think query could be optimized (should be gain) - could be done only in MySql40
	 *
	 * @param $resolution          values - one of: 'day', 'month', 'year'
	 * @param $visitors_type       values - one of: ''; _JS_DB_IPADD__TYPE_NOT_IDENTIFIED_VISITOR; _JS_DB_IPADD__TYPE_REGULAR_VISITOR; _JS_DB_IPADD__TYPE_BOT_VISITOR
	 * @param $include_summarized  values: true; false; //get this value from $JSConf->include_summarized; ($JSConf = new js_JSConf();)
	 * @param $buid				   required only if $include_summarized = false //get this value from $JSDbSOV->getBuid($buid); ($JSDbSOV = js_JSDbSOV();)
	 * @param $date                formats: ''; '2009-03-25'; '2009-3-9'; '2009-03-25 16:42:56' (NOT RECOMENDED); //use '' to omit time limitation 
	 * @param $arr_obj_result      Result. Array of objects soreted ascending
	 *
	 * NOTICE:
	 *   Faster version of this function is selectNumberOfVisitorsForYMD() (we should check it in MySQL 5.x)
	 *
	 * @param out integer
	 * @return true on success
	 */
	function selectNumberOfVisitorsWithResolution( $resolution, $visitors_type, $buid, $timestamp_from, $timestamp_to, &$arr_obj_result, $cacheKey = null ) {

		$res_q = '';
		$res_g = '';
		if ($resolution == 'day') {
			$res_q = ' DAYOFMONTH(FROM_UNIXTIME(v.changed_at)) AS day, MONTH(FROM_UNIXTIME(v.changed_at)) AS month, YEAR(FROM_UNIXTIME(v.changed_at)) AS year';
			$res_g = ' DAYOFMONTH(FROM_UNIXTIME(v.changed_at)), MONTH(FROM_UNIXTIME(v.changed_at)), YEAR(FROM_UNIXTIME(v.changed_at))';
		} else if ($resolution == 'month') {
			$res_q = ' MONTH(FROM_UNIXTIME(v.changed_at)) AS month, YEAR(FROM_UNIXTIME(v.changed_at)) AS year';
			$res_g = ' MONTH(FROM_UNIXTIME(v.changed_at)), YEAR(FROM_UNIXTIME(v.changed_at))';
		} else {
			//year
			$res_q = ' YEAR(FROM_UNIXTIME(v.changed_at)) AS year';
			$res_g = ' YEAR(FROM_UNIXTIME(v.changed_at))';
		}

		$query = ''
		. ' SELECT'
        . '   SQL_BIG_RESULT COUNT(*) AS nbr_visitors, ' //@todo we need SQL_BIG_RESULT? If Yes, other db queries should be fixed
        . '   v.changed_at, '
		.     $res_q
		. ' FROM'
		. '   #__jstats_visits AS v'
        . '   LEFT JOIN #__jstats_clients c ON (c.client_id=v.client_id)'
		. ' WHERE'
		. '   '.( ($visitors_type!=='') ? 'c.client_type='.$visitors_type : '1=1' )
		. '   '.( (!empty($buid)) ? 'AND v.visit_id >= '.$buid : '')
		. '   AND '.js_JSDatabaseAccess::getConditionStringFromTimestamps($timestamp_from, $timestamp_to)
		. ' GROUP BY'
		.     $res_g
		. ' ORDER BY v.changed_at'
		//.     $res_g
 		;

        $arr_obj_result = js_Cache::cachedQuery( $cacheKey,$query, $timestamp_from, $timestamp_to);
		/*$this->db->setQuery( $query );
		$arr_obj_result = $this->db->loadObjectList(); */
		if ($this->db->getErrorNum() > 0)
			return false;
		
		return true;
	}


	function getPagesImpressionsArr( $limitstart, $limit, $timestamp_from, $timestamp_to, &$arr_obj_result, $objecttype = -1  ) {
        if (js_JSDbSMR::isMySql40orGreater())
			return js_JSDbSMR::getPagesImpressionsArr_MySql40($limitstart, $limit, $timestamp_from, $timestamp_to, $arr_obj_result, $objecttype);
		else
			return js_JSDbSMR::getPagesImpressionsArr_MySql30($limitstart, $limit, $timestamp_from, $timestamp_to, $arr_obj_result, $objecttype);
	}

	/** 
	 * probably this query could be optimized for performance (by nested selects) 
	 *
	 * DO NOT CHANGE ANYTHING!!! - this query is 5x faster in compare if we use JOIN syntax! (MySql fault)
	 */
	function getPagesImpressionsArr_MySql40( $limitstart, $limit, $timestamp_from, $timestamp_to, &$arr_obj_result, $objecttype = -1  ) {

        $where = array();
        $where[] = js_JSDatabaseAccess::getConditionStringFromTimestamps($timestamp_from, $timestamp_to);

        if($objecttype > -1)
        {
            //only display specific clients
            if($objecttype == 3)
            {
                $objecttype = _JS_DB_IPADD__TYPE_BOT_VISITOR;
                $where[] = "c.client_type = $objecttype and c.browser_id = 1024";
            }
            else
            {
                $where[] = "c.client_type = $objecttype";
            }
        }
        $query = ""
		. " SELECT"
		. "   f.page_id          AS page_id,"
		. "   f.page             AS page_url,"
		. "   f.page_title       AS page_title,"
		. "   s.page_impressions AS page_impressions"
		. " FROM ("
		. "   SELECT p.page, p.page_id, p.page_title"
		. "   FROM #__jstats_pages p"
		. " ) AS f, ("
		. "   SELECT i.page_id, count(*) AS page_impressions"
		. '   FROM'
		. '     #__jstats_impressions i'
		. '     LEFT JOIN #__jstats_visits v ON (v.visit_id=i.visit_id)'//optimized?
        . (($objecttype > -1)? 'LEFT JOIN #__jstats_clients c ON (c.client_id=v.client_id)' : '')
		. ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' )
		. "   GROUP BY i.page_id"
		. "   ORDER BY page_impressions DESC"
		. "   LIMIT $limitstart, $limit"
		. " ) AS s"
		. " WHERE f.page_id = s.page_id"
		. " ORDER BY page_impressions DESC";
        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
		$JSDatabaseAccess->db->setQuery( $query );//$pagination->limitstart, $pagination->limit" already are inside query
		$arr_obj_result = $JSDatabaseAccess->db->loadObjectList();
		if ($JSDatabaseAccess->db->getErrorNum() > 0)
			return false;
			
		return true;
	}
	
	
	/** See *_MySql40 for details
	 *
	 *  @deprecated It is 5 times slower than *_MySql40 in 'MySql 5.0.51' and 'JS DB 20 [MB]'
	 */
	function getPagesImpressionsArr_MySql30( $limitstart, $limit, $timestamp_from, $timestamp_to, &$arr_obj_result, $objecttype = -1  ) {
		
        $where = array();
        $where[] = js_JSDatabaseAccess::getConditionStringFromTimestamps($timestamp_from, $timestamp_to);

        if($objecttype > -1)
        {
            //only display specific clients
            if($objecttype == 3)
            {
                $objtype = _JS_DB_IPADD__TYPE_BOT_VISITOR;
                $where[] = "c.client_type = $objecttype and c.browser_id = 1024";
            }
            else
            {
                $where[] = "c.client_type = $objecttype";
            }
        }
        
		$query = ""
		. " SELECT"
		. "   p.page_id    AS page_id,"
		. "   p.page       AS page_url,"
		. "   p.page_title AS page_title,"
		. "   COUNT(*)     AS page_impressions"
		. " FROM"
		. "   #__jstats_pages p"
		. "   LEFT JOIN #__jstats_impressions i ON (p.page_id=i.page_id)"
		. '   LEFT JOIN #__jstats_visits v ON (v.visit_id=i.visit_id)'
        . (($objecttype > -1)? 'LEFT JOIN #__jstats_clients c ON (c.client_id=v.client_id)' : '')
        . ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' )
		. " GROUP BY page_id"
		. " ORDER BY page_impressions DESC"//with 'ORDER BY page_impressions DESC' execute takes almost 5s regardles of use 'LIMIT' or not. //without 'ORDER BY' and with 'LIMIT 0, 30' execute takes 0.01s //tests done on 20.0MB JS database
		. " LIMIT $limitstart, $limit";
        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
		$JSDatabaseAccess->db->setQuery( $query );//$pagination->limitstart, $pagination->limit" already are inside query
		$arr_obj_result = $JSDatabaseAccess->db->loadObjectList();
		if ($JSDatabaseAccess->db->getErrorNum() > 0)
			return false;
			
		return true;
	}

	function selectNotIdentifiedVisitorsArr( $limitstart, $limit, $timestamp_from, $timestamp_to, $include_summarized, &$arr_obj_result ) {

		$query = ''
		. ' SELECT'
		. '   a.code as tld,'
        . '   a.code,'
        . '   a.ip_type,'
        . '   a.ip_exclude,'
        . '   c.client_id,'
        . '   c.client_exclude,'
        . '   c.client_type,'
        . '   a.ip_exclude,'
        . '   a.ip,'
        . '   a.nslookup,'
        . '   c.useragent,'
		. '   v.changed_at,'
        . '   i2nc.country'
		. ' FROM'
		. '   #__jstats_clients AS c'
        . '   LEFT OUTER JOIN #__jstats_visits v ON (v.client_id=c.client_id)'
        . '   LEFT OUTER JOIN #__jstats_ipaddresses a ON (a.ip=v.ip)'
        . '   LEFT OUTER JOIN #__ip2nationCountries i2nc ON (i2nc.code=a.code)'
		. ' WHERE'
		. '   c.client_type = '._JS_DB_IPADD__TYPE_NOT_IDENTIFIED_VISITOR
		. '   AND '.js_JSDatabaseAccess::getConditionStringFromTimestamps($timestamp_from, $timestamp_to)
		;

		$query .= ' ORDER BY v.changed_at DESC';
		$this->db->setQuery( $query, $limitstart, $limit );
		$arr_obj_result = $this->db->loadObjectList();
		if ($this->db->getErrorNum() > 0)
			return false;
			
		return true;
	}
	

	
	/** Database should be organized in different way - than this function will be much simpler! */
	function getOperatingSystemVisistsArr( $timestamp_from, $timestamp_to, $include_summarized = false, &$arr_obj_result, $objecttype = -1  ) {

        $where = array();
        $where[] = "c.client_id = v.client_id";
        $where[] = "sy.os_id = c.os_id";

        $where[] = js_JSDatabaseAccess::getConditionStringFromTimestamps($timestamp_from, $timestamp_to);

        if($objecttype > -1)
        {
            //only display specific clients
            if($objecttype == 3)
            {
                $objecttype = _JS_DB_IPADD__TYPE_BOT_VISITOR;
                $where[] = "c.client_type = $objecttype and c.browser_id = 1024";
            }
            else
            {
                $where[] = "c.client_type = $objecttype";
            }
        }

		$query = ""
		. " SELECT"
		. "   sy.os_name AS os_name,"
		. "   count(*) AS os_visits"
		. " FROM"
		. "   #__jstats_clients AS c, "
        . "   #__jstats_visits AS v,"
        . "   #__jstats_systems AS sy"
        . ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' )
		. ' GROUP BY sy.os_name'
		. ' ORDER BY os_visits DESC, c.os_id ASC'
		;
		$this->db->setQuery( $query );
		$db_result = $this->db->loadObjectList();
		if ($this->db->getErrorNum() > 0)
			return false;

		{//this part simulate JOIN (join could not be performed due to wrong database structure)
			$ava_sys = array();
			$res = $this->getAvailableOperatingSystemArr( $ava_sys );
			if ($res == false)
				return false;
				
			$os_name_to_image_arr = array();
			$os_name_to_ostype_id = array();
			$os_name_to_os_id = array();
			foreach($ava_sys as $rowt) {
				$os_name_to_image_arr[$rowt->os_name] = $rowt->os_img;
				$os_name_to_ostype_id[$rowt->os_name] = $rowt->os_type;
				$os_name_to_os_id[$rowt->os_name] = $rowt->os_id;
			}
			$__jstats_ostype = unserialize(_JS_DB_TABLE__OSTYPE);//whole table #__jstats_ostype (with entries)
				
			$arr_obj_result = array();
			foreach($db_result as $obj) {
				$obj->os_img = (isset($os_name_to_image_arr[$obj->os_name])) ? $os_name_to_image_arr[$obj->os_name] : 'unknown';
				$ostype_id = (isset($os_name_to_ostype_id[$obj->os_name])) ? $os_name_to_ostype_id[$obj->os_name] : _JS_DB_OSTYP__ID_UNKNOWN;
				$obj->ostype_img = $__jstats_ostype[$ostype_id]['ostype_img'];
				//$os_id = (isset($os_name_to_os_id[$obj->os_name])) ? $os_name_to_os_id[$obj->os_name] : 0;
				$obj->ostype_name = $__jstats_ostype[$ostype_id]['ostype_name'];
				$arr_obj_result[] = $obj;
			}
		}			
				
		return true;
	}
	
	function getAvailableOperatingSystemArr( &$arr_obj_result ) {
		$query = ''
		. ' SELECT'
		. '   o.os_id        AS os_id,'
		. '   o.os_key       AS os_key,'
		. '   o.os_name      AS os_name,'
		. '   o.os_type      AS os_type,'
		. '   o.os_img       AS os_img'
		. ' FROM'
		. '   #__jstats_systems o'
        . ' ORDER BY'
        . '   o.os_ordering'
		;
		$this->db->setQuery( $query );
		$arr_obj_result = $this->db->loadObjectList();
		if ($this->db->getErrorNum() > 0)
			return false;
			
		return true;
	}
	
	/** the same as getAvailableSystemArr(), but sorted and distinct */
	function getAvailableOperatingSystemArrForHuman( &$arr_obj_result ) {
		$query = ''
		. ' SELECT DISTINCT'
		. '   o.os_name      AS os_name,'
		. '   o.os_type      AS os_type,'
		. '   o.os_img       AS os_img'
		. ' FROM'
		. '   #__jstats_systems o'
		. ' WHERE'
		. '   o.os_id > 0'
		. ' ORDER BY'
		. '   os_name ASC'
		;
		$this->db->setQuery( $query );
		$arr_obj_result = $this->db->loadObjectList();
		if ($this->db->getErrorNum() > 0)
			return false;
			
		return true;
	}
}

