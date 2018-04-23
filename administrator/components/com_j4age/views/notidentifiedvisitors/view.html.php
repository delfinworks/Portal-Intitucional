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
class j4ageViewNotidentifiedVisitors extends JView
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

        require_once( dirname(__FILE__) .DS. '..'.DS. '..' .DS. 'database' .DS.'select.one.value.php' );
        require_once( dirname(__FILE__) .DS. '..'.DS. '..' .DS. 'database' .DS.'select.many.rows.php' );
        require_once( dirname(__FILE__) .DS. '..'.DS. '..' .DS. 'api' .DS. 'general.php' );

        $engine =  JoomlaStats_Engine::getInstance();
        $this->assignRef("engine", $engine);
        $this->assignRef("JSConf", $engine->JSConf);
        $this->assignRef("FilterTimePeriod", $engine->FilterTimePeriod);
        $statisticsCommon = new js_JSStatisticsCommonTpl();
        $this->assignRef("statisticsCommon", $statisticsCommon);

        $mainframe = JFactory::getApplication();

        $limit	= intval( $mainframe->getUserStateFromRequest( 'viewlistlimit', 'limit', $mainframe->getCfg( 'list_limit' ) ) );
        $limitstart	= intval( $mainframe->getUserStateFromRequest( 'viewlimitstart', 'limitstart', 0 ) );

        $timestamp_from = '';
        $timestamp_to = '';
        $this->FilterTimePeriod->getTimePeriodsDatesAsTimestamp( $timestamp_from, $timestamp_to );

        $NumberOfNotIdentifiedVisitors = 0;
        $JSApiGeneral = new js_JSApiGeneral();
        $JSApiGeneral->getVisitorsNumber( _JS_DB_IPADD__TYPE_NOT_IDENTIFIED_VISITOR, false, $timestamp_from, $timestamp_to, $NumberOfNotIdentifiedVisitors );

        jimport( 'joomla.html.pagination' );
        $pagination = new JPagination( $NumberOfNotIdentifiedVisitors, $limitstart, $limit );

        $JSDbSMR = new js_JSDbSMR();
        $rows = null;
        $JSDbSMR->selectNotIdentifiedVisitorsArr($pagination->limitstart, $pagination->limit, $timestamp_from, $timestamp_to, false, $rows );


        $this->assignRef("pagination", $pagination);
        $this->assignRef("rows", $rows);

		parent::display();
	}	
}
