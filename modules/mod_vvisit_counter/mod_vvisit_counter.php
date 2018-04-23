<?php
/**
* @version		$Id: mod_vvisit_counter.php 2009-12-05 vinaora $
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

$vvisit_path	=	JPATH_ADMINISTRATOR.DS."components".DS."com_vvisit_counter".DS."helpers";
$vvisit_exists	=	file_exists( $vvisit_path.DS."vinaora_visitors_counter.php" ) &&
					file_exists( $vvisit_path.DS."browsers.php" ) &&
					file_exists( $vvisit_path.DS."datetime.php" );

if ( $vvisit_exists ) {
	require_once ( $vvisit_path.DS."vinaora_visitors_counter.php" );
	require_once ( $vvisit_path.DS."browsers.php" );
	require_once ( $vvisit_path.DS."datetime.php" );
}
else{
	echo $vvisit_path;
	echo JText::printf("Please reinstall [Vinaora Visitors Counter] component");
	return;
}

require_once ( dirname(__FILE__).DS.'helper.php' );

/* ------------------------------------------------------------------------------------------------ */
// Read our Parameters
$mode			=	@$params->get( 'mode', 'full' );

$today			=	@$params->get( 'today', 'Today' );
$yesterday		=	@$params->get( 'yesterday', 'Yesterday' );
$x_week			=	@$params->get( 'week', 'This week' );
$l_week			=	@$params->get( 'lweek', 'Last week' );
$x_month		=	@$params->get( 'month', 'This month' );
$l_month		=	@$params->get( 'lmonth', 'Last month' );
$all			=	@$params->get( 'all', 'All days' );

$beginday		=	@$params->get( 'beginday', '' );

$online			=	@$params->get( 'online', 'Online Now: ' );
$guestip		=	@$params->get( 'guestip', 'Your IP: ' );
$guestinfo		=	@$params->get( 'guestinfo', 'Your: ' );
$formattime		=	@$params->get( 'formattime', 'Today: %B %d, %Y' );

$digit_type 	= 	@$params->get( 'digit_type', 'default' );
$number_digits	=	(int) @$params->get( 'number_digits', 6 );

$stats_type 	= 	@$params->get( 'stats_type', 'default' );
$widthtable		=	(int) @$params->get( 'widthtable', 90 );

$autohide		=	@$params->get( 'autohide', false );
$hrline			=	@$params->get( 'hrline', true );

$initialvalue	=	(int) @$params->get( 'initialvalue', 0 );

$issunday		=	@$params->get( 'issunday', true );

$pretext  		= 	@$params->get( 'pretext', "" );
$posttext  		= 	@$params->get( 'posttext', "" );

$cache_time		=	(int) @$params->get( 'cache_time', 15 );

/* ------------------------------------------------------------------------------------------------ */



/* ------------------------------------------------------------------------------------------------ */
// Check the Display Mode
switch ( $mode ){
	case "simple":
		$s_digit		=	true;
		
		$s_stats		=	false;
		
		$s_today		=	false;
		$s_yesterday	=	false;
		$s_week			=	false;
		$s_lweek		=	false;
		$s_month		=	false;
		$s_lmonth		=	false;
		$s_all			=	false;
		
		$hrline			=	false;
		$s_online		=	false;
		$s_ip			=	false;
		$s_guestinfo	=	false;
		$s_timenow		=	false;
		break;
		
	case "standard":
		$s_digit		=	true;
		
		$s_stats		=	true;
		
		$s_today		=	true;
		$s_yesterday	=	true;
		$s_week			=	true;
		$s_lweek		=	true;
		$s_month		=	true;
		$s_lmonth		=	true;
		$s_all			=	true;
		
		$hrline			=	false;
		$s_online		=	false;
		$s_ip			=	false;
		$s_guestinfo	=	false;
		$s_timenow		=	false;
		break;
		
	case "full":
		$s_digit		=	true;
		
		$s_stats		=	true;
		
		$s_today		=	true;
		$s_yesterday	=	true;
		$s_week			=	true;
		$s_lweek		=	true;
		$s_month		=	true;
		$s_lmonth		=	true;
		$s_all			=	true;
		
		$hrline			=	true;
		$s_online		=	true;
		$s_ip			=	true;
		$s_guestinfo	=	true;
		$s_timenow		=	true;
		break;
	
	case "custom":
		$s_digit		=	modVisitCounterHelper::isEnabled( $digit_type );
		
		$s_stats		=	modVisitCounterHelper::isEnabled( $stats_type );
		
		$s_today		=	modVisitCounterHelper::isEnabled( $today );
		$s_yesterday	=	modVisitCounterHelper::isEnabled( $yesterday );
		$s_week			=	modVisitCounterHelper::isEnabled( $x_week );
		$s_lweek		=	modVisitCounterHelper::isEnabled( $l_week );
		$s_month		=	modVisitCounterHelper::isEnabled( $x_month );
		$s_lmonth		=	modVisitCounterHelper::isEnabled( $l_month );
		$s_all			=	modVisitCounterHelper::isEnabled( $all );
		
		$s_online		=	modVisitCounterHelper::isEnabled( $online );
		$s_ip			=	modVisitCounterHelper::isEnabled( $guestip );
		$s_guestinfo	=	modVisitCounterHelper::isEnabled( $guestinfo );
		$s_timenow		=	modVisitCounterHelper::isEnabled( $formattime );
		break;
}
$iscache		=	modVisitCounterHelper::isEnabled( $cache_time );

/* ------------------------------------------------------------------------------------------------ */

// Get Time Offset from Global Configuration of Joomla!
$config			=&	JFactory::getConfig();
$offset			=	$config->getValue('config.offset');	

// May be use $offset =	$mainframe->getCfg( 'offset' );

// Get a reference to the global cache object.
$cache		=	& JFactory::getCache();
$cache_time	*=	60;

if ( $cache_time<0 || $cache_time>3600 ){
	$cache_time = CACHE_TIMEOUT_DEFAULT*60;
}
$cache->setLifeTime( $cache_time );

// Detect Guest's IP Address
if( !empty($_SERVER['REMOTE_ADDR']) ) $ip = $_SERVER['REMOTE_ADDR'];

/* ------------------------------------------------------------------------------------------------ */


$now			=	mktime();
$visits_array	=	array();
$datetime		=	& extVisitCounterDateTime::getTimeStart( $offset, $issunday, $now );

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

/* ------------------------------------------------------------------------------------------------ */



/* ------------------------------------------------------------------------------------------------ */
// BEGIN: Caculate number visits of Today, Yesterday, This Week, Last Week, This Month, Last Month

// Count Today's Visits
$visits_array	=	extVinaoraVisitorsCounter::getVisits(  );
$today_visits	=	$visits_array['visits'];

// Count All Visits
if ( $iscache ){
	$visits_array	= $cache->call( array( 'extVinaoraVisitorsCounter', 'getVisits' ), 0, $local_daystart );
}
else {
	$visits_array	= extVinaoraVisitorsCounter::getVisits( 0,$local_daystart );
}
$all_visits		=	$visits_array['visits'];
$all_visits		+=	$today_visits;
$all_visits		+=	$initialvalue;


// Count Yesterday's Visits
if( $s_yesterday ){
	if ( $iscache ){
		$visits_array	= $cache->call( array( 'extVinaoraVisitorsCounter', 'getVisits' ), $local_yesterdaystart, $local_daystart );
	}
	else {
		$visits_array	= extVinaoraVisitorsCounter::getVisits( $local_yesterdaystart, $local_daystart );
	}
	$yesterday_visits	=	$visits_array['visits'];
}

// Count This Week's Visits
if( $s_week ){
	if ( $iscache ){
		$visits_array	= $cache->call( array( 'extVinaoraVisitorsCounter', 'getVisits' ), $local_weekstart, $local_daystart );
	}
	else{
		$visits_array	= extVinaoraVisitorsCounter::getVisits( $local_weekstart, $local_daystart );
	}
	$week_visits	=	$visits_array['visits'];
	$week_visits	+=	$today_visits;
}

// Count Last Week's Visits
if( $s_lweek ){
	if ( $iscache ){
		$visits_array	=	$cache->call( array( 'extVinaoraVisitorsCounter', 'getVisits' ), $local_lweekstart, $local_weekstart );
	}
	else{
		$visits_array	=	extVinaoraVisitorsCounter::getVisits( $local_lweekstart, $local_weekstart );
	}
	$lweek_visits	=	$visits_array['visits'];
}

// Count This Month's Visits
if( $s_month ){
	if ( $iscache ){
		$visits_array	=	$cache->call( array( 'extVinaoraVisitorsCounter', 'getVisits' ), $local_monthstart, $local_daystart );
	}
	else{
		$visits_array	=	extVinaoraVisitorsCounter::getVisits( $local_monthstart, $local_daystart );
	}
	$month_visits	=	$visits_array['visits'];
	$month_visits	+=  $today_visits;
}

// Count Last Month's Visits
if( $s_lmonth ){
	if ( $iscache ){
		$visits_array	=	$cache->call( array( 'extVinaoraVisitorsCounter', 'getVisits' ), $local_lmonthstart, $local_monthstart );
	}
	else{
		$visits_array	=	extVinaoraVisitorsCounter::getVisits( $local_lmonthstart, $local_monthstart );
	}
	$lmonth_visits	=	$visits_array['visits'];
}

// Count Online in x minutes
$online_time	=	ONLINE_TIME_DEFAULT;
$online_time	*=	60;

if( $s_online ){
	$online_visits_array	=	extVinaoraVisitorsCounter::getVisits( 0,0,$online_time);
	//$online_visits	=	$visits_array['visits'];
}

// END: CACULATE VISITORS
/* ------------------------------------------------------------------------------------------------ */

// Show non-zero statistic
if ( $autohide && !$yesterday_visits )	$s_yesterday	=	false;
if ( $autohide && !$lweek_visits )		$s_lweek		=	false;
if ( $autohide && !$lmonth_visits )		$s_lmonth		=	false;

$time	=	& JFactory::getDate( );
$time->setOffset( $offset );

require(JModuleHelper::getLayoutPath('mod_vvisit_counter'));
?>
