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
class j4ageViewTools extends JView
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

        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();

        $query = 'SELECT count(*)'
        . ' FROM #__jstats_impressions'
        ;
        $JSDatabaseAccess->db->setQuery( $query );
        $pr_sum = $JSDatabaseAccess->db->loadResult();

        $JSDbSOV = new js_JSDbSOV();
        $LastSummarizationDate = false;
        $JSDbSOV->getJSLastSummarizationDate($LastSummarizationDate);
        $plugins =& js_PluginManager::loadAvailablePlugins(-1, true);;

        $this->assignRef("plugins", $plugins);
        $this->assignRef("pr_sum", $pr_sum);
        $this->assignRef("LastSummarizationDate", $LastSummarizationDate);
        $this->assignRef("JSConf", $JSConf);
        $this->assignRef("periodIndays", JRequest::getVar( 'periodIndays', 730 ));

		parent::display();
	}	
}
