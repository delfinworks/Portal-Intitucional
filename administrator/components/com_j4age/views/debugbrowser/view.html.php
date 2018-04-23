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
require_once( dirname( __FILE__ ) .DS.'..'.DS.'..' .DS. 'libraries'.DS.'count.classes.php' );

jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.pagination' );

/**
 * Debug Browser View
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class j4ageViewDebugBrowser extends JView
{                    
	/**
	 * display method of Hello view
	 * @return void
	 **/
	function display($tpl = null)
	{
        $mainframe = JFactory::getApplication();

		$model	  =& $this->getModel();
		$text = '';

        js_JSToolBarMenu::jsButton('apply', JTEXT::_( 'Solve Conflicts' ) , 'applyParsedUseragents', 'debugbrowser', 'maintenance');
        //js_JSToolBarMenu::jsButton('js_exclude.png', JTEXT::_( 'Show IPs' ) , null, 'exclude', 'maintenance', 'exclude');

        JToolBarHelper::divider( );


		//show the sub content
        $engine =  JoomlaStats_Engine::getInstance();
        $this->assignRef("engine", $engine);

        $query = ''
        . ' SELECT *'
        . ' FROM'
        . '   #__jstats_browsers b'
        . ' WHERE'
        . '   b.browser_id > 0 ORDER BY b.browser_ordering'
        ;
        $this->engine->db->setQuery( $query );
        $browsertypes = $this->engine->db->loadObjectList();

        $browserMap = array();
        foreach($browsertypes as $browser)
        {
            $browserMap[$browser->browser_id] = $browser;
        }

        $query = ''
        . ' SELECT *'
        . ' FROM'
        . '   #__jstats_systems sy'
        . ' WHERE'
        . '   sy.os_id > 0 ORDER BY sy.os_ordering'
        ;
        $this->engine->db->setQuery( $query );
        $systemtypes = $this->engine->db->loadObjectList();

        $systemsMap = array();
        foreach($systemtypes as $system)
        {
            $systemsMap[$system->os_id] = $system;
        }

        $afilter = JRequest::getVar('afilter', '');
        $this->assignRef("afilter", $afilter);

        $where = array();
        /**
         * Append the subfilter option
         */
        if(!empty($afilter))
        {
            JError::raise(E_NOTICE, 0, "Only a subset of the original result set is shown based on your selection");
            $where[] = $afilter;
            /**
             * Change the time period to show everything
             */
            //$this->engine->FilterTimePeriod->setTimePeriod( 0, 0 );
        }

		$date_from;
		$date_to;
		$this->engine->FilterTimePeriod->getTimePeriodsDates( $date_from, $date_to );

        $objtype = JRequest::getInt('objtype', -1);
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

        $whereStr = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );

        $query = "SELECT DISTINCT c.useragent, c.browser_id, c.browser_version, c.client_id, c.os_id, '' as os_name FROM #__jstats_clients as c $whereStr
                  GROUP BY `useragent` ORDER BY `useragent` DESC";

		$this->engine->db->setQuery( $query );
		$rows = $this->engine->db->loadObjectList();

        $total = count( $rows );
        $limit		= intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', 50));
        $limitstart	= intval($mainframe->getUserStateFromRequest("viewlimitstart", 'limitstart', 0));

        if($limit < 50)
        {
            $limit = 50;
        }
        
        $pagination = new js_JPagination( $total, $limitstart, $limit );
        $rows = empty($rows)? array() : array_slice($rows, $pagination->limitstart, $pagination->limit);

        $countMatchinComment = 0;
        $countMatchByName = 0;
        $countFullMatchByName = 0;
        $conflicts = 0;

        $filteredRows = array();

		if ( $total > 0 )
        {
            foreach( $rows as $key=>$row )
            {
                $browser = null;
                if(isset($browserMap[$row->browser_id]))
                {
                    $browser = $browserMap[$row->browser_id];
                }
                if($browser)
                {
                    $row->browser_name = $browser->browser_name;
                }
                else
                {
                    $row->browser_name = 'Unknown';
                }

                $system = null;
                if(isset($systemsMap[$row->os_id]))
                {
                    $system = $systemsMap[$row->os_id];
                }
                if($system)
                {
                    $row->os_name = $system->os_name;
                }
                else
                {
                    $row->os_name = 'Unknown';
                }
                $version = "";

                $products = js_JSCountVisitor::parseUserAgent($row->useragent);

                $row->browserObj = js_JSCountVisitor::findBrowserFromUserAgent($this->engine->db, $browsertypes, $version , $row->useragent, $products);
                //$row->browserVersion = $version;

                $row->osObj = js_JSCountVisitor::findOsFromUserAgent($this->engine->db, $systemtypes, $row->useragent, $products);

                /**
                 * The column allows just 25 letters
                 */
                if(strlen($row->browserObj->version) > 25)
                {
                   $row->browserObj->version = substr($row->browserObj->version, 0, 25);
                }

                $row->conflictInBrowser = $row->browser_id != $row->browserObj->browser_id;
                $row->conflictInBrowserVersion = strcmp(trim($row->browserObj->version), trim($row->browser_version));
                $row->conflictInSystem = strcmp($row->os_id, $row->osObj->os_id);

                $row->isConflict = false;
                if($row->conflictInBrowser || $row->conflictInBrowserVersion || $row->conflictInSystem )
                {
                    $conflicts++;
                    $row->conflictId = $conflicts;
                    $row->isConflict = true;

                    $filteredRows[] = $row;
                }

            }
		}
        $showOnlyConflicts = ($limit > 500  && $total > 500);

        if($showOnlyConflicts)
        {
           $rows = $filteredRows;
           JError::raiseNotice( 0, "Only conflicts are shown!" );
        }

        JError::raiseNotice( 0, "$conflicts conflicts found within the current shown page compared to stored data. Press 'Solve Conflicts' to fix the displayed conflicts!" );

        if($conflicts > 0 )
        {
            JError::raiseNotice( 0, "Add a index to the 'useragent' column of jstats_clients DB table if you experience performance issues or display less items at once" );
        }

        $this->assignRef("browserMap", $browserMap);
        $this->assignRef("systemsMap", $systemsMap);
        $this->assignRef("browsertypes", $browsertypes);
        $this->assignRef("viewController", $this);
        $this->assignRef("rows", $rows);
        $this->assignRef("total", $total);
        $this->assignRef("pagination", $pagination);
        $this->assignRef("conflicts", $conflicts);

		parent::display();
	}
}


class js_JPagination extends JPagination
{
	/**
	 * Creates a dropdown box for selecting how many records to show per page
	 *
	 * @access	public
	 * @return	string	The html for the limit # input box
	 * @since	1.0
	 */
	function getLimitBox()
	{
        $mainframe = JFactory::getApplication();

		// Initialize variables
		$limits = array ();

		// Make the option list
		/*for ($i = 5; $i <= 30; $i += 5) {
			$limits[] = JHTML::_('select.option', "$i");
		}   */
		$limits[] = JHTML::_('select.option', '50');
        $limits[] = JHTML::_('select.option', '100');
        $limits[] = JHTML::_('select.option', '250');
        $limits[] = JHTML::_('select.option', '500');
        $limits[] = JHTML::_('select.option', '1000');
        $limits[] = JHTML::_('select.option', '2500');
        $limits[] = JHTML::_('select.option', '5000');
        $limits[] = JHTML::_('select.option', '10000');
		$limits[] = JHTML::_('select.option', '0', JText::_('all'));

		$selected = $this->_viewall ? 0 : $this->limit;

		// Build the select list
		if ($mainframe->isAdmin()) {
			$html = JHTML::_('select.genericlist',  $limits, 'limit', 'class="inputbox" size="1" onchange="submitform();"', 'value', 'text', $selected);
		} else {
			$html = JHTML::_('select.genericlist',  $limits, 'limit', 'class="inputbox" size="1" onchange="this.form.submit()"', 'value', 'text', $selected);
		}
		return $html;
	}
}
