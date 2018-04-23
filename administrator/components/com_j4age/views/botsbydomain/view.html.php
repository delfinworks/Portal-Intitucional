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
class j4ageViewBotsByDomain extends JView
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
        require_once( dirname(__FILE__) .DS. '..'.DS. '..' .DS. 'database' .DS.'select.one.value.php' );

		$engine =  JoomlaStats_Engine::getInstance();
		$this->assignRef("engine", $engine);
        $this->assignRef("JSConf", $engine->JSConf);
        $this->assignRef("FilterTimePeriod", $engine->FilterTimePeriod);
        $statisticsCommon = new js_JSStatisticsCommonTpl();
        $this->assignRef("statisticsCommon", $statisticsCommon);

        $mainframe = JFactory::getApplication();

		$JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
		$result = '';

		$limit	    = intval( $mainframe->getUserStateFromRequest( 'viewlistlimit', 'limit', $mainframe->getCfg( 'list_limit' ) ) );
        $limitstart	= intval( $mainframe->getUserStateFromRequest( 'viewlimitstart', 'limitstart', 0 ) );

        $timestamp_from = null;
        $timestamp_to   = null;
        $this->FilterTimePeriod->getTimePeriodsDatesAsTimestamp( $timestamp_from, $timestamp_to );

		$JSDbSOV = new js_JSDbSOV();


		$query = ''
		. ' SELECT'
		. '   COUNT(*)   AS numbers,'
        . '   br.browser_id,'
        . '   br.browser_name as browser'
		. ' FROM'
		. '   #__jstats_clients c'
        . '   LEFT JOIN #__jstats_visits v ON (v.client_id = c.client_id)'
        . '   LEFT JOIN #__jstats_browsers br ON (br.browser_id = c.browser_id)'
		. ' WHERE'
		. '   c.client_type='._JS_DB_IPADD__TYPE_BOT_VISITOR
		. '   AND '.$JSDatabaseAccess->getConditionStringFromTimestamps( $timestamp_from, $timestamp_to)
		. ' GROUP BY'
		. '   c.browser_id'
		. ' ORDER BY'
		. '   numbers DESC,'
		. '   browser ASC'
		;
		$JSDatabaseAccess->db->setQuery( $query );
		$rows = $JSDatabaseAccess->db->loadObjectList();


		$total = 0;
		$max_value = 0;
		$sum_all_values = 0;
		if ( $rows ) {
			$total = count( $rows );

            foreach( $rows as $row ) {
                $sum_all_values   += $row->numbers;

                if( $row->numbers > $max_value ) {
                    $max_value = $row->numbers;
                }
            }
		}

		jimport( 'joomla.html.pagination' );
		//pagination is not dealed in right way ( a) MySQL 3.0 do not have nested queries (so unable to do this)  b) probably there was not gain from this - use profiler to check)
		$pagination = new JPagination( $total, $limitstart, $limit );

        $this->assignRef("pagination", $pagination);
        $this->assignRef("rows", $rows);
        $this->assignRef("sum_all_values", $sum_all_values);
        $this->assignRef("max_value", $max_value);
        $this->assignRef("total", $total);
		parent::display();
	}	
}
