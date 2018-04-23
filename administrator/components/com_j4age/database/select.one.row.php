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


require_once( dirname( __FILE__ ) .DIRECTORY_SEPARATOR. 'access.php' );
require_once( dirname( __FILE__ ) .DIRECTORY_SEPARATOR. 'db.constants.php' );





/**
 * This class contain database query selects that return one row
 *
 * All methods are static
 * 
 * js_JSDbSOR JoomlaStats Database Select One Row 
 */
class js_JSDbSOR extends js_JSDatabaseAccess
{
	/** constructor initialize database access */
	function __construct() {
		parent::__construct();
	}

	
	function getPagesImpressionsSums( $timestamp_from, $timestamp_to, &$obj_result,$objecttype = -1 ) {
		if ($this->isMySql40orGreater())
			return $this->getPagesImpressionsSums_MySql40( $timestamp_from, $timestamp_to, $obj_result, $objecttype );
		else
			return $this->getPagesImpressionsSums_MySql30( $timestamp_from, $timestamp_to, $obj_result, $objecttype );
	}

	/** probably this query could be optimized for performance (by nested selects) */
	//function getPagesImpressionsSums_MySql40( $day, $month, $year, &$obj_result ) {
	function getPagesImpressionsSums_MySql40( $timestamp_from, $timestamp_to, &$obj_result, $objecttype = -1 ) {

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

		$query = ''
		. ' SELECT'
		. '   COUNT(s.page_id)        AS nbr_visited_pages,'			//return number of visited pages (in time period)
		. '   SUM(s.page_impressions) AS sum_all_pages_impressions,'	//return sum of page impressions of all visited pages (in time period)
		. '   MAX(s.page_impressions) AS max_page_impressions'			//always there is page with the higest visits number - that visit number is returned here
		. ' FROM ('
		. '   SELECT'
		. '     i.page_id, COUNT(*) AS page_impressions'
		. '   FROM '
		. '     #__jstats_impressions i'
		. '     LEFT JOIN #__jstats_visits v ON (v.visit_id=i.visit_id)'//optimized
        . (($objecttype > -1)? 'LEFT JOIN #__jstats_clients c ON (c.client_id=v.client_id)' : '')
		. ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' )
		. '   GROUP BY i.page_id'
		. ' ) AS s';
		$this->db->setQuery( $query );
		$tot = $this->db->loadObjectList();
		if ($this->db->getErrorNum() > 0)
			return false;
		
		$obj_result = $tot[0];
		
		if ($obj_result->nbr_visited_pages == 0) { //set missing data
			$obj_result->sum_all_pages_impressions = 0;
			$obj_result->max_page_impressions = 0;
		}
			
		return true;
	}
	
	//function getPagesImpressionsSums_MySql30( $day, $month, $year, &$obj_result ) {
	function getPagesImpressionsSums_MySql30( $timestamp_from, $timestamp_to, &$obj_result, $objecttype = -1 ) {

        $where = array();
        $where[] =js_JSDatabaseAccess::getConditionStringFromTimestamps($timestamp_from, $timestamp_to);

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
		. "   COUNT(*)   AS page_impressions,"
		. "   i.page_id  AS page_id"//!!
		. " FROM"
		. "   #__jstats_impressions i"
		. '   LEFT JOIN #__jstats_visits v ON (v.visit_id=i.visit_id)'
        . (($objecttype > -1)? 'LEFT JOIN #__jstats_clients c ON (c.client_id=v.client_id)' : '')
		. ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' )
		. " GROUP BY"
		. "   i.page_id";
		$this->db->setQuery( $query );
		$tot = $this->db->loadResultArray();//!!    //loadRowList();//loadResultArray();//loadAssocList();
		if ($this->db->getErrorNum() > 0)
			return false;
		
		$sum = 0;
		foreach ($tot as $t)
			$sum = $sum + $t;
		
		$obj_result = null;
		$obj_result = new stdClass();
		$obj_result->nbr_visited_pages = count($tot);
		$obj_result->sum_all_pages_impressions = $sum;
		$obj_result->max_page_impressions = (count($tot)>0) ? max($tot) : 0;//prevent warning
		
		unset($tot);//free large part of memory
		return true;
	}
	
}

