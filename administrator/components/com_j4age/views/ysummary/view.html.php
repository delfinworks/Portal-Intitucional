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
 * JoomlaStats View Controller
 *
 */
class j4ageViewYSummary extends JView
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


		$engine =  JoomlaStats_Engine::getInstance();
		$this->assignRef("engine", $engine);
        $chartView =  js_getView('amline', 'html');
        $chartEnabled = !empty($chartView);
        $prof = & JProfiler::getInstance( 'JS' );
		js_echoJSDebugInfo($prof->mark('begin'), '');


		$JSDbSOV = new js_JSDbSOV();
		$JSApiGeneral = new js_JSApiGeneral();

		$buid = 0;
		$JSDbSOV->getBuid( $buid );

		$junk  = '%';
		$junk2 = '%';
		$year  = '%';

		$this->engine->FilterTimePeriod->getDMY( $junk, $junk2, $year );

        if($year == '%')
        {
           $year	= js_gmdate( 'Y' );
           JError::raise(E_NOTICE, 0, JTEXT::_( 'You have not choosen a year displaying data of' ).': <strong>'. $year . '</strong>');
        }
        $year = intval($year);
        
        $cacheKey = $year;

        $timestamp_from = 0;//mktime(0, 0, 0, 1, 1, $year);
        $timestamp_to = 0;//mktime(23, 59, 59, 12, 31, $year);

        /**
         * the filter is used to retrieve the period of the charts, so we have to set the period first
         */
        $this->engine->FilterTimePeriod->setLocalTimePeriod(0, 0, 0, 1, 1, $year, 23, 59, 59, 12, 31, $year);
        $this->engine->FilterTimePeriod->getTimePeriodsDatesAsTimestamp( $timestamp_from, $timestamp_to );

		$where = array();

		$v			= 0; // visitor;
		$uv			= 0; // unique visitor
		$b			= 0; // bots
		$ub			= 0; // unique bots
		$p 			= 0; // pages
		$r 			= 0; // referrers
		$tuv		= 0; // total unique visitors
		$tv			= 0; // total visitors
		$tub		= 0; // total unique bots
		$tb			= 0; // total bots
		$tp			= 0; // total pages
		$tr			= 0; // total referrers
		$niv		= 0; // not identified visitors
		$tniv		= 0; // total not identified visitors
		$univ		= 0; // unique not identified visitors
		$tuniv		= 0; // total unique not identified visitors
		$sum		= 0; // sum
		$tsum		= 0; // total sum
		$usum		= 0; // unique sum
		$tusum		= 0; // total unique sum
        $total_inquiries = 0;

		$resolution = 'month';
		$include_summarized = $this->engine->JSConf->include_summarized;

		js_echoJSDebugInfo($prof->mark('after includes and creating variables'), '');


		$v_arr = array();
		{ //get visitors
			$visitors_type = _JS_DB_IPADD__TYPE_REGULAR_VISITOR;
			$arr_obj_result = null;
			$JSApiGeneral->getAmountOfVisitsByResolution( $resolution, $visitors_type, $timestamp_from, $timestamp_to, $arr_obj_result, $cacheKey."-v_arr" );
			if ($arr_obj_result) {
				foreach($arr_obj_result as $obj)
					$v_arr[$obj->month] = $obj->nbr_visitors;
			}
		}

        js_echoJSDebugInfo($prof->mark('Collected v_arr data from DB'));
        if( js_checkForTimeOut() ) return;


		$b_arr = array();
		{ //get bots
			$visitors_type = _JS_DB_IPADD__TYPE_BOT_VISITOR;
			$arr_obj_result = null;
			$JSApiGeneral->getAmountOfVisitsByResolution( $resolution, $visitors_type, $timestamp_from, $timestamp_to, $arr_obj_result, $cacheKey."-b_arr" );
			if ($arr_obj_result) {
				foreach($arr_obj_result as $obj)
					$b_arr[$obj->month] = $obj->nbr_visitors;
			}
		}

        js_echoJSDebugInfo($prof->mark('Collected b_arr data from DB'));
        if( js_checkForTimeOut() ) return;


		$niv_arr = array();
		{ // not identified visitors
			$visitors_type = _JS_DB_IPADD__TYPE_NOT_IDENTIFIED_VISITOR;
			$arr_obj_result = null;
			$JSApiGeneral->getAmountOfVisitsByResolution( $resolution, $visitors_type, $timestamp_from, $timestamp_to, $arr_obj_result, $cacheKey."-niv_arr" );
			if ($arr_obj_result) {
				foreach($arr_obj_result as $obj)
					$niv_arr[$obj->month] = $obj->nbr_visitors;
			}
		}

        js_echoJSDebugInfo($prof->mark('Collected niv_arr data from DB'));
        if( js_checkForTimeOut() ) return;


		$p_arr = array();
		{ // pages

			$query =
                    "
                    SELECT SQL_BIG_RESULT count(*) AS nbr_impressions, MONTH(FROM_UNIXTIME(`timestamp`)) AS month, YEAR(FROM_UNIXTIME(`timestamp`)) AS year, timestamp
                        FROM `#__jstats_impressions`
                        WHERE ( timestamp >= $timestamp_from AND timestamp <= $timestamp_to )
                        GROUP BY YEAR(FROM_UNIXTIME(`timestamp`)), MONTH(FROM_UNIXTIME(`timestamp`))
                    ";
            // visit_date
            $arr_obj_result = js_Cache::cachedQuery( $cacheKey."-p_arr", $query, $timestamp_from, $timestamp_to);

			//$this->engine->db->setQuery( $query );
			//$arr_obj_result = $this->engine->db->loadObjectList();
			if ($arr_obj_result) {
				foreach($arr_obj_result as $obj)
                {
                    $p_arr[$obj->month] = $obj->nbr_impressions;
                }
			}

            js_echoJSDebugInfo($prof->mark('Collected p_arr data from DB'));

            if($chartEnabled && $arr_obj_result)
            {
                $chartView->createGraph($arr_obj_result, 'nbr_impressions', array('title'=> JTEXT::_( 'Page impressions' ), 'bullet'=> 'square_outlined' ));
                js_echoJSDebugInfo($prof->mark('Graph for Chart created'));
            }
		}
        if( js_checkForTimeOut() ) return;



        js_echoJSDebugInfo($prof->mark('Start Loop'));

		$dm = array(0,31,28 + date('L',mktime(0,0,0,(int)1,(int)1,(int)$year)),31,30,31,30,31,31,30,31,30,31);

		$alternator = 0;

        $rows = array();

		for( $i = 1; $i < 13; $i++ ) {

            $row = new stdClass();

			//$month = $i;
            $timestamp_from = mktime(0, 0, 0, $i, 1, $year);
            $timestamp_to = mktime(23, 59, 59, $i, $dm[$i], $year);

            $cacheMonthKey = $cacheKey.'-'.$i;

			{ // get Unique visitors
				$visitors_type = _JS_DB_IPADD__TYPE_REGULAR_VISITOR;
				$JSDbSOV->selectNumberOfUniqueVisitors( $visitors_type, $cacheMonthKey."-uv", $buid, $timestamp_from, $timestamp_to, $uv );
				$tuv += $uv;
			}


			{ // get visitors
				$v = (isset($v_arr[$i])) ? $v_arr[$i] : 0;
				$tv += $v;
			}

			{ // get bots
				$b = (isset($b_arr[$i])) ? $b_arr[$i] : 0;
				$tb += $b;
			}

			{ // get Unique bots
				$visitors_type = _JS_DB_IPADD__TYPE_BOT_VISITOR;
				$JSDbSOV->selectNumberOfUniqueVisitors( $visitors_type, $cacheMonthKey."-ub", $buid, $timestamp_from, $timestamp_to, $ub );
				$tub += $ub;
			}

			{ // get Pages
				$p = (isset($p_arr[$i])) ? $p_arr[$i] : 0;
				$tp += $p;
			}

			// get Referrers
			$query = "SELECT count(*) as referrercount FROM #__jstats_referrer WHERE timestamp >= $timestamp_from AND timestamp <= $timestamp_to";
            $rObject = js_Cache::cachedObjectQuery( $cacheMonthKey."-r", $query, $timestamp_from, $timestamp_to);
            $r = $rObject->referrercount;

            $query = "SELECT count(*) as inquiries FROM #__jstats_keywords WHERE referrer_id IS NOT NULL AND timestamp >= $timestamp_from AND timestamp <= $timestamp_to";
            $rObject = js_Cache::cachedObjectQuery( $cacheMonthKey."-inquiries", $query, $timestamp_from, $timestamp_to);
            $inquiries = $rObject->inquiries;
            $row->inquiries = $inquiries;
            $r = $r-$inquiries;


			$tr += $r;
            $total_inquiries += $inquiries; 

			{ // not identified visitors
				$niv = (isset($niv_arr[$i])) ? $niv_arr[$i] : 0;
				$tniv += $niv;
			}

			{// unique not identified visitors
				$visitors_type = _JS_DB_IPADD__TYPE_NOT_IDENTIFIED_VISITOR;
				$JSDbSOV->selectNumberOfUniqueVisitors( $visitors_type, $cacheMonthKey."-univ", $buid, $timestamp_from, $timestamp_to, $univ );
				$tuniv += $univ;
			}

			// sums
			$sum  = $v + $b + $niv;
			$usum = $uv + $ub + $univ;
			$tsum  += $sum;

            $row->changed_at = mktime(0,0,0, $i, 1, $year);

            $row->month = $i;
            $row->uv = $uv;
           // $row->add = $add;
            $row->v = $v;
            $row->alternator = $alternator;
            $row->p = $p;
            $row->r = $r;
            $row->ub = $ub;
            $row->b = $b;
            $row->univ = $univ;
            $row->niv = $niv;
            $row->usum = $usum;
            $row->sum = $sum;

            if( ( $row->uv != 0 ) && ( $row->v != 0 ) ) {
              $row->vavg = number_format( round( ( $row->v / $row->uv ), 1), 1);
            }else{
              $row->vavg = '.';
            }

            $rows[] = $row;

			$alternator = 1 - $alternator;

            if( js_checkForTimeOut() ) return;

		}

        js_echoJSDebugInfo($prof->mark('End Loop'));


        if($chartEnabled)
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


		{ // Get the values for the totals line
			// RB: values acuired higher in this function are wrong - remove them
			// RB: change to new database method
			// AT: v3.0 changed to new method once again

            $timestamp_from = mktime(0, 0, 0, 1, 1, $year);
            $timestamp_to = mktime(23, 59, 59, 12, 31, $year);

			{ // get Unique visitors
				$visitors_type = _JS_DB_IPADD__TYPE_REGULAR_VISITOR;
				$JSDbSOV->selectNumberOfUniqueVisitors( $visitors_type, $cacheKey."-tuv", $buid, $timestamp_from, $timestamp_to, $tuv );
			}

			{ // get Unique bots
				$visitors_type = _JS_DB_IPADD__TYPE_BOT_VISITOR;
				$JSDbSOV->selectNumberOfUniqueVisitors( $visitors_type, $cacheKey."-tub", $buid, $timestamp_from, $timestamp_to, $tub );
			}

			{// unique not identified visitors
				$visitors_type = _JS_DB_IPADD__TYPE_NOT_IDENTIFIED_VISITOR;
				$JSDbSOV->selectNumberOfUniqueVisitors( $visitors_type, $cacheKey."-tuniv", $buid, $timestamp_from, $timestamp_to, $tuniv );
			}

			$tusum = $tuv + $tub + $tuniv;
		}


		$total = new stdClass();
			$total->month_or_year = $year;
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

        //$show_summarized = $this->engine->JSConf->show_summarized;

		//Total line (last line, sum line)
		$visits_average = '0.0';
		if( ( $total->tuv != 0 ) && ( $total->tv != 0 ) ) {
			$format_token = '%01.2f';
			$visits_average = sprintf($format_token, ( $total->tv / $total->tuv ));
		}
        $JSStatisticsTpl = new js_JSStatisticsCommonTpl();
        $JSTemplate = new js_JSTemplate();

        $this->assignRef("rows", $rows);
        $this->assignRef("total", $total);
        $this->assignRef("visits_average", $visits_average);
        $this->assignRef("JSStatisticsTpl", $JSStatisticsTpl);
        $this->assignRef("JSTemplate", $JSTemplate);
        $this->assignRef("chartView", $chartView);
        
		parent::display();
	}	
}
