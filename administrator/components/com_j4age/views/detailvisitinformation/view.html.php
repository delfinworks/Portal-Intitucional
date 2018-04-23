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
class j4ageViewDetailVisitInformation extends JView
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

		$JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
        $visit_id = JRequest::getVar('moreinfo');
        
		$query = ''
		. ' SELECT'
		. '   count(*) AS impresions,'
		. '   p.page,'
        . '   p.page_title,'
        . '   i.timestamp'
		. ' FROM'
		. '   #__jstats_impressions i'
		. '   LEFT JOIN #__jstats_pages AS p ON (p.page_id = i.page_id)'
		. ' WHERE'
		. '   i.visit_id = ' . $visit_id
		. ' GROUP BY'
		. '   p.page'
		;
		$JSDatabaseAccess->db->setQuery( $query );
		$impressions_result_arr = $JSDatabaseAccess->db->loadObjectList();


		$impressions_sum_all	= 0;
		if( count( $impressions_result_arr ) > 0 ) {
			foreach( $impressions_result_arr as $row ) {
            	$impressions_sum_all += $row->impresions;
        	}
		}


		$query = ''
		. ' SELECT'
		. '   p.page,'
		. '   p.page_title,'
        . '   i.timestamp'
		. ' FROM'
		. '   #__jstats_impressions i'
		. '   LEFT JOIN #__jstats_pages AS p ON (p.page_id = i.page_id)'
		. ' WHERE'
		. '   i.visit_id = ' . $visit_id
		;
		$JSDatabaseAccess->db->setQuery( $query );
		$path_result_arr = $JSDatabaseAccess->db->loadObjectList();


		$query = ''
		. ' SELECT'
		. '   v.visit_id,'
		. '   v.client_id,'
		. '   v.joomla_userid,'
		. '   v.changed_at'
		. ' FROM '
		. '   #__jstats_visits v'
		. ' WHERE'
		. '   v.visit_id = ' . $visit_id
		;
		$JSDatabaseAccess->db->setQuery( $query );
		$VisitObj = $JSDatabaseAccess->db->loadObject();

		if ($VisitObj->joomla_userid > 0) {
			$query = ''
			. ' SELECT'
			. '   ju.name AS joomla_username'
			. ' FROM '
			. '   #__users ju'
			. ' WHERE'
			. '   ju.id = ' . $VisitObj->joomla_userid
			;
			$JSDatabaseAccess->db->setQuery( $query );
			$VisitObj->joomla_username = $JSDatabaseAccess->db->loadResult();
		}

        /**
         * Refactoring required
         */
		$query = ''
		. ' SELECT'
		. '   a.ip            AS visitor_ip,'
		. '   a.nslookup      AS visitor_nslookup,'
		. '   a.code,'
		. '   sy.os_name      AS system,'
		. '   br.browser_name AS browser,'
		. '   c.client_type,'
        . '   c.visitor_id, '
        . '   c.client_id,'
        . '   i2nc.country'
		. ' FROM'
		. '   #__jstats_visits v'
        . '   LEFT JOIN #__jstats_clients AS c ON (c.client_id = v.client_id)'
        . '   LEFT OUTER JOIN #__jstats_ipaddresses AS a ON (a.ip = v.ip)'
        . '   LEFT OUTER JOIN #__jstats_systems AS sy ON (sy.os_id = c.os_id)'
        . '   LEFT OUTER JOIN #__jstats_browsers AS br ON (br.browser_id = c.browser_id)'
        . '   LEFT OUTER JOIN #__ip2nationCountries AS i2nc ON (i2nc.code = a.code)'
		. ' WHERE'
		. '   v.visit_id = ' . $visit_id
		;
		$JSDatabaseAccess->db->setQuery( $query );
		$VisitorObj = $JSDatabaseAccess->db->loadObject();

        //$resolvedName = null;

        //js_PluginManager::fireEventWithArgs( 'getCountryDetails', array($VisitorObj->code, &$resolvedName));
        /*if($resolvedName != null)
        {
           $VisitorObj->fullname = $resolvedName->fullname;
        }
        else
        {
           $VisitorObj->fullname = JTEXT::_( $VisitorObj->code ); 
        }*/

        $this->assignRef("VisitObj", $VisitObj);
        $this->assignRef("VisitorObj", $VisitorObj);
        $this->assignRef("impressions_sum_all", $impressions_sum_all);
        $this->assignRef("impressions_result_arr", $impressions_result_arr);
        $this->assignRef("path_result_arr", $path_result_arr);


		parent::display();
	}	
}
