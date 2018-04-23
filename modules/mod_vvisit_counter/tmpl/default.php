<?php
/**
* @version		$Id: default.php 2009-12-05 vinaora $
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
defined('_JEXEC') or die('Restricted access'); 
?>
<?php

JHTML::stylesheet("mod_vvisit_counter.css", "modules/mod_vvisit_counter/css/");

$html	=	'<!-- Vinaora Visitors Counter for Joomla! -->';
$html 	.= 	'<div id="vvisit_counter">';
if (!empty($pretext)) $html .= '<div>'.$pretext.'</div>';

/* ------------------------------------------------------------------------------------------------ */
// BEGIN: SHOW DIGIT COUNTER
if($s_digit){

	$html .= '<div>';
	
	$arr = & modVisitCounterHelper::getDigits( $all_visits,$number_digits );
	
	foreach ($arr as $digit){
		$html .= modVisitCounterHelper::showDigitImage( $digit_type, $digit );
	}
	$html .= '</div>';
	
}
// END: SHOW DIGIT COUNTER
/* ------------------------------------------------------------------------------------------------ */


/* ------------------------------------------------------------------------------------------------ */
// BEGIN: TABLE STATISTICS

if ( $s_stats ){

	$html		.=	'<div>';
	$html		.=	'<table cellpadding="0" cellspacing="0" style="width: '.$widthtable.'%;">';
	$html		.=	'<tbody>';
	
	// Show today, yestoday, this week, this month, and all visits
	if($s_today){
		$timeline	=	modVisitCounterHelper::showTimeLine( $local_daystart, 0, $offset );
		$html		.=	modVisitCounterHelper::showStatisticsRows( $stats_type, "vtoday", $timeline, $today, $today_visits );
	}
	if($s_yesterday){
		$timeline	=	modVisitCounterHelper::showTimeLine( $local_yesterdaystart, 0, $offset );
		$html		.=	modVisitCounterHelper::showStatisticsRows( $stats_type, "vyesterday", $timeline, $yesterday, $yesterday_visits );
	}
	if($s_week){
		$timeline	=	modVisitCounterHelper::showTimeLine( $local_weekstart, $local_daystart, $offset );
		$html		.=	modVisitCounterHelper::showStatisticsRows( $stats_type, "vweek", $timeline, $x_week, $week_visits );
	}
	if($s_lweek){
		$timeline	=	modVisitCounterHelper::showTimeLine( $local_lweekstart, $local_weekstart, $offset );
		$html		.=	modVisitCounterHelper::showStatisticsRows( $stats_type, "vlweek", $timeline, $l_week, $lweek_visits );
	}
	if($s_month){
		$timeline	=	modVisitCounterHelper::showTimeLine( $local_monthstart, $local_daystart, $offset );
		$html		.=	modVisitCounterHelper::showStatisticsRows( $stats_type, "vmonth", $timeline, $x_month, $month_visits );
	}
	if($s_lmonth){
		$timeline	=	modVisitCounterHelper::showTimeLine( $local_lmonthstart, $local_monthstart, $offset );
		$html		.=	modVisitCounterHelper::showStatisticsRows( $stats_type, "vlmonth", $timeline, $l_month, $lmonth_visits );
	}
	if($s_all){
		$timeline	=	empty($beginday)?"Vinaora Visitors Counter":JText::sprintf('Since',$beginday);
		$html	.=	modVisitCounterHelper::showStatisticsRows( $stats_type, "vall", $timeline, $all, $all_visits );
	}
	
	$html		.= "</tbody></table></div>";
}
// END: TABLE STATISTICS
/* ------------------------------------------------------------------------------------------------ */



/* ------------------------------------------------------------------------------------------------ */
// BEGIN: SHOW HORIZONTAL LINE
if ( $hrline ){
	$html  	.=	'<hr/>';
}
// END: SHOW HORIZONTAL LINE
/* ------------------------------------------------------------------------------------------------ */



/* ------------------------------------------------------------------------------------------------ */
// BEGIN: SHOW GUEST'S INFO
// Show Guest's Info

if ( $s_online || $s_ip || $s_guestinfo || $s_timenow ){

	$html		.=	'<div>';
	
	if($s_online){
		//$html	.= $online . " " . $online_visits . "<br />";
		
		$html	.=	JText::sprintf('We have').':&nbsp;';
		
		$str_online = '';
		if ( $online_visits_array['guests'] ){
			$str_online	.=	JText::sprintf('guests', $online_visits_array['guests']).',&nbsp;';
		}
		if ( $online_visits_array['members'] ){
			$str_online	.=	JText::sprintf('members', $online_visits_array['members']).',&nbsp;';
		}
		if ( $online_visits_array['bots'] ){
			$str_online	.=	JText::sprintf('bots', $online_visits_array['bots']).',&nbsp;';
		}
		
		$str_online	=	substr($str_online,0,-7);
		
		$html	.=	$str_online.'&nbsp;'.JText::sprintf('online');
		$html	.=	'<br />';
	}
	
	if($s_ip){
		$html	.= $guestip . ":&nbsp;" . $ip . "<br />";
	}
	
	if($s_guestinfo){
		//require_once(JPATH_ADMINISTRATOR.DS."components".DS."com_vvisit_counter".DS."helpers".DS."browsers.php");
		$browser = _browser();
		if (!empty( $browser )){
			$html	.=	( $browser['browser'] == "msie" ? "Internet Explorer":ucwords($browser['browser']) );
			$html	.= "&nbsp;";
			$html	.= $browser['version'];
			$html	.= ",&nbsp;";
			$html	.= ucwords($browser['platform']) ;
			$html		.= "<br /> ";
		}
	}
	
	if ($s_timenow){		
		$html		.=	$time->toFormat( $formattime );
	}
	
	$html		.=	'</div>';
}
// END: SHOW GUEST'S INFO
/* ------------------------------------------------------------------------------------------------ */



/* ------------------------------------------------------------------------------------------------ */
if (!empty($posttext)){
	$html		.= '<div>'.$posttext.'</div>';
}

$html .= '<div><a href="http://vina';
$html .= 'ora.com" target="_self" title="Vinaora Visit';
$html .= 'ors Counter '.VERSION_MODULE.' for Joomla!">';
$html .= 'Visitors Counter</a></div>';
$html .= '</div>';
/* ------------------------------------------------------------------------------------------------ */

echo $html;
?>