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
require_once( dirname(__FILE__) .DS. '..'.DS. '..' .DS. 'libraries'.DS. 'template.html.php' );
require_once( dirname(__FILE__) .DS. '..'.DS. '..' .DS. 'libraries'.DS. 'statistics.common.php' );

/**
 * Hello View
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class j4ageViewConfiguration extends JView
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


        $JSConf = js_JSConf::getInstance();

        $statisticsCommon = new js_JSStatisticsCommon($JSConf);
        $this->assignRef("statisticsCommon", $statisticsCommon);

        $JSDbSOV = new js_JSDbSOV();
        $LastSummarizationDate = false;
        $JSDbSOV->getJSLastSummarizationDate($LastSummarizationDate);
        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();

        $enable_index_clients_useragent = $JSDatabaseAccess->hasTableIndexForColumn('#__jstats_clients', 'useragent');
        $enable_index_impressions_visit = $JSDatabaseAccess->hasTableIndexForColumn('#__jstats_impressions', 'visit_id');
        $enable_index_visits_changed_at = $JSDatabaseAccess->hasTableIndexForColumn('#__jstats_visits', 'changed_at');
        $enable_index_visits_ip         = $JSDatabaseAccess->hasTableIndexForColumn('#__jstats_visits', 'ip');
        
        $this->assignRef("enable_index_clients_useragent", $enable_index_clients_useragent);
        $this->assignRef("enable_index_impressions_visit", $enable_index_impressions_visit);
        $this->assignRef("enable_index_visits_changed_at", $enable_index_visits_changed_at);
        $this->assignRef("enable_index_visits_ip"        , $enable_index_visits_ip);

        $this->assignRef("JSConf", $JSConf);
        $this->assignRef("LastSummarizationDate", $LastSummarizationDate);

		parent::display();
	}	
}
