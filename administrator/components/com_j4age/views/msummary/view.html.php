<?php


/**
 * @package j4age
 * @copyright Copyright (C) 2009-@THISYEAR@ j4age Team. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * Thank you to the project j4age and it's team, on which roots this project is build on.
 */



// Check to ensure this file is only called within the Joomla! application and never directly
if( !defined( '_JEXEC' ) ) {
	die( 'JS: No Direct Access to '.__FILE__ );
}


jimport( 'joomla.application.component.view' );
require_once( dirname(__FILE__) .DS. '..'.DS. '..' .DS. 'database' .DS.'select.one.value.php' );
require_once( dirname(__FILE__) .DS. '..'.DS. '..' .DS. 'api' .DS. 'general.php' );
require_once( dirname(__FILE__) .DS. '..'.DS. '..' .DS. 'libraries'.DS. 'template.html.php' );
require_once( dirname(__FILE__) .DS. '..'.DS. '..' .DS. 'libraries'.DS. 'statistics.common.html.php' );

/**
 * Hello View
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class j4ageViewMSummary extends JView
{
	/**
	 * display method of Hello view
	 * @return void
	 **/
	function display($tpl = null)
	{
	/*
		$model	  =& $this->getModel();
		$text = '';
		JToolBarHelper::title(   JText::_( 'Technical Expertise' ).': <small><small>[ ' . $text.' ]</small></small>', 'dvw' );
		JToolBarHelper::save('saveEntry');
		if ($isNew)  {
			JToolBarHelper::cancel('cancelEntry');
		} else {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancelEntry', 'Close' );
		}
        */
		//show the sub content

        $chartView =  js_getView('amline', 'html');

		$engine =  JoomlaStats_Engine::getInstance();
        $JSTemplate = new js_JSTemplate();
        $JSStatisticsTpl = new js_JSStatisticsCommonTpl();

        $JSDbSOV = new js_JSDbSOV();

        $prof = & JProfiler::getInstance( 'JS' );

        $this->assignRef("engine", $engine);
        $this->assignRef("JSTemplate", $JSTemplate);
        $this->assignRef("JSStatisticsTpl", $JSStatisticsTpl);
        $this->assignRef("JSDbSOV", $JSDbSOV);
        //$this->assignRef("JSApiGeneral", $JSApiGeneral);
        $this->assignRef("prof", $prof);

		js_echoJSDebugInfo($prof->mark('begin'), '');

		//$JSUtil = new js_JSUtil();

		$buid = 0;
		$JSDbSOV->getBuid( $buid );

		$info	= ''; // new mic

		$junk  = '%';
		$month = '%';
		$year  = '%';

		$this->engine->FilterTimePeriod->getDMY( $junk, $month, $year );

		{//set month and year when not selected
            $JSNowTimeStamp = js_getJSNowTimeStamp();
			if( $month == '%' ) {
				// user selected whole month ('-')
				$month	= js_gmdate( 'n', $JSNowTimeStamp );

				$info .= JTEXT::_( 'You have not choosen a month displaying data of' ).': <strong>'. $JSTemplate->monthToString($month, false) . '</strong>';
			}

			if( $year == '%' ) {
				$year	= js_gmdate( 'Y', $JSNowTimeStamp );

				$info .= JTEXT::_( 'You have not choosen a year displaying data of' ).': <strong>' . $year . '</strong>';
			}
		}
        $cacheKey = $year."-".$month;
		if( $info ) {
            JError::raise(E_NOTICE, 0, $info);
		}

		$daysOfMonth = array(0,31,28 + date('L',mktime(0,0,0,(int)$month,(int)1,(int)$year)),31,30,31,30,31,31,30,31,30,31);



        $timestamp_from = mktime(0, 0, 0, $month, 1, $year);
        $timestamp_to = mktime(23, 59, 59, $month, $daysOfMonth[$month], $year);

        $this->engine->FilterTimePeriod->setTimePeriod( $timestamp_from, $timestamp_to );
        $this->engine->FilterTimePeriod->getTimePeriodsDatesAsTimestamp( $timestamp_from, $timestamp_to );

		js_echoJSDebugInfo($prof->mark('after includes and creating variables'), '');

        $rows = array();
        $total = new stdClass();
        $visits_average = '0.0';

        $this->assignRef("dm", $daysOfMonth);
        $this->assignRef("buid", $buid);
        $this->assignRef("year", $year);
        $this->assignRef("month", $month);

        $this->retrieveData($rows, $total, $visits_average, $chartView, $timestamp_from, $timestamp_to, $cacheKey, $year, $month);

        $this->assignRef("total", $total);
        $this->assignRef("visits_average", $visits_average);
        $this->assignRef("chartView", $chartView);

		parent::display();
	}

    function retrieveData(&$rows, &$total, &$visits_average, &$chartView, $timestamp_from, $timestamp_to, $cacheKey, $year, $month )
    {
        $prof = & JProfiler::getInstance( 'JS' );
        $JSApiGeneral = new js_JSApiGeneral();
        $resolution = 'day';

		$v_arr = array();
		{ //get visitors
			$visitors_type = _JS_DB_IPADD__TYPE_REGULAR_VISITOR;
			$arr_obj_result = null;
			$JSApiGeneral->getAmountOfVisitsByResolution( $resolution, $visitors_type, $timestamp_from, $timestamp_to, $arr_obj_result, $cacheKey."-v_arr" );
			if ($arr_obj_result) {
				foreach($arr_obj_result as $obj)
					$v_arr[$obj->day] = $obj->nbr_visitors;
			}
		}

        js_echoJSDebugInfo($prof->mark('Collected $v_arr data from DB'));
        if( js_checkForTimeOut() ) return;


		$b_arr = array();
		{ //get bots
			$visitors_type = _JS_DB_IPADD__TYPE_BOT_VISITOR;
			$arr_obj_result = null;
			$JSApiGeneral->getAmountOfVisitsByResolution( $resolution, $visitors_type, $timestamp_from, $timestamp_to, $arr_obj_result, $cacheKey."-b_arr" );
			if ($arr_obj_result) {
				foreach($arr_obj_result as $obj)
					$b_arr[$obj->day] = $obj->nbr_visitors;
			}
		}

        js_echoJSDebugInfo($prof->mark('Collected $b_arr data from DB'));
        if( js_checkForTimeOut() ) return;

		$niv_arr = array();
		{ // not identified visitors
			$visitors_type = _JS_DB_IPADD__TYPE_NOT_IDENTIFIED_VISITOR;
			$arr_obj_result = null;
			$JSApiGeneral->getAmountOfVisitsByResolution( $resolution, $visitors_type, $timestamp_from, $timestamp_to, $arr_obj_result, $cacheKey."-niv_arr" );
			if ($arr_obj_result) {
				foreach($arr_obj_result as $obj)
					$niv_arr[$obj->day] = $obj->nbr_visitors;
			}
		}

        js_echoJSDebugInfo($prof->mark('Collected $niv_arr data from DB'));
        if( js_checkForTimeOut() ) return;

		$p_arr = array();
		{ // pages

            $query =
                    "
                    SELECT SQL_BIG_RESULT count(*) AS nbr_impressions, MONTH(FROM_UNIXTIME(`timestamp`)) AS month, YEAR(FROM_UNIXTIME(`timestamp`)) AS year, DAYOFMONTH(FROM_UNIXTIME(`timestamp`)) AS day,  timestamp
                        FROM `#__jstats_impressions`
                        WHERE ( timestamp >= $timestamp_from AND timestamp <= $timestamp_to )
                        GROUP BY YEAR(FROM_UNIXTIME(`timestamp`)), MONTH(FROM_UNIXTIME(`timestamp`)), DAYOFMONTH(FROM_UNIXTIME(`timestamp`))
                    ";

            $arr_obj_result = js_Cache::cachedQuery($cacheKey."-p_arr", $query, $timestamp_from, $timestamp_to);

			//$this->engine->db->setQuery( $query );
			//$arr_obj_result = $this->engine->db->loadObjectList();
			if ($arr_obj_result) {
				foreach($arr_obj_result as $obj)
					$p_arr[$obj->day] = $obj->nbr_impressions;
			}

		}

        js_echoJSDebugInfo($prof->mark('Collected $p_arr data from DB'));
        $pp_arr = array();
        //todo remove the assignments - not needed anymore
        $this->assignRef("pp_arr", $pp_arr);
        $this->assignRef("p_arr", $p_arr);
        $this->assignRef("niv_arr", $niv_arr);
        $this->assignRef("b_arr", $b_arr);
        $this->assignRef("v_arr", $v_arr);

        $this->assignRef("date_from", $timestamp_from);
        $this->assignRef("date_to", $timestamp_to);

        $v 			= 0; // visitors
        $b 			= 0; // bots
        $p			= 0; // pages
        $r			= 0; // referrer
        $inquiries	= 0; // search inquiries
        $ub 		= 0; // unique bots
        $tub		= 0; // total unique bots
        $uv 		= 0; // unique visitors
        $tv 		= 0; // total visitors
        $tuv		= 0; // total unique visitors
        $tb 		= 0; // total bots
        $tp 		= 0; // total pages
        $tr 		= 0; // total referrers
        $sum		= 0; // sum
        $tsum		= 0; // total sum
        $usum		= 0; // unique sum
        $tusum		= 0; // total unique sum
        $univ		= 0; // unique not identified visitors
        $tuniv		= 0; // total unique not identified visitors
        $niv		= 0; // not identified visitors
        $tniv		= 0; // total not identified visitors
        $total_inquiries = 0;

		js_echoJSDebugInfo($this->prof->mark('before loop'), '');
        if( js_checkForTimeOut() ) return;


		for( $i = 1; $i <= $this->dm[$this->month]; $i++) {

            $row = new stdClass();
			$day = $i;

            $timestamp_from = mktime(0,0,0,$this->month, $day, $this->year);
            $timestamp_to   = mktime(23,59,59,$this->month, $day, $this->year);

            $row->day = $day;
            $row->year = $this->year;
            $row->month = $this->month;
            $dayCachedKey = $cacheKey.'-'.$day;
			{ // get Unique visitors
				$visitors_type = _JS_DB_IPADD__TYPE_REGULAR_VISITOR;
				$this->JSDbSOV->selectNumberOfUniqueVisitors( $visitors_type, $dayCachedKey."-uv", $this->buid, $timestamp_from, $timestamp_to, $uv );
			}


			{ // get visitors
				$v = (isset($this->v_arr[$i])) ? $this->v_arr[$i] : 0;
				$tv += $v;
			}

			{ // get bots
				$b = (isset($this->b_arr[$i])) ? $this->b_arr[$i] : 0;
				$tb += $b;
			}


			{ // get Unique bots
				$visitors_type = _JS_DB_IPADD__TYPE_BOT_VISITOR;
				$this->JSDbSOV->selectNumberOfUniqueVisitors( $visitors_type, $dayCachedKey."-ub", $this->buid, $timestamp_from, $timestamp_to, $ub );
			}

			{ // get Pages
				$p = (isset($this->p_arr[$i])) ? $this->p_arr[$i] : 0;
				$tp += $p;
			}

			// get Referrers
            $mytimestamp_from = mktime(0, 0, 0, $this->month, 1, $this->year);
            $mytimestamp_to = mktime(23, 59, 59, $this->month, $i, $this->year);

			$query = "SELECT count(*) as referrercount FROM #__jstats_referrer WHERE timestamp >= $timestamp_from AND timestamp <= $timestamp_to";
            $rObject = js_Cache::cachedObjectQuery( $dayCachedKey."-r", $query, $timestamp_from, $timestamp_to);
            $r = $rObject->referrercount;

            $query = "SELECT count(*) as inquiries FROM #__jstats_keywords WHERE referrer_id IS NOT NULL AND timestamp >= $timestamp_from AND timestamp <= $timestamp_to";
            $rObject = js_Cache::cachedObjectQuery( $dayCachedKey."-inquiries", $query, $timestamp_from, $timestamp_to);
            $inquiries = $rObject->inquiries;
            $row->inquiries = $inquiries;
            $r = $r-$inquiries;

			$tr += $r;

            $total_inquiries += $inquiries;


			{ // not identified visitors
				$niv = (isset($this->niv_arr[$i])) ? $this->niv_arr[$i] : 0;
				$tniv += $niv;
			}


			{// unique not identified visitors
				$visitors_type = _JS_DB_IPADD__TYPE_NOT_IDENTIFIED_VISITOR;
				$this->JSDbSOV->selectNumberOfUniqueVisitors( $visitors_type, $dayCachedKey."-univ", $this->buid, $timestamp_from, $timestamp_to, $univ );
			}


			// sums
			$sum  = $v  + $b  + $niv;
			$usum = $uv + $ub + $univ;
			$tsum  += $sum;

            $row->month = $month;
            $row->i = $i;
            $row->uv = $uv;
            $row->v = $v;
            $row->p = $p;
            $row->r = $r;
            $row->ub = $ub;
            //$row->add = $add;
            $row->b = $b;
            $row->univ = $univ;
            $row->niv = $niv;
            $row->usum = $usum;
            $row->sum = $sum;
            $row->changed_at = mktime(0,0,0, $month, $i, $year);

            if( ( $row->uv != 0 ) && ( $row->v != 0 ) ) {
                $format_token = '%01.2f';
                $row->vavg = sprintf($format_token, ( $row->v / $row->uv ));
            }else{
                $row->vavg = '.';
            }

            $rows[] = $row;
            if( js_checkForTimeOut() ) return;

		}
        if(!empty($chartView))
        {
            $chartView->createGraph($rows, 'uv', array('title'=> JTEXT::_( 'Unique visitors' ), 'bullet'=> 'square_outlined' ));
            $chartView->createGraph($rows, 'v', array('title'=> JTEXT::_( 'Visitors' ), 'bullet'=> 'square_outlined' ));
            $chartView->createGraph($rows, 'vavg', array('title'=> JTEXT::_( 'Visits average' ), 'bullet'=> 'square_outlined' ));
            $chartView->createGraph($rows, 'r', array('title'=> JTEXT::_( 'Referrers' ), 'bullet'=> 'square_outlined' ));
            $chartView->createGraph($rows, 'ub', array('title'=> JTEXT::_( 'Unique bots/spiders' ), 'bullet'=> 'square_outlined' ));
            $chartView->createGraph($rows, 'b', array('title'=> JTEXT::_( 'Bots/Spiders' ), 'bullet'=> 'square_outlined' ));
            $chartView->createGraph($rows, 'univ', array('title'=> JTEXT::_( 'Unique NIV' ), 'bullet'=> 'square_outlined' ));
            $chartView->createGraph($rows, 'niv', array('title'=> JTEXT::_( 'NIV' ), 'bullet'=> 'square_outlined' ));
            $chartView->createGraph($rows, 'usum', array('title'=> JTEXT::_( 'Unique sum' ), 'bullet'=> 'square_outlined' ));
            $chartView->createGraph($rows, 'sum', array('title'=> JTEXT::_( 'Sum' ), 'bullet'=> 'square_outlined' ));
        }

		js_echoJSDebugInfo($this->prof->mark('after loop'), '');
        $this->assignRef("rows", $rows);

        { // Get the values for the totals line
			// RB: values acuired higher in this function are wrong - remove them
			// RB: change to new database method
			// AT: v3.0 changed to new method once again

			$timestamp_from = mktime(0,0,0,$this->month, 1, $this->year);
			$timestamp_to   = mktime(23,59,59,$this->month, $this->dm[$this->month], $this->year);

			{ // get Unique visitors
				$visitors_type = _JS_DB_IPADD__TYPE_REGULAR_VISITOR;
				$this->JSDbSOV->selectNumberOfUniqueVisitors( $visitors_type, $cacheKey."-tuv", $this->buid, $timestamp_from, $timestamp_to, $tuv );
			}

			{ // get Unique bots
				$visitors_type = _JS_DB_IPADD__TYPE_BOT_VISITOR;
				$this->JSDbSOV->selectNumberOfUniqueVisitors( $visitors_type, $cacheKey."-tub", $this->buid, $timestamp_from, $timestamp_to, $tub );
			}

			{// unique not identified visitors
				$visitors_type = _JS_DB_IPADD__TYPE_NOT_IDENTIFIED_VISITOR;
				$this->JSDbSOV->selectNumberOfUniqueVisitors( $visitors_type, $cacheKey."-tuniv", $this->buid, $timestamp_from, $timestamp_to, $tuniv );
			}

			$tusum = $tuv + $tub + $tuniv;
		}


			$total->month_or_year = $this->month;
			$total->tuv           = $tuv;
			$total->tv            = $tv;
			$total->tp            = $tp;
			$total->tr            = $tr;
			$total->tub           = $tub;
			$total->tb            = $tb;
			$total->tuniv         = $tuniv;
			$total->tniv          = $tniv;
			$total->tusum         = $tusum;
			$total->tsum          = $tsum;
            $total->inquiries = $total_inquiries;

		//Total line (last line, sum line)

		if( ( $total->tuv != 0 ) && ( $total->tv != 0 ) ) {
			$format_token = '%01.2f';
			$visits_average = sprintf($format_token, ( $total->tv / $total->tuv ));
		}
    }

    function retrieveData_new(&$rows, &$total, &$visits_average, &$chartView, $timestamp_from, $timestamp_to, $cacheKey )
    {
        $query =
                            "
                            SELECT data
                                FROM `#__jstats_cache`
                                WHERE type = '$cacheKey' ( timestamp >= $timestamp_from AND timestamp <= $timestamp_to )

                            ";

                    $arr_obj_result = js_Cache::cachedQuery($cacheKey."-p_arr", $query, $timestamp_from, $timestamp_to);

    }
}
