<?php
/**
* @version		$Id: datetime.php 2009-12-05 vinaora $
* @package		VINAORA VISITORS COUNTER
* @copyright	Copyright (C) 2007 - 2010 VINAORA. All rights reserved.
* @license		GNU/GPL
* @website		http://vinaora.com
* @email		admin@vinaora.com
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<?php

class extVisitCounterDateTime {	
	
	/*
	** Determine Starting Date/Time of Today, Yesterday, This Week, Last Week, This Month, Last Month
	** Return Unix Time Array
	*/
	function &getTimeStart( $offset=0, $issunday=true, $now="" ){
		$offset		=	(float) $offset;
		$now		=	(int) $now;

		if ( empty($now) ){
			$now		=	mktime();
		}
		
		/* ------------------------------------------------------------------------------------------------ */
		// Determine GMT Time (UTC+00:00)
		// Determine this minute, this hour, this day, this month, this year
		// Don't use strftime()
		$minute			=	(int) gmstrftime( "%M", $now );
		$hour			=	(int) gmstrftime( "%H", $now );
		$day			=	(int) gmstrftime( "%d", $now );
		$month			=	(int) gmstrftime( "%m", $now );
		$year			=	(int) gmstrftime( "%Y", $now );
		
		// Determine Starting GMT Time and Local Time of Today
		$daystart		=	gmmktime( 0,0,0,$month,$day,$year );
		$local_daystart	=	extVisitCounterDateTime::localTimeStart( $daystart, $offset, "day");
		
		// Determine Starting GMT Time and Local Time of Yesterday
		// $yesterdaystart		=	strtotime( "-1 day", $daystart ) ;
		
		$yesterdaystart			=	$daystart - 86400;
		$local_yesterdaystart	=	$local_daystart - 86400;
		
		// Determine Starting GMT Time and Local Time of This Week
		// If Sunday is starting day of week then Sunday = 0 ... Saturday = 6
		// If Monday is starting day of week then Monday = 0 ... Sunday = 6
		$weekday			=	(int) strftime("%w", $now );
		
		if ( !$issunday ) {
			if ( $weekday ) $weekday--;
			else $weekday = 6;
		}
		
		$weekstart			=	$daystart - $weekday*86400;
		$local_weekstart	=	extVisitCounterDateTime::localTimeStart( $weekstart, $offset, "week");
		
		// Starting Starting GMT Time and Local Time of Last Week
		$lweekstart			=	$weekstart - 7*86400;
		$local_lweekstart	=	$local_weekstart - 7*86400;
		
		// Determine Starting GMT Time and Local Time of This Month
		$monthstart			=	gmmktime( 0,0,0,$month,1,$year );
		$local_monthstart	=	extVisitCounterDateTime::localTimeStart( $monthstart, $offset, "month");
		
		// Determine Starting GMT Time and Local Time of Last Month
		// $days_lmonth: Number days of last month (28/29, 30 or 31)
		$days_lmonth		=	(int) strftime("%d", $monthstart - 86400 );
		$lmonthstart		=	$monthstart - $days_lmonth*86400;
		$local_lmonthstart	=	$local_monthstart - $days_lmonth*86400;
		
		$datetime	=	array();
		
		$datetime["daystart"]				=	$daystart;
		$datetime["local_daystart"]			=	$local_daystart;
		$datetime["yesterdaystart"]			=	$yesterdaystart;
		$datetime["local_yesterdaystart"]	=	$local_yesterdaystart;
		$datetime["weekstart"]				=	$weekstart;
		$datetime["local_weekstart"]		=	$local_weekstart;
		$datetime["lweekstart"]				=	$lweekstart;
		$datetime["local_lweekstart"]		=	$local_lweekstart;
		$datetime["monthstart"]				=	$monthstart;
		$datetime["local_monthstart"]		=	$local_monthstart;
		$datetime["lmonthstart"]			=	$lmonthstart;
		$datetime["local_lmonthstart"]		=	$local_lmonthstart;
		
		return $datetime;
	}
	
	
	
	/*
	** Determine Local Starting Time
	** Return Unix Time
	** Example: If Global Time (GMT+00:00) = 1248912000 (2009/07/31 - 00:00:00)
	**			then Local Time (GMT+07:00) = 1248912000 - 7*3600 = 1248886800,
	**			Local Time (GMT-05:00) = 1248912000 + 5*3600 = 1248930000
	*/
	/* ------------------------------------------------------------------------------------------------ */
	function localTimeStart( $timestart, $offset=0, $type="day", $now = "" ){
		$timestart	=	(int) $timestart;
		$offset		=	(float) $offset;
		$now		=	(int) $now;
		
		if ( empty($now) ){
			$now		=	mktime();
		}
		
		$type	=	strtolower( trim ($type) );
		if ( $type != "day" && $type != "week" && $type != "month" ) $type = "day";

		$nexttimestart	=	strtotime( "+1 " . $type, $timestart ) ;
		$lasttimestart	=	strtotime( "-1 " . $type, $timestart ) ;
		
		if ( $offset > 0 ) {
			if ( ( $nexttimestart - $now ) < $offset*60*60 ) {
				$timestart = $nexttimestart - $offset*60*60;
			}
			else $timestart -=  $offset*60*60;
		}
		
		if ( $offset < 0 ) {
			$offset	=	-$offset;
			if ( ( $now - $timestart ) < $offset*60*60 ) {
				$timestart = $lasttimestart + $offset*60*60;
			}
			else $timestart += $offset*60*60;
		}
		
		return $timestart;
	}
	/* ------------------------------------------------------------------------------------------------ */
	
}
?>