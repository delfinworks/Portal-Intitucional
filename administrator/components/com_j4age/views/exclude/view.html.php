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

require_once( dirname( __FILE__ ) .DS.'..'.DS.'..' .DS. 'libraries' .DS. 'statistics.common.html.php' );
require_once( dirname(__FILE__) .DS. '..'.DS. '..' .DS. 'libraries' .DS. 'base.classes.php' );
require_once( dirname(__FILE__) .DS. '..'.DS. '..' .DS. 'libraries' .DS. 'template.html.php' );
require_once( dirname(__FILE__) .DS. '..'.DS. '..' .DS. 'database' .DS. 'access.php' );

jimport( 'joomla.application.component.view' );

/**
 * Hello View
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class j4ageViewExclude extends JView
{                    
	/**
	 * display method of Hello view
	 * @return void
	 **/
	function display($tpl = null)
	{
        if($tpl == null)
        {
            $tpl = JRequest::getVar( 'tpl', $tpl );
            if(empty($tpl))
            {
                $tpl = null;
            }
        }
        $mainframe = JFactory::getApplication();

		global $option;


        $StatisticsMenu = array();
        $StatisticsMenu['excludeClient'] = array( 'label' => "".JTEXT::_( 'Clients' ), 'view' => 'exclude', 'type' => 'showExclusionForClients', 'controller' => 'maintenance', 'tpl' => 'exclude_clients' );
        $StatisticsMenu['excludeIP'] = array( 'label' => "".JTEXT::_( 'IP Addresses' ), 'view' => 'exclude', 'type' => 'showExclusionForIPs', 'controller' => 'maintenance', 'tpl' => '' );

        js_JSSubToolBarMenu::addMenuItems($StatisticsMenu);
        echo js_JSSubToolBarMenu::render();

        echo "<br/>";

		$JSDatabaseAccess = js_JSDatabaseAccess::getInstance();

		$limit		= $mainframe->getUserStateFromRequest( 'viewlistlimit', 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$search		= $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search		= $JSDatabaseAccess->db->getEscaped( trim( strtolower( $search ) ) );

        $JSUtil = new js_JSUtil();
        $this->assignRef("JSUtil", $JSUtil);

        if($tpl == null)
        {
            js_JSToolBarMenu::jsButton('unpublish', JTEXT::_( 'Exclude' ) , 'doIpExclude', 'exclude', 'maintenance');
            js_JSToolBarMenu::jsButton('publish', JTEXT::_( 'Include' ) , 'doIpInclude', 'exclude', 'maintenance');

            JToolBarHelper::divider( );

           $this->loadIPAddresses($JSDatabaseAccess, $limit, $limitstart, $search);
        }
        else if($tpl == "exclude_clients")
        {
            $params = array('tpl' => 'exclude_clients');
            js_JSToolBarMenu::jsButton('unpublish', JTEXT::_( 'Exclude' ) , 'excludeClients', 'exclude', 'maintenance', false, false, $params);
            js_JSToolBarMenu::jsButton('publish', JTEXT::_( 'Include' ) , 'includeClients', 'exclude', 'maintenance', false, false, $params);

            JToolBarHelper::divider( );        
            $this->loadClients($JSDatabaseAccess, $limit, $limitstart, $search);
        }
		parent::display($tpl);
	}

    function loadClients(&$JSDatabaseAccess, $limit, $limitstart, $search)
    {
		$where = array();
		if( isset( $search ) && strlen($search) > 0 ) {
			$where[] = '(c.useragent LIKE \'%' . $search . '%\''
			. ' OR sy.os_name LIKE \'%' . $search . '%\''
			. ' OR br.browser_name LIKE \'%' . $search . '%\''
			. ' OR c.browser_version LIKE \'%' . $search . '%\''
			.')';
		}
        else
        {
            $where[] = "(c.client_exclude > 0)";
        }

		$query= 'SELECT COUNT(*)'
		. ' FROM #__jstats_clients c'
        . ' INNER JOIN #__jstats_browsers AS br ON br.browser_id = c.browser_id'
        . ' INNER JOIN #__jstats_systems AS sy ON sy.os_id = c.os_id'
		. ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '')
		;

		$JSDatabaseAccess->db->setQuery( $query );
		$total = $JSDatabaseAccess->db->loadResult();
		if( $JSDatabaseAccess->db->getErrorNum() ) {
			echo $JSDatabaseAccess->db->stderr();
			return false;
		}

		jimport( 'joomla.html.pagination' );
		$pagination = new JPagination( $total, $limitstart, $limit );

        $query = 'SELECT count(c.client_id) as visits, MAX(v.changed_at) as changed_at, c.client_id, c.useragent, c.client_type, sy.os_name, br.browser_name, c.browser_version, c.client_exclude'
        . ' FROM #__jstats_clients AS c'
        . ' INNER JOIN #__jstats_browsers AS br ON br.browser_id = c.browser_id'
        . ' INNER JOIN #__jstats_systems AS sy ON sy.os_id = c.os_id'
        . ' LEFT OUTER JOIN #__jstats_visits AS v ON v.client_id = c.client_id'
        . ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' )
        . ' GROUP BY c.client_id ORDER BY client_exclude DESC, changed_at DESC, client_id DESC'
        ;

		$JSDatabaseAccess->db->setQuery( $query, $pagination->limitstart, $pagination->limit );
		$rows = $JSDatabaseAccess->db->loadObjectList();
		if( $JSDatabaseAccess->db->getErrorNum() ) {
			echo $JSDatabaseAccess->db->stderr();
			return false;
		}

        $this->assignRef("rows", $rows);
        $this->assignRef("pagination", $pagination);
        $this->assignRef("search", $search);
    }

    function loadIPAddresses(&$JSDatabaseAccess, $limit, $limitstart, $search)
    {
		$where = array();

		if( isset( $search ) && strlen($search) > 0 ) {
			$where[] = '(a.ip LIKE \'%' . $search . '%\''
			. ' OR a.nslookup LIKE \'%' . $search . '%\''
//			. ' OR browser LIKE \'%' . $search . '%\''
//			. ' OR system LIKE \'%' . $search . '%\''
			.')';
		}
        else
        {
            $where[] = "(a.ip_exclude > 0)";
        }

		$query= 'SELECT COUNT(*)'
		. ' FROM #__jstats_ipaddresses as a'
		. ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '')
		;
		$JSDatabaseAccess->db->setQuery( $query );
		$total = $JSDatabaseAccess->db->loadResult();
		if( $JSDatabaseAccess->db->getErrorNum() ) {
			echo $JSDatabaseAccess->db->stderr();
			return false;
		}

		jimport( 'joomla.html.pagination' );
		$pagination = new JPagination( $total, $limitstart, $limit );

		$query = 'SELECT a.ip, a.nslookup, a.ip_exclude, a.ip_type'
		. ' FROM #__jstats_ipaddresses as a'
		. ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' )
		. ' ORDER BY a.ip_exclude DESC, a.ip DESC'
		;
		$JSDatabaseAccess->db->setQuery( $query, $pagination->limitstart, $pagination->limit );
		$rows = $JSDatabaseAccess->db->loadObjectList();
		if( $JSDatabaseAccess->db->getErrorNum() ) {
			echo $JSDatabaseAccess->db->stderr();
			return false;
		}

        $this->assignRef("rows", $rows);
        $this->assignRef("pagination", $pagination);
        $this->assignRef("search", $search);
    }
}
