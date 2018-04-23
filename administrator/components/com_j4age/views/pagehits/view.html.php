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
class j4ageViewPageHits extends JView
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

        require_once( dirname(__FILE__) .DS. '..'.DS. '..' .DS. 'database' .DIRECTORY_SEPARATOR. 'select.one.value.php' );
        require_once( dirname(__FILE__) .DS. '..'.DS. '..' .DS. 'database' .DIRECTORY_SEPARATOR. 'select.many.rows.php' );
        require_once( dirname(__FILE__) .DS. '..'.DS. '..' .DS. 'api' .DIRECTORY_SEPARATOR. 'general.php' );
        require_once( dirname(__FILE__) .DS. '..'.DS. '..'.DS. 'libraries' .DS. 'filters.php' );

		$engine =  JoomlaStats_Engine::getInstance();
        $statisticsCommon = new js_JSStatisticsCommonTpl();
        $this->assignRef("engine", $engine);
        $this->assignRef("statisticsCommon", $statisticsCommon);

        $mainframe = JFactory::getApplication();

        // ###  Filters
        $date_from = '';
        $date_to = '';
        $this->engine->FilterTimePeriod->getTimePeriodsDates( $date_from, $date_to );

        $timestamp_from = null;
        $timestamp_to   = null;
        $this->engine->FilterTimePeriod->getTimePeriodsDatesAsTimestamp( $timestamp_from, $timestamp_to );

        $this->engine->FilterSearch->show_search_filter = false;
        $this->engine->FilterDomain->show_domain_filter = false;
        $vid = '';
        $moreinfo = '';

        $limit	= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mainframe->getCfg( 'list_limit' )));
        $limitstart	= intval( $mainframe->getUserStateFromRequest( "viewlimitstart", 'limitstart', 0 ) );



        // ###  Content
        $nbr_visited_pages 			= 0;
        $sum_all_pages_impressions	= 0;
        $max_page_impressions		= 0;
        $result_arr					= array();


        $objtype = JRequest::getInt('objtype', 1);
        JRequest::setVar('objtype', $objtype);
        
        $sums = null;
        $JSApiGlobal = new js_JSApiGeneral();
        $JSApiGlobal->getPagesImpressionsSums( $timestamp_from, $timestamp_to, $sums, $objtype);

        $nbr_visited_pages = $sums->nbr_visited_pages;
        $sum_all_pages_impressions = $sums->sum_all_pages_impressions;
        $max_page_impressions = $sums->max_page_impressions;

        $total = $nbr_visited_pages;
        jimport( 'joomla.html.pagination' );
        $pagination = new JPagination( $total, $limitstart, $limit );

        $this->assignRef("pagination", $pagination);

        js_JSDbSMR::getPagesImpressionsArr($pagination->limitstart, $pagination->limit, $timestamp_from, $timestamp_to, $result_arr, $objtype );

        $this->assignRef("total", $total);
        $this->assignRef("max_page_impressions", $max_page_impressions);
        $this->assignRef("sum_all_pages_impressions", $sum_all_pages_impressions);
        $this->assignRef("sums", $sums);
        $this->assignRef("result_arr", $result_arr);
        $this->assignRef("nbr_visited_pages", $nbr_visited_pages);


		parent::display();
	}	
}
