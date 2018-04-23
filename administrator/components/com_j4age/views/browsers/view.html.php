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
class j4ageViewBrowsers extends JView
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

        $JSStatisticsCommonTpl = new js_JSStatisticsCommonTpl();

		$engine =  JoomlaStats_Engine::getInstance();
		$this->assignRef("engine", $engine);


        /** Make sure we have resolved all IP address information */
        $rows = array();
        IPInfoHelper::CheckIPAddresses($rows);

		$totalbrowsers 	= 0;
		$totalnmb		= 0;
		$totalmax 		= 0;

        $timestamp_from = null;
        $timestamp_to   = null;
        $this->engine->FilterTimePeriod->getTimePeriodsDatesAsTimestamp( $timestamp_from, $timestamp_to );

        $where = array();
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
        $where[] = $this->engine->JSDatabaseAccess->getConditionStringFromTimestamps($timestamp_from, $timestamp_to);
        
		$query = ''
		. ' SELECT'
		. '   count(*)   AS numbers,'
        . '   br.browser_name AS browser,'
        . '   br.* '
		. ' FROM'
		. '   #__jstats_clients AS c'
        . '   LEFT JOIN #__jstats_visits AS v ON (v.client_id = c.client_id)'
        . '   LEFT JOIN #__jstats_browsers AS br ON (br.browser_id = c.browser_id)'
        . ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' )
		. ' GROUP BY'
		. '   browser'
		. ' ORDER BY'
		. '   numbers DESC,'
		. '   browser ASC'
		;
		$this->engine->db->setQuery( $query );
		$rows = $this->engine->db->loadObjectList();

		if( count( $rows ) > 0 ) {
			foreach( $rows as $row ) {
            	$totalbrowsers++;
                $totalnmb += $row->numbers;

            	if( $row->numbers > $totalmax ) {
                    $totalmax = $row->numbers;
            	}
        	}
		}

		$total = 0;
		$max_value = 0;
		$sum_all_values = 0;
		if ( $rows ) {
			$total = count( $rows );

            foreach( $rows as $row ) {
                $sum_all_values   += $row->numbers;

                if( $row->numbers > $max_value ) {
                    $max_value = $row->numbers;
                }
            }
		}
        $this->assignRef("rows", $rows);
        $this->assignRef("sum_all_values", $sum_all_values);
        $this->assignRef("max_value", $max_value);
        $this->assignRef("total", $total);
        $this->assignRef("totalnmb", $totalnmb);
        $this->assignRef("JSStatisticsCommonTpl", $JSStatisticsCommonTpl);
        $JSUtil = new js_JSUtil();
        $JSSystemConst = new js_JSSystemConst();
        $this->assignRef("JSSystemConst", $JSSystemConst);
        $this->assignRef("JSUtil", $JSUtil);
        $this->assignRef("totalbrowsers", $totalbrowsers);

		parent::display();
	}	
}
