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
class j4ageViewSystems extends JView
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

        require_once( dirname(__FILE__) .DS. '..'.DS. '..' .DS. 'database' .DS.'db.constants.php' );
        require_once( dirname(__FILE__) .DS. '..'.DS. '..' .DS. 'api' .DS. 'general.php' );

		$engine =  JoomlaStats_Engine::getInstance();
        $this->assignRef("engine", $engine);
        $this->assignRef("JSConf", $engine->JSConf);
        $this->assignRef("FilterTimePeriod", $engine->FilterTimePeriod);
        $statisticsCommon = new js_JSStatisticsCommonTpl();
        $this->assignRef("statisticsCommon", $statisticsCommon);

        $date_from = '';
        $date_to = '';
        $this->FilterTimePeriod->getTimePeriodsDates( $date_from, $date_to );

        $timestamp_from = null;
        $timestamp_to   = null;
        $this->engine->FilterTimePeriod->getTimePeriodsDatesAsTimestamp( $timestamp_from, $timestamp_to );

        $objtype = JRequest::getInt('objtype', -1);
        JRequest::setVar('objtype', $objtype);

        $result_arr = array();
        $JSApiGeneral = new js_JSApiGeneral();
        $JSApiGeneral->getOperatingSystemVisistsArr( $timestamp_from, $timestamp_to, false, '', $result_arr, $objtype );


        $sum_all_system_visits	= 0;
        $max_system_visits		= 0;

        if( count( $result_arr ) > 0 ) {
            foreach( $result_arr as $row ) {
                $sum_all_system_visits += $row->os_visits;

                if( $row->os_visits > $max_system_visits ) {
                    $max_system_visits = $row->os_visits;
                }
            }
        }

        $ostype_name_arr = array();
        {
            $__jstats_ostype = unserialize(_JS_DB_TABLE__OSTYPE);
            foreach( $__jstats_ostype as $ostype )
                $ostype_name_arr[] = $ostype['ostype_name'];
        }

        $this->assignRef("sum_all_system_visits", $sum_all_system_visits);
        $this->assignRef("max_system_visits", $max_system_visits);
        $this->assignRef("ostype_name_arr", $ostype_name_arr);
        $this->assignRef("result_arr", $result_arr);

		parent::display();
	}	
}
