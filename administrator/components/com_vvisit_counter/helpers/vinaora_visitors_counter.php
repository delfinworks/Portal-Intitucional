<?php
/**
* @version		$Id: vinaora_visitors_counter.php 2009-12-05 vinaora $
* @package		VINAORA VISITORS COUNTER
* @copyright	Copyright (C) 2007 - 2010 VINAORA. All rights reserved.
* @license		GNU/GPL
* @website		http://vinaora.com
* @email		admin@vinaora.com
* 
* @warning		DON'T EDIT OR DELETE LINK HTTP://VINAORA.COM ON THE FOOTER OF MODULE. PLEASE CONTACT ME IF YOU WANT.
*
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<?php

// Birth day of Joomla: 15 Sept 2005
define( 'BIRTH_DAY_JOOMLA' , 1126742400 );
require_once( dirname(__FILE__).DS.'upgrade.php' );
require_once( dirname(__FILE__).DS.'datetime.php' );

class extVinaoraVisitorsCounter
{
	/*
	** Create Table #__vvcounter_logs
	*/
	/* ------------------------------------------------------------------------------------------------ */
	function createTable (){
		
		// Check if table exists. When not, create it
		$query	=	" CREATE TABLE IF NOT EXISTS #__vvcounter_logs (
						`time` int unsigned NOT NULL,
						`visits` mediumint unsigned NOT NULL default '0',
						`guests` mediumint unsigned NOT NULL default '0',
						`members` mediumint unsigned NOT NULL default '0',
						`bots` mediumint unsigned NOT NULL default '0',
						UNIQUE KEY (`time`)
					) ENGINE=MyISAM; ";
					
		$db	=& JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
		
		if ( $db->getErrorNum() ) {
			JError::raiseWarning( 500, $db->stderr() );
		}
	}
	/* ------------------------------------------------------------------------------------------------ */
	
	
	
	/*
	** Return Last Time Log
	*/
	/* ------------------------------------------------------------------------------------------------ */
	function lastTimeLog(){
	
		$db 		= 	& JFactory::getDBO();
		$ltime		=	0;
		
		$query		=	' SELECT MAX(time) ' .
						' FROM #__vvcounter_logs ';
	
		$db->setQuery($query);
		
		// Last Time
		$ltime		=	$db->loadResult();
		
		if ( $db->getErrorNum() ) {
			// extVinaoraVisitorsCounter::createTable();
			JError::raiseWarning( 500, $db->stderr() );
		}
		
		return $ltime;
		
	}
	/* ------------------------------------------------------------------------------------------------ */	
	
	
	/*
	** Insert New Log
	*/
	/* ------------------------------------------------------------------------------------------------ */
	function insertLog( $time, $visits_array ){
		
		$db 		=	& JFactory::getDBO();
		
		$time		=	(int) $time;
		$visits		=	(int) $visits_array['visits'];
		$guests		=	(int) $visits_array['guests'];
		$members	=	(int) $visits_array['members'];
		$bots		=	(int) $visits_array['bots'];
		
		$query 		= 	" INSERT INTO #__vvcounter_logs (time, visits, guests, members, bots) ";
		$query		.=	" VALUES ( $time, $visits, $guests, $members, $bots ) ";
		
		$db->setQuery($query);
		$db->query();
		
		if ($db->getErrorNum()) {
			JError::raiseWarning( 500, $db->stderr() );
		}
	}
	/* ------------------------------------------------------------------------------------------------ */	



	/*
	** Check table if exits
	*/
	/* ------------------------------------------------------------------------------------------------ */
	function tableExits( $tablename ){
		
		if ( empty($tablename) ) return false;
		
		$db	=	&JFactory::getDBO();
		$tablename	=	trim($tablename);
		
		// Remove default prefix "#__"
		if ( strpos( $tablename, "#__") !== false ){
			$tablename = substr( $tablename,3 );
		}
		
		// Add prefix. Example: jos_
		$tablename	=	$db->getPrefix().$tablename;
		
		// Check if table #__tablename exists or not
		if ( !in_array($tablename,$db->getTableList()) ) return true;
		
		if ($db->getErrorNum()) {
			JError::raiseWarning( 500, $db->stderr() );
		}
		
		return false;
	}
	/* ------------------------------------------------------------------------------------------------ */		
	
	
	/*
	** Get Number of Visits from $timestart to $timestop
	*/
	/* ------------------------------------------------------------------------------------------------ */
	function &getVisits( $timestart=0, $timestop=0, $duration=0 ){
	
		$timestart		=	(int) $timestart;
		$timestop		=	(int) $timestop;
		
		if ( $timestart || $timestop ){
			return extVinaoraVisitorsCounter::getVisitsFromLogs( $timestart, $timestop );
		}
		else{
			if ( $duration ){
				return extVinaoraVisitorsCounter::getVisitsOnline( $duration );
			}
			else{
				return extVinaoraVisitorsCounter::getToday();
			}
		}
	}
	/* ------------------------------------------------------------------------------------------------ */
	
	
	/*
	** Get Number of Visits of Today
	*/
	/* ------------------------------------------------------------------------------------------------ */
	function &getToday( ){
		$config			=	&JFactory::getConfig();
		$offset			=	(float) $config->getValue('config.offset');	
		$lifetime		=	(int) $config->getValue('config.lifetime')*60;
		
		$now			=	mktime();
		$daystart		=	$now - ( $now % 86400 );
		$local_daystart	=	extVisitCounterDateTime::localTimeStart( $daystart, $offset, "day");
		
		if ( $now-$local_daystart < $lifetime ){
			$visits		=	extVinaoraVisitorsCounter::getVisitsFromSession( $local_daystart );
		}
		else{
			$visitsLogs		=	extVinaoraVisitorsCounter::getVisitsFromLogs( $local_daystart );
			$visitsSession	=	extVinaoraVisitorsCounter::getVisitsFromSession( $visitsLogs['lasttime']+1 );
			$visits			=	extVinaoraVisitorsCounter::array_add( $visitsLogs, $visitsSession);
		}
		return $visits;
	}
	/* ------------------------------------------------------------------------------------------------ */
	

	/*
	** Get Number of Visits Online
	*/
	/* ------------------------------------------------------------------------------------------------ */
	function &getVisitsOnline( $duration=900 ){
		$db			=	&JFactory::getDBO();
		$now		=	mktime();
		$duration	=	(int) $duration;
		$time		=	$now-$duration;
		
		$records	=	null;
		$total		=	array();
		
		$total['visits']	=	0;
		$total['guests']	=	0;
		$total['members']	=	0;
		$total['bots']		=	0;
		$total['lasttime']	=	0;

		$query		=	" SELECT session_id, time, guest, data " .
						" FROM #__session " .
						" WHERE time > $time ";
		
		$db->setQuery($query);
		$sessions = $db->loadObjectList();
		
		if ( $db->getErrorNum() ) {
			JError::raiseWarning( 500, $db->stderr() );
		}
		
		if ( count($sessions) ) {
			$lasttime	=	0;
		    foreach ( $sessions as $session ) {
				
				$lasttime	=	max( $lasttime, (int) $session->time );
				$start		=	extVinaoraVisitorsCounter::getVisitTimerStart( $session->data );
				
				// if member increase member count by 1
				if ( !$session->guest ) {
					$total['members'] ++;
				}
				else{
					if ( extVinaoraVisitorsCounter::isBot( $session->data ) ) {
						$total['bots']	++;
					}
					else{
						$total['guests'] ++;
					}
				}
				$total['visits'] ++;
			}
			$total['lasttime']	=	$lasttime;
		}
		
		return $total;
	}
	/* ------------------------------------------------------------------------------------------------ */
	
	
	
	/*
	** Get Number of Visits In Logs from $timestart to $timestop
	*/
	/* ------------------------------------------------------------------------------------------------ */
	function &getVisitsFromLogs( $timestart = 0, $timestop = 0 ){
	
		$db			=	&JFactory::getDBO();
		
		$timestart	=	(int) $timestart;
		$timestop	=	(int) $timestop;
		$records	=	null;
		
		$total				=	array();
		
		$total['visits']	=	0;
		$total['guests']	=	0;
		$total['members']	=	0;
		$total['bots']		=	0;
		$total['lasttime']	=	0;

		if ( $timestart < BIRTH_DAY_JOOMLA ) $timestart = 0;
		if ( $timestop < BIRTH_DAY_JOOMLA ) $timestop = 0;

		$query		=	" SELECT time, visits, guests, members, bots " .
						" FROM #__vvcounter_logs " .
						" WHERE '1=1' ";
		
		if ( !$timestart ){
			if ( $timestop ) {
				$query	.=	" AND time <= $timestop ";
			}
		}
		else{
			if ( !$timestop ){
				$query		.= " AND time > $timestart ";
			}
			else{
				if ( $timestop == $timestart ){
					return $total;
				}
				else{
					$query		.= " AND time > $timestart ";
					$query		.= " AND time <= $timestop ";						
				}
			}
		}
		
		$db->setQuery($query);
		$records = $db->loadObjectList();
		
		if ( $db->getErrorNum() ) {
			JError::raiseWarning( 500, $db->stderr() );
		}
		
		if ( count($records) ) {
			$lasttime	=	0;
			foreach ( $records as $record ) {
				$lasttime			=	max( $lasttime, (int) $record->time );
				$total['visits']	+=	(int) $record->visits;
				$total['guests']	+=	(int) $record->guests;
				$total['members']	+=	(int) $record->members;
				$total['bots']		+=	(int) $record->bots;
			}
			$total['lasttime']		=	$lasttime;
		}
		
		return $total;
	}
	/* ------------------------------------------------------------------------------------------------ */
	
	
	
	/*
	** Get Number of Visits in Joomla's Session from $timestart to $timestop
	*/
	/* ------------------------------------------------------------------------------------------------ */
	function &getVisitsFromSession( $timestart = 0, $timestop = 0 ){	
	
		$db			=	&JFactory::getDBO();
		$now		=	mktime();
		
		$timestart	=	(int) $timestart;
		$timestop	=	(int) $timestop;
		
		$records	=	null;
		$total		=	array();
		
		$total['visits']	=	0;
		$total['guests']	=	0;
		$total['members']	=	0;
		$total['bots']		=	0;
		$total['lasttime']	=	0;
		
		if ( $timestart < BIRTH_DAY_JOOMLA ) $timestart = 0;
		if ( $timestop < BIRTH_DAY_JOOMLA ) $timestop = 0;

		$query		=	" SELECT session_id, time, guest, data " .
						" FROM #__session " .
						" WHERE '1=1' ";
		
		if ( !$timestart ){
			if ( $timestop ) {
				$query	.=	" AND time < $timestop ";
			}
		}
		else{
			if ( !$timestop ){
				$query		.= " AND time >= $timestart ";
			}
			else{
				if ( $timestop == $timestart ){
					$query		.= " AND time = $timestart ";
				}
				else{
					$query		.= " AND time >= $timestart ";
					$query		.= " AND time < $timestop ";		
				}
			}
		}
		
		$db->setQuery($query);
		$sessions = $db->loadObjectList();
		
		if ( $db->getErrorNum() ) {
			JError::raiseWarning( 500, $db->stderr() );
		}
		
		if ( count($sessions) ) {
			$lasttime	=	0;
			
		    foreach ( $sessions as $session ) {
				
				$lasttime			=	max( $lasttime, (int) $session->time );
				$start				=	extVinaoraVisitorsCounter::getVisitTimerStart( $session->data );
				
				// Count if new visit
				if ( ( !$start ) || ( $start > $timestart ) ){
					// if member increase member count by 1
					if ( !$session->guest ) {
						$total['members'] ++;
					}
					else{
						if ( extVinaoraVisitorsCounter::isBot( $session->data ) ) {
							$total['bots']	++;
						}
						else{
							$total['guests'] ++;
						}
					}
					$total['visits'] ++;
				}
			}
			$total['lasttime']	=	$lasttime;
		}
		
		return $total;
	}
	/* ------------------------------------------------------------------------------------------------ */

	
	
	
	/* ------------------------------------------------------------------------------------------------ */
	function isBot( $session ){
		
		if ( empty($session) ) return NULL;
		
		$bots	=	array("google.com","yahoo.com","msn.com","ask.com","cuil.com","baidu.com","Yandex");
		foreach ( $bots as $bot ){
			if ( strpos( $session,$bot ) ) return $bot;
		}

		return NULL;
	}
	/* ------------------------------------------------------------------------------------------------ */
	
	
	
	/* ------------------------------------------------------------------------------------------------ */
	function isNewVisit( $data, $time ){
		$search	=	'"session.timer.start";i:'.$time;
		if ( !strpos( $data,$search ) ) return true;
		else return false;
	}
	/* ------------------------------------------------------------------------------------------------ */
	
	
	
	/* ------------------------------------------------------------------------------------------------ */
	function getVisitTimerStart( $data ){
		// '..."session.timer.start";i:1251988522;s:18:"session.timer.last";i:1251988522;s:17:"session.timer.now";i:1251988522;..."
		
		$start	=	strpos( $data,"session.timer.start" );
		$time	=	0;
		if ( $start ) $time	= (int) substr( $data,$start+23,10 );
		return $time;
		
	}
	/* ------------------------------------------------------------------------------------------------ */
	
	
	
	/* ------------------------------------------------------------------------------------------------ */
	function &getVisitTimer( $data ){
		// '..."session.timer.start";i:1251988522;s:18:"session.timer.last";i:1251988522;s:17:"session.timer.now";i:1251988522;..."
		
		// $start = FALSE if not found.
		$start		=	strpos( $data,"session.timer.start" );
		$last		=	strpos( $data,"session.timer.last" );
		$now		=	strpos( $data,"session.timer.now" );
		
		$time			=	array();
		$time['start']	=	0;
		$time['last']	=	0;
		$time['now']	=	0;
		
		if ( $start || $last || $now ) {
			$time['start']	=	(int) substr( $data,$start+23,10 );
			$time['last']	=	(int) substr( $data,$last+22,10 );
			$time['now']	=	(int) substr( $data,$now+21,10 );
		}
		
		return $time;
		
	}
	/* ------------------------------------------------------------------------------------------------ */
	
	
	
	/* ------------------------------------------------------------------------------------------------ */
	function array_add($a1, $a2) {
		if ( count($a1) && count($a2) ){
			$a1['visits']	+=	$a2['visits'];
			$a1['guests']	+=	$a2['guests'];
			$a1['members']	+=	$a2['members'];
			$a1['bots']		+=	$a2['bots'];
			$a1['lasttime']	=	max($a1['lasttime'], $a2['lasttime']);
		}
		return $a1;
	}
	/*
		// adds the values at identical keys together
		$aRes = $a1;
		foreach (array_slice(func_get_args(), 1) as $aRay) {
			foreach (array_intersect_key($aRay, $aRes) as $key => $val) $aRes[$key] += $val;
			$aRes += $aRay;
		}
		return $aRes;
	*/
	/* ------------------------------------------------------------------------------------------------ */
	
	
	
	/*
	** Move old data from #__vvisitcounter to #__vvcounter_logs
	*/
	/* ------------------------------------------------------------------------------------------------ */
	function migrateTable ( $now='' ){
	
		$now = ( empty($now) )? mktime():((int) $now);
		
		$visits_array	=	array("visits"=>0,"guests"=>0,"members"=>0,"bots"=>0);
		
		$config			=	&JFactory::getConfig();
		$offset			=	(float) $config->getValue('config.offset');
		$issunday		=	true;
		
		$datetime		=	&extVisitCounterDateTime::getTimeStart( $offset, $issunday, $now );

		$daystart				=	$datetime["daystart"];
		$local_daystart			=	$datetime["local_daystart"];
		$yesterdaystart			=	$datetime["yesterdaystart"];
		$local_yesterdaystart	=	$datetime["local_yesterdaystart"];
		$weekstart				=	$datetime["weekstart"];
		$local_weekstart		=	$datetime["local_weekstart"];
		$lweekstart				=	$datetime["lweekstart"];
		$local_lweekstart		=	$datetime["local_lweekstart"];
		$monthstart				=	$datetime["monthstart"];
		$local_monthstart		=	$datetime["local_monthstart"];
		$lmonthstart			=	$datetime["lmonthstart"];
		$local_lmonthstart		=	$datetime["local_lmonthstart"];
		
		asort($datetime);
		
		$visits_array['visits']	=	extVisitCounterUpgrade::getVisits( 0,current($datetime) );
		extVinaoraVisitorsCounter::insertLog( current($datetime),$visits_array );
		
		while ( $cur_time = current($datetime) ) {
			$next_time = next($datetime);
			if ( ($cur_time != $next_time) && (!empty($next_time)) ) {
				$visits_array['visits']	=	extVisitCounterUpgrade::getVisits( $cur_time,$next_time );
				extVinaoraVisitorsCounter::insertLog( $next_time,$visits_array );				
			}
		}
		
		$visits_array['visits']	=	extVisitCounterUpgrade::getVisits( end($datetime),0 );
		extVinaoraVisitorsCounter::insertLog( $now-1,$visits_array );

	}
	/* ------------------------------------------------------------------------------------------------ */
}
?>