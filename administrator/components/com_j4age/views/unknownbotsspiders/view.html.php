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
class j4ageViewUnknownBotsSpiders extends JView
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

        $mainframe = JFactory::getApplication();
		global $option;

        /** Make sure we have resolved all IP address information */
        $rows = array();
        IPInfoHelper::CheckIPAddresses($rows);

		$limit	= intval( $mainframe->getUserStateFromRequest( 'viewlistlimit', 'limit', $mainframe->getCfg( 'list_limit' )));
        $limitstart	= intval( $mainframe->getUserStateFromRequest( 'viewlimitstart', 'limitstart', 0 ) );

		$where = array();

		$date_from;
		$date_to;
		$this->engine->FilterTimePeriod->getTimePeriodsDatesAsTimestamp( $date_from, $date_to );

		$where[] = 'a.code = t.code';
        $where[] = 'v.client_id = c.client_id';
        $where[] = 'a.ip = v.ip';
        $where[] = 'br.browser_id = c.browser_id';
		$where[] = '(br.browser_id = 1024 or br.browser_id = 0)'; // All unknown bots & unknown clients
		$where[] = $this->engine->JSDatabaseAccess->getConditionStringFromTimestamps( $date_from, $date_to);

		/* mic: show only actual data (without already archived/purged)
		 * a.table : jstats_ipadresses
         * c.table : jstats_visits
         */
        if( !$this->engine->JSConf->include_summarized ) {
            $where[] = 'v.visit_id >= ' . $this->engine->buid();
        }

        // get total records
		$query = 'SELECT COUNT(*)'
		. ' FROM #__jstats_ipaddresses AS a, #__jstats_clients AS c, #__ip2nationCountries AS t, #__jstats_visits AS v, #__jstats_browsers AS br'
		. ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : ' ' )
		;
		$this->engine->db->setQuery( $query );
		$total = $this->engine->db->loadResult();

		jimport( 'joomla.html.pagination' );
		$pagination = new JPagination( $total, $limitstart, $limit );

		$query = 'SELECT DISTINCT(c.client_id), a.ip, a.nslookup, a.ip_exclude, a.ip_type, c.client_id, c.client_exclude,  t.code, t.code as tld, t.country, t.country as fullname, c.useragent, v.changed_at, c.client_type '
        . ' FROM #__jstats_ipaddresses AS a, #__jstats_clients AS c, #__ip2nationCountries AS t, #__jstats_visits AS v, #__jstats_browsers AS br'
		. ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' )
		. ' GROUP BY c.client_id ORDER BY v.changed_at DESC'
		;
		$this->engine->db->setQuery( $query, $pagination->limitstart, $pagination->limit );
		$rows = $this->engine->db->loadObjectList();

        $this->assignRef("rows", $rows);
        $this->assignRef("pagination", $pagination);

		parent::display();
	}	
}
