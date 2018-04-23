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
class j4ageViewBots extends JView
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

        if(!$JSDatabaseAccess->executionTimeAvailable() )
        {
           return; //No time left to show the display
        }
		$result = '';

		$limit	    = intval( $mainframe->getUserStateFromRequest( 'viewlistlimit', 'limit', $mainframe->getCfg( 'list_limit' ) ) );
        $limitstart	= intval( $mainframe->getUserStateFromRequest( 'viewlimitstart', 'limitstart', 0 ) );

		$timestamp_from = null;
		$timestamp_to   = null;
		$this->FilterTimePeriod->getTimePeriodsDatesAsTimestamp( $timestamp_from, $timestamp_to );

		$buid = 0;
		$JSDbSOV = new js_JSDbSOV();
		$JSDbSOV->getBuid( $buid );

		$query = ''
		. ' SELECT'
		. '   a.code, a.nslookup, a.ip_type,'
		. '   br.browser_name, c.browser_version,'
		. '   c.useragent, v.changed_at, v.visit_id, c.client_id, a.ip, a.ip_exclude, c.client_exclude, c.client_type'
		. ' FROM'
		. '   #__jstats_clients c'
		. '   LEFT JOIN #__jstats_visits v ON (v.client_id = c.client_id)'
        . '   LEFT JOIN #__jstats_ipaddresses a ON (a.ip = v.ip)'
        . '   LEFT JOIN #__jstats_browsers br ON (br.browser_id = c.browser_id)'
		. ' WHERE'
		. '    c.client_type='._JS_DB_IPADD__TYPE_BOT_VISITOR
		//.     ( ($domain_str!='') ? (' AND br.browser_name LIKE \'' . $domain_str . '\'') : '' )
		. '   AND '.$JSDatabaseAccess->getConditionStringFromTimestamps( $timestamp_from, $timestamp_to)
		.     (($this->JSConf->include_summarized) ? ('') : (' AND v.visit_id>='.$buid) )
		. ' ORDER BY'
		. '   v.changed_at DESC'
		;
		$JSDatabaseAccess->db->setQuery( $query );
		$rows = $JSDatabaseAccess->db->loadObjectList();
        js_echoJSDebugInfo('Loaded bot list');
        //IPInfoHelper::CheckIPAddresses($rows);

		$total = 0;
		if ( $rows ) {
			$total = count( $rows );
		}
        //if limit equals 0 it means show all!
        if($limit == 0)
        {
           $limit = $total-$limitstart;
        }

		if ( $total > 0 ) {
			for ($i=$limitstart; ($i<$total && $i<($limitstart+$limit)); $i++) {
				$vid = $rows[$i]->visit_id;

				$query = 'SELECT count(*) AS pages_nbr'
				. ' FROM #__jstats_impressions i'
				. ' WHERE i.visit_id = ' . $vid
                //. '   AND '.$JSDatabaseAccess->getConditionStringFromTimestamps( $timestamp_from, $timestamp_to)
				;
				$JSDatabaseAccess->db->setQuery( $query );
				$pages_nbr = $JSDatabaseAccess->db->loadResult();
				$rows[$i]->pages_nbr = $pages_nbr;
                if(!$JSDatabaseAccess->executionTimeAvailable() )
                {
                   break; //We avoid timeouts by executiong to many enquires
                }
			}
		}

		jimport( 'joomla.html.pagination' );
		//pagination is not dealed in right way ( a) MySQL 3.0 do not have nested queries (so unable to do this)  b) probably there was not gain from this - use profiler to check)
		$pagination = new JPagination( $total, $limitstart, $limit );
    
        $this->assignRef("pagination", $pagination);
        $this->assignRef("rows", $rows);
        $this->assignRef("total", $total);

        parent::display();
	}	
}
