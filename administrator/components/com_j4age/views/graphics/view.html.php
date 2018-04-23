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
class j4ageViewGraphics extends JView
{                    
	/**
	 * display method of Hello view
	 * @return void
	 **/
	function display($tpl = null)
	{
        JToolBarHelper::title( 'j4age'.': <small><small>[ ' . JTEXT::_( 'Charts' ) . ' ]</small></small>', 'js_js-logo.png' );
        echo JoomlaStats_Engine::renderFilters(false, false);
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
        echo JoomlaStats_Engine::renderFilters(false, false, true);
        
        require_once( dirname(__FILE__) .DS. '..'.DS. '..' .DS. 'database' .DS.'select.one.value.php' );
        require_once( dirname(__FILE__) .DS. '..'.DS. '..' .DS. 'api' .DS. 'general.php' );
        require_once( dirname(__FILE__) .DS. '..'.DS. '..' .DS. 'libraries'.DS. 'template.html.php' );

		$engine =  JoomlaStats_Engine::getInstance();
		$this->assignRef("engine", $engine);

        $date_from;
        $date_to;

        $this->engine->FilterTimePeriod->getTimePeriodsDatesAsTimestamp( $date_from, $date_to );
        //JError::raise(E_NOTICE, 0, "$date_from - $date_to");

        $timestamp_from = $date_from;
        $timestamp_to = $date_to;

        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();

        $where[] = $JSDatabaseAccess->getConditionStringFromTimestamps( $date_from, $date_to);

        $date_from = date('j-m-Y',$date_from);
        $date_to = date('j-m-Y',$date_to);;
        echo "<div style='text-align: center'><h2>$date_from to $date_to</h2></div>";

        $conditionStringFromDates = implode( ' AND ', $where );



		$width		= 440;
		$height		= 300;

		$query = "SELECT COUNT(r.referrer) AS referrer, MAX(TRIM(r.domain)) AS domain, CONCAT('http://', MAX(TRIM(r.domain))) AS url, r.timestamp
		 FROM #__jstats_referrer as r
		 LEFT OUTER JOIN #__jstats_keywords as k on k.referrer_id = r.refid
		 WHERE k.referrer_id IS NULL AND r.timestamp >= $timestamp_from AND r.timestamp <= $timestamp_to
		 GROUP BY r.domain
		 ORDER BY r.referrer DESC
		 LIMIT 10"
		;
		$JSDatabaseAccess->db->setQuery( $query );
		$rows = $JSDatabaseAccess->db->loadObjectList();//loadRowList();
        $chartView =  js_getView('ampie', 'html');
        if(!empty($chartView))
        {
            $chart =& $chartView->createChart();
            //$chart =& $chartView->charts[0];
            $chart->appendSlices($rows, 'domain', 'referrer', 'url', null, array('title' => JTEXT::_( 'Top 10' ) . ' ' . JTEXT::_( 'Referrers' )));
              echo $chartView->render();
        }


        // show 10 top ip.addressess
        $rows = null;
        $query = "SELECT COUNT(v.ip) AS ipvisits, MAX(a.code) AS code, v.ip, a.nslookup
         FROM #__jstats_visits as v,#__jstats_clients as c,  #__jstats_ipaddresses as a
         WHERE a.ip = v.ip AND v.client_id = c.client_id AND c.client_type = 1 AND $conditionStringFromDates
         GROUP BY v.ip
         ORDER BY ipvisits DESC
         LIMIT 10"
        ;
        $JSDatabaseAccess->db->setQuery( $query );
        $rows = $JSDatabaseAccess->db->loadObjectList();

        $dates = array();

        foreach( $rows as $row )
        {
            $data = new stdClass();
            $data->ipvisits = $row->ipvisits;
            $data->country = $row->code;
            $data->ip = $row->ip;
            $data->label = (empty($row->nslookup) ? long2ip($row->ip) : $row->nslookup) . ' (' . $row->code . ')';
            $dates[] = $data;
        }

        if(!empty($chartView))
        {
            $chart =& $chartView->createChart();
            $chart->appendSlices($dates, 'label', 'ipvisits', null, null, array('title' => JTEXT::_( 'Top 10' ) . ' ' . JTEXT::_( 'Visitor' )));
            //echo $chartView->render();
        }


        $rows = null;
        $query = "SELECT COUNT(v.visit_id) AS ipvisits, MAX(a.code) AS code, v.ip, a.nslookup, br.browser_name
         FROM #__jstats_visits as v,#__jstats_clients as c,#__jstats_browsers as br,  #__jstats_ipaddresses as a
         WHERE a.ip = v.ip AND v.client_id = c.client_id AND c.browser_id = br.browser_id AND c.client_type = 2 AND $conditionStringFromDates
         GROUP BY br.browser_name
         ORDER BY ipvisits DESC
         LIMIT 10"
        ;
        $JSDatabaseAccess->db->setQuery( $query );
        $rows = $JSDatabaseAccess->db->loadObjectList();

        $dates = array();

        foreach( $rows as $row )
        {
            $data = new stdClass();
            $data->ipvisits = $row->ipvisits;
            $data->country = $row->code;
            $data->ip = $row->ip;
            $data->label = (empty($row->browser_name) ? $row->nslookup : $row->browser_name) . ' (' . $row->code . ')';
            $dates[] = $data;
        }

        if(!empty($chartView))
        {
            $chart =& $chartView->createChart();
            $chart->appendSlices($dates, 'label', 'ipvisits', null, null, array('title' => JTEXT::_( 'Top 10' ) . ' ' . JTEXT::_( 'Bots' )));
            echo $chartView->render();
        }

        $allkeywords = array();
        if(!empty($chartView))
        {
            // keywords
            $rows = null;
            $query = "SELECT LOWER(TRIM(k.keywords)) as query, COUNT(k.keywords) AS count_kw, k.timestamp
             FROM #__jstats_keywords as k WHERE k.timestamp >= $timestamp_from AND k.timestamp <= $timestamp_to
             GROUP BY query
             ORDER BY count_kw DESC"
            ;
            $JSDatabaseAccess->db->setQuery( $query );
            $allkeywords = $JSDatabaseAccess->db->loadObjectList();
            $allkeywords = empty($allkeywords)? array() : $allkeywords;
            $rows = empty($allkeywords)? array() : array_slice($allkeywords, 0, 15);

            $chart =& $chartView->createChart();
            $chart->appendSlices($rows, 'query', 'count_kw', null, null, array('title' => JTEXT::_( 'Top' ) . ' 15 ' . JTEXT::_( 'search queries' )));
            //echo $chartView->render();

            $rows = null;
            $keywords = array();
            foreach($allkeywords as $row)
            {
               $queryWords = explode(' ', $row->query);
               foreach($queryWords as $queryWord)
               {
                  $keywordEntry = &$keywords[$queryWord];
                  if(empty($keywordEntry))
                  {
                     $keywordEntry = new stdClass();
                     $keywordEntry->word = $queryWord;
                     $keywordEntry->count = $row->count_kw;
                     $keywords[$queryWord] = $keywordEntry;
                  }
                  else
                  {
                     $keywordEntry->count = $keywordEntry->count + $row->count_kw;
                  }
               }
            }
            /**
             * The chart does not show all entries, so we want to make sure that at least the best results are shown
             */
            usort($keywords, 'cmpKeywordObj');
            
            $keywords = empty($keywords)? array() : array_slice($keywords, 0, 15);
            
            $chart =& $chartView->createChart();
            $chart->appendSlices($keywords, 'word', 'count', null, null, array('title' => JTEXT::_( 'Top' ) . ' 15 ' . JTEXT::_( 'keywords' )));
            echo $chartView->render();
        }

		//parent::display();
	}

}
function cmpKeywordObj($a, $b)
{
    if ($a->count == $b->count) {
        return strcmp($a->word, $b->word);
    }
    return ($a->count < $b->count) ? 1 : -1;
}
