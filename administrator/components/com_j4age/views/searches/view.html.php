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
class j4ageViewSearches extends JView
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
        $this->assignRef("FilterDomain", $engine->FilterDomain);
        $statisticsCommon = new js_JSStatisticsCommonTpl();
        $this->assignRef("statisticsCommon", $statisticsCommon);
        $model = $this->getModel();
        $isKeywords = $model->isKeywords;

        $mainframe = JFactory::getApplication();

		$JSDatabaseAccess = js_JSDatabaseAccess::getInstance();

		$limit	    = intval( $mainframe->getUserStateFromRequest( 'viewlistlimit', 'limit', $mainframe->getCfg( 'list_limit' ) ) );
        $limitstart	= intval( $mainframe->getUserStateFromRequest( 'viewlimitstart', 'limitstart', 0 ) );

		$fromDate = null;
        $toDate = null;
        $this->FilterTimePeriod->getTimePeriodsDatesAsLong( $fromDate, $toDate );

		/*  NOTICE: visit_id was introduced in v3.0.0.372 - old data were NOT converted to this value, so it can not be used!! It is introduced to collect data for the future!! (not all data could be converted to new format, that is why now we duplicate data!)*/

        $where =  '     k.timestamp >= ' .$fromDate . ' '
                . ' AND k.timestamp <= ' . $toDate . ' '
		;

		$domain_str = $this->FilterDomain->getDomainString();
		if( ($isKeywords) && ($domain_str!='')  )
			$where .= ' AND b.searcher_name LIKE \''. $domain_str . '\'';


		if( $isKeywords ) {
			// Search Keyphrases
			/*  NOTICE: visit_id was introduced in v3.0.0.372 - old data were NOT converted to this value, so it can not be used!! It is introduced to collect data for the future!! (not all data could be converted to new format, that is why now we duplicate data!)*/
            //We use lower() to avoid the case sensetive entries - we can't used "keyword" as column name otherwise the grouping does not work
			$query = 'SELECT LOWER(TRIM(k.keywords)) as query, count(*) AS count'
			. ' FROM #__jstats_keywords AS k'
			. ' LEFT JOIN #__jstats_searchers AS b ON (k.searcher_id = b.searcher_id)'
			. ' WHERE'
			. $where
			. ' GROUP BY query'
			. ' ORDER BY count DESC, query'
			;
		} else {
			// Search Engines
			/*  NOTICE: visit_id was introduced in v3.0.0.372 - old data were NOT converted to this value, so it can not be used!! It is introduced to collect data for the future!! (not all data could be converted to new format, that is why now we duplicate data!)*/
			$query = 'SELECT b.searcher_name, count(*) AS count'
			. ' FROM #__jstats_keywords AS k'
			. ' LEFT JOIN #__jstats_searchers AS b ON (k.searcher_id = b.searcher_id)'
			. ' WHERE'
			. $where
			. ' GROUP BY b.searcher_name'
			. ' ORDER BY count DESC'
			;
		}

		$JSDatabaseAccess->db->setQuery( $query );
		$rows = $JSDatabaseAccess->db->loadObjectList();

		$total = 0;
		$max_value = 0;
		$sum_all_values = 0;
		if ( $rows ) {
			$total = count( $rows );

            foreach( $rows as $row ) {
                $sum_all_values   += $row->count;

                if( $row->count > $max_value ) {
                    $max_value = $row->count;
                }
            }
		}

		jimport( 'joomla.html.pagination' );
		//pagination is not dealed in right way ( a) MySQL 3.0 do not have nested queries (so unable to do this)  b) probably there was not gain from this - use profiler to check)
		$pagination = new JPagination( $total, $limitstart, $limit );

        $this->assignRef("isKeywords", $isKeywords);
        $this->assignRef("rows", $rows);
        $this->assignRef("pagination", $pagination);
        $this->assignRef("total", $total);
        $this->assignRef("max_value", $max_value);
        $this->assignRef("sum_all_values", $sum_all_values);

		parent::display();
	}	
}
