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

/**
 * Hello View
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class j4ageViewReferrers extends JView
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

        require_once( dirname(__FILE__) .DS. '..'.DS. '..' .DS. 'database' .DS.'access.php' );

        $engine =  JoomlaStats_Engine::getInstance();
        $this->assignRef("engine", $engine);
        $this->assignRef("JSConf", $engine->JSConf);
        $this->assignRef("FilterTimePeriod", $engine->FilterTimePeriod);
        $this->assignRef("FilterSearch", $engine->FilterSearch);
        $this->assignRef("FilterDomain", $engine->FilterDomain);
        $statisticsCommon = new js_JSStatisticsCommonTpl();
        $this->assignRef("statisticsCommon", $statisticsCommon);
        $model = $this->getModel();
        $byPage = $model->isByPage;

        $mainframe = JFactory::getApplication();

		$JSDatabaseAccess = js_JSDatabaseAccess::getInstance();


		$limit	    = intval( $mainframe->getUserStateFromRequest( 'viewlistlimit', 'limit', $mainframe->getCfg( 'list_limit' ) ) );
        $limitstart	= intval( $mainframe->getUserStateFromRequest( 'viewlimitstart', 'limitstart', 0 ) );

		$day   = '%';
		$month = '%';
		$year  = '%';
		$this->FilterTimePeriod->getDMY( $day, $month, $year );

        $timestamp_from;
        $timestamp_to;
        $this->engine->FilterTimePeriod->getTimePeriodsDatesAsTimestamp( $timestamp_from, $timestamp_to );
        //JError::raise(E_NOTICE, 0, "$date_from - $date_to");



		/*  NOTICE: visit_id was introduced in v3.0.0.372 - old data were NOT converted to this value, so it can not be used!! It is introduced to collect data for the future!! (not all data could be converted to new format, that is why now we duplicate data!)*/
        $where = '';
		$domain_str = $this->FilterDomain->getDomainString();
		if( ($byPage) && ($domain_str!='')  )
			$where .= ' AND domain LIKE \'' . $domain_str . '\'';


		if( $byPage ) {
			// 'Referrers by page'
			/*  NOTICE: visit_id was introduced in v3.0.0.372 - old data were NOT converted to this value, so it can not be used!! It is introduced to collect data for the future!! (not all data could be converted to new format, that is why now we duplicate data!)*/
			$query = ''
			. ' SELECT'
			. '   COUNT(*) AS counter,'
			. '   referrer'
			. ' FROM'
            . '   #__jstats_referrer r'
            . ' LEFT OUTER JOIN #__jstats_keywords k ON k.referrer_id = r.refid'
			. ' WHERE'
			. " r.timestamp >= $timestamp_from AND r.timestamp <= $timestamp_to"
            . " AND k.referrer_id IS NULL"
            . $where
			. ' GROUP BY'
			. '   r.referrer'
			. ' ORDER BY'
			. '   counter DESC'
			;
		} else {
			// 'Referrers by domain'
			/*  NOTICE: visit_id was introduced in v3.0.0.372 - old data were NOT converted to this value, so it can not be used!! It is introduced to collect data for the future!! (not all data could be converted to new format, that is why now we duplicate data!)*/
			$query = ''
			. ' SELECT'
			. '   COUNT(*) AS counter,'
			. '   domain'
			. ' FROM'
			. '   #__jstats_referrer r'
            . ' LEFT OUTER JOIN #__jstats_keywords k ON k.referrer_id = r.refid'
			. ' WHERE'
            . " r.timestamp >= $timestamp_from AND r.timestamp <= $timestamp_to "
            . " AND k.referrer_id IS NULL"
			.     $where
			. ' GROUP BY'
			. '   r.domain'
			. ' ORDER BY'
			. '   counter DESC'
			;
		}
        js_echoJSDebugInfo("Before Query".$query);
        
		$JSDatabaseAccess->db->setQuery( $query );
		$rows = $JSDatabaseAccess->db->loadObjectList();

        js_echoJSDebugInfo("After Query".$query);

		$total = 0;
		$max_value = 0;
		$sum_all_values = 0;
		if ( $rows ) {
			$total = count( $rows );

            foreach( $rows as $row ) {
                $sum_all_values   += $row->counter;

                if( $row->counter > $max_value ) {
                    $max_value = $row->counter;
                }
            }
		}

		jimport( 'joomla.html.pagination' );
		//pagination is not dealed in right way ( a) MySQL 3.0 do not have nested queries (so unable to do this)  b) probably there was not gain from this - use profiler to check)
		$pagination = new JPagination( $total, $limitstart, $limit );

        $this->assignRef("pagination", $pagination);
        $this->assignRef("byPage", $byPage);
        $this->assignRef("rows", $rows);
        $this->assignRef("total", $total);
        $this->assignRef("max_value", $max_value);
        $this->assignRef("sum_all_values", $sum_all_values);
		parent::display();
	}	
}
