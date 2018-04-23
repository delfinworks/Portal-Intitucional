<?php
/**
* @version		$Id: helper.php 2009-12-05 vinaora $
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

define( 'VERSION_MODULE' , "2.0" );
define( 'CACHE_TIMEOUT_DEFAULT' , 15 );
define( 'ONLINE_TIME_DEFAULT' , 15 );
define( 'DIGIT_COUNTER_PATH' , 'modules/mod_vvisit_counter/images/digit_counter' );
define( 'STATISTIC_ICON_PATH' , 'modules/mod_vvisit_counter/images/stats' );

class modVisitCounterHelper
{
	function render(&$params)
	{
		// doing something
	}
	
	/*
	** Check Parameter
	** Return False if Parameter equal to "0" (zero) or "No" or Empty
	*/
	/* ------------------------------------------------------------------------------------------------ */
	function isEnabled( $param = "" ){
		
		// $param is Undefined variable
		if ( empty( $param ) ) return false;
		
		// $param is Defined variable
		$param = strtolower( trim($param) );
		
		if ( $param == "" ) return false;
		if ( $param == "0" ) return false;
		if ( $param == "no" ) return false;
		
		return true;
	}
	/* ------------------------------------------------------------------------------------------------ */
	
	
	
	/*
	** Get Digits of Digital Counter
	** Return Array of Digits with Leading Zeros
	** Input: $number = 123, $length = 6
	** Output: Array a[]: a[0]=>0, a[1]=>0, a[2]=>0, a[3]=>1, a[4]=>2, a[5]=>3
	*/
	/* ------------------------------------------------------------------------------------------------ */
	function &getDigits( $number, $length=0 )
	{
		$strlen = strlen($number);
		
		$arr	=	array();
		$diff	=	$length -  $strlen;
		
		// Push Leading Zeros
		while ( $diff>0 ){
			array_push( $arr,0 );
			$diff--;
		}
		
		// For PHP 4.x
		/*
		$arrNumber	=	array();
		for ($i = 0; $i < $strlen; $i++) {
			$arrNumber[] = substr($number,$i,1);
		}
		*/
		
		// For PHP 5.x: 
		$arrNumber	=	str_split( $number );
		
		$arr		=	array_merge( $arr,$arrNumber );
		
		return $arr;
	}
	/* ------------------------------------------------------------------------------------------------ */
	

	
	/*
	** Show Digit Counter Image
	*/
	/* ------------------------------------------------------------------------------------------------ */
	function showDigitImage( $digit_type="default", $digit )
	{	
		$ret	=	'<img src="'.JURI::base().DIGIT_COUNTER_PATH.'/'.$digit_type.'/'.$digit.'.png"';
		$ret	.=	' alt="mod_vvisit_counter"';
		$ret	.=	' title="Vinaora Visitors Counter '.VERSION_MODULE.'"';
		$ret	.=	' />';
		
		return $ret;
	}
	/* ------------------------------------------------------------------------------------------------ */	

	
	
	/*
	** Show Statistics Table's Rows
	*/
	/* ------------------------------------------------------------------------------------------------ */
	function showStatisticsRows( $stats_type="default", $image, $timeline = "", $time = "", $visitors = "")
	{
		$ret	=	'<tr align="left"><td>';
		$ret	.=	'<img src="'.JURI::base().STATISTIC_ICON_PATH.'/'.$stats_type.'/'.$image.'.png"';
		$ret	.=	' alt="mod_vvisit_counter"';
		$ret	.=	' title="'.$timeline.'" /></td>';
		$ret	.=	'<td>'.$time.'</td>';
		$ret	.=	'<td align="right">'.$visitors.'</td></tr>';
		
		return $ret;
	}
	/* ------------------------------------------------------------------------------------------------ */

	
	
	/*
	** Show Timeline.
	** Output: %Y-%m-%d -> %Y-%m-%d
	*/
	/* ------------------------------------------------------------------------------------------------ */
	function showTimeLine( $timeBegin = 0, $timeEnd = 0, $offset = 0, $formattime = "%Y-%m-%d", $spacer = " -&gt; " )
	{
		$timeBegin	=	(int) $timeBegin;
		$timeEnd	=	(int) $timeEnd;
		$offset		=	(float) $offset;
		
		$str		=	"";
		
		if ( $timeBegin ){
			$time	=	& JFactory::getDate( $timeBegin );
			$time->setOffset( $offset );
			
			$str 	.=	$time->toFormat( $formattime ) ;
			
			if ( $timeEnd ){
				$time	=	& JFactory::getDate( $timeEnd );
				$time->setOffset( $offset );

				$str	.=	$spacer;
				$str	.=	$time->toFormat( $formattime ) ;
			}
		}
		return $str;
	}
	/* ------------------------------------------------------------------------------------------------ */

}
?>