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
jimport( 'joomla.html.pagination' );

/**
 * Hello View
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class j4ageViewVisits extends JView
{                    
	/**
	 * display method of Hello view
	 * @return void
	 **/
	function display($tpl = null)
	{
        JToolBarHelper::title( 'j4age'.': <small><small>[ ' . JTEXT::_( 'Visits' ) . ' ]</small></small>', 'js_js-logo.png' ); // this generate demand for css style 'icon-48-js_js-logo'

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

        /**
         *  Retrieve filter options to reduce the resultset to an specific subset
         */

        $mainframe = JFactory::getApplication();
		$engine =  JoomlaStats_Engine::getInstance();
		$this->assignRef("engine", $engine);

        /** Make sure we have resolved all IP address information */
        $rows = array();
        //IPInfoHelper::CheckIPAddresses($rows);

		$JSTemplate = new js_JSTemplate();
		$JSSystemConst = new js_JSSystemConst();
		$JSUtil = new js_JSUtil();

		$JSTemplate->jsLoadToolTip();

		$retval = '';
        $option = JRequest::getVar('option', '');

		$limit		= intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mainframe->getCfg('list_limit')));
        $limitstart	= intval($mainframe->getUserStateFromRequest("viewlimitstart", 'limitstart', 0));
		$search		= $mainframe->getUserStateFromRequest("search{$option}", 'search', '');
		$search		= $this->engine->db->getEscaped( trim( strtolower( $search ) ) );


		$where	= array();
        $objtype = JRequest::getInt('objtype', 1);
        JRequest::setVar('objtype', $objtype);
        
        if($objtype > -1)
        {
            //only display specific clients
            if($objtype == 3)
            {
                $objtype = _JS_DB_IPADD__TYPE_BOT_VISITOR;
                $where[] = "c.client_type = $objtype and c.browser_id = 1024";
            }
            else
            {
                $where[] = "c.client_type = $objtype";
            }
        }

        $afilter = JRequest::getVar('afilter', '');

        /**
         * Append the subfilter option
         */
        if(!empty($afilter))
        {
            JError::raise(E_NOTICE, 0, "Only a subset of the original result set is shown based on your selection");
            $where[] = ' ' . $afilter;
            /**
             * Change the time period to show everything
             */
            //$this->engine->FilterTimePeriod->setTimePeriod( 0, 0 );
        }

        $date_from = 0;
        $date_to = 0;

        $this->engine->FilterTimePeriod->getTimePeriodsDatesAsTimestamp( $date_from, $date_to );
        $where[] = $this->engine->JSDatabaseAccess->getConditionStringFromTimestamps( $date_from, $date_to);


		//RB: todo: add also username to the search >> mic: table users is NOT in query!! @todo: add users table
		if( $search ) {
			$where[] = '('
			. ' INET_NTOA(a.ip) LIKE \'%' . $search . '%\''
			. ' OR LOWER(br.browser_name) LIKE \'%' . $search . '%\''
			. ' OR LOWER(sy.os_name) LIKE \'%' . $search . '%\''
			. ' OR LOWER(a.nslookup) LIKE \'%' . $search . '%\''
			. ' OR LOWER(a.code) LIKE \'%' . $search . '%\''
			. ' OR LOWER(i2nc.country) LIKE \'%' . $search . '%\''
			. ' OR ju.name LIKE \'%' . $search . '%\''
			.')';
			//RB: is LOWER needed? 'like' should check case insensitive? mic: NO, like IS case sensitive!
		}


        $chartView =  js_getView('amline', 'html');
        if(!empty($chartView))
        {
            $query = "SELECT v.changed_at, count(*) as count
             FROM #__jstats_visits as v
             LEFT OUTER JOIN #__jstats_ipaddresses AS a ON (a.ip = v.ip)
             LEFT OUTER JOIN #__jstats_clients AS c ON (c.client_id = v.client_id) ";
            if($search)
            {
            $query .= "
             LEFT OUTER JOIN #__users AS ju ON (ju.id = v.joomla_userid)
             LEFT OUTER JOIN #__jstats_browsers AS br ON (br.browser_id = c.browser_id)
             LEFT OUTER JOIN #__jstats_systems AS sy ON (sy.os_id = c.os_id)
             LEFT OUTER JOIN #__ip2nationCountries AS i2nc ON (i2nc.code = a.code)
            ";
            }
            $chartView->createGraphByQuery($query, 'count', array('title'=> JTEXT::_( 'Visits' ), 'bullet'=> 'square_outlined', 'where' => $where ));
        }

        js_profilerMarker('retriev data');


        // select total
        /**
         * Dropped Query to retrieve the total columns - performing 2 queries within the DB does not improve the performance at all
         */

		$query  = 'SELECT a.ip AS aid, a.ip, a.ip_type,  a.ip_exclude, a.code, a.nslookup, c.*, i2nc.country, sy.os_name as system, sy.os_img, br.browser_name, br.browser_img, c.browser_version, v.joomla_userid, ju.name AS joomla_username, v.changed_at, v.visit_id'
		. ' FROM #__jstats_clients AS c'
		. ' LEFT OUTER JOIN #__jstats_visits AS v ON (v.client_id = c.client_id)'
        . ' LEFT OUTER JOIN #__users AS ju ON (ju.id = v.joomla_userid)'   //joining with #__users table make this query 5% slower
        . ' LEFT OUTER JOIN #__jstats_ipaddresses AS a ON (a.ip = v.ip)'
        . ' LEFT OUTER JOIN #__jstats_browsers AS br ON (br.browser_id = c.browser_id)'
        . ' LEFT OUTER JOIN #__jstats_systems AS sy ON (sy.os_id = c.os_id)'
        . ' LEFT OUTER JOIN #__ip2nationCountries AS i2nc ON (i2nc.code = a.code)'
		. ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' )
		. ' ORDER BY v.changed_at DESC'
		;

        //$rows = js_Cache::temporaryCachedQuery(null, $query, $date_from, $date_to);
        

		$this->engine->db->setQuery( $query);//, $pagination->limitstart, $pagination->limit );
		$rows = $this->engine->db->loadObjectList();
        

        IPInfoHelper::CheckIPAddresses($rows);

        $total = count($rows);

        js_profilerMarker('data retrieved');

        $pagination = new JPagination( $total, $limitstart, $limit );
        $rows = empty($rows)? array() : array_slice($rows, $pagination->limitstart, $pagination->limit);

        $this->assignRef("limitstart", $limitstart);
        $this->assignRef("rows", $rows);
        $this->assignRef("total", $total);
        $this->assignRef("pagination", $pagination);
        $this->assignRef("JSSystemConst", $JSSystemConst);
        $this->assignRef("JSUtil", $JSUtil);
        $this->assignRef("afilter", $afilter);
        $this->assignRef("chartView", $chartView);

		parent::display();
        js_profilerMarker('after display');
	}
}

