<?php

              
/**
 * @package j4age
 * @copyright Copyright (C) 2009-@THISYEAR@ j4age Team. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * Thank you to the project j4age and it's team, on which roots this project is build on.
 */
             
          

// Check to ensure this file is only called within the Joomla! application and never directly
if( !defined( '_JS_STAND_ALONE' ) && !defined( '_JEXEC' ) )
{
	die( 'JS: No Direct Access to '.__FILE__ );
}


jimport( 'joomla.application.component.view' );

/**
 * Hello View
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class j4ageViewAmline extends JView
{
    var $graphData;
    var $graphSettings;
    var $chartId ;

     function __construct($config = array())
     {
          parent::__construct($config);
          $this->graphData = array();
          $this->graphSettings = array();
          $this->chartId = 'flashchart-amline';
     }

	/**
	 * display method of Hello view
	 * @return void
	 **/
	function display($tpl = null)
	{
		$engine =  JoomlaStats_Engine::getInstance();
		$this->assignRef("engine", $engine);

        $day  = '%';
        $month = '%';
        $year  = '%';

        $date_fromStr = null;
        $date_toStr = null;
        $date_from = null;
        $date_to = null;

        $engine->FilterTimePeriod->getTimePeriodsDatesAsLong( $date_from, $date_to );
        // echo (date('Y-m-j h:m:s',$date_from).' '.date('Y-m-j h:m:s',$date_to).'<br/>');
        $steps = $engine->FilterTimePeriod->step;

        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
        
        $period = 86400;


        $inDays = false;
        //$inWeeks = false;
        $inMonth = false;
        $format = null;
        $query = null;
        $lastPosition = $date_from;

        $period = $date_to - $date_from;
        $series = $period / 86400;

        if($steps == 'day' || $series <= 31)
        {
            $series = $period / 86400;
            $inDays = true;
            $format = "Y-m-j";
            $steps == 'day';
        }
        /*else if($steps == 'week')
        {
            $series = $period / ( 86400 * 7);
        } */
        else //if($steps == 'month')
        {
            $series = $period / ( 86400 * 27);
            $inMonth = true;
            $format = "Y-m";

            $lastPosition = mktime(0,0,0, date('n', $date_from), 1, date('Y', $date_from));
        }
        $chartData	= '';

        $currentMonth = date("n", $date_to);
        $currentYear = date("Y", $date_to);
        $currentDay = date("j", $date_to);

        // now build the data for the chart
        $chartData .= '<chart>';
        $chartData .= '<series>';
        $seriesData = '';
        for( $i = 0; $i < $series; $i++ )
        {
            if($inDays)
            {
                $seriesTime = strtotime("+$i day", $date_from);
            }
            else
            {
                $seriesTime = strtotime("+$i month", $date_from);

                //$seriesTime = mktime(0,0,0, $month, 1, $year);
            }
            //$seriesTime = $start + ($period * $i);
            $key = date($format, $seriesTime);

            $seriesData .= '<value xid="'.$key.'">'.$key.'</value>' ;
        }
        $chartData .= $seriesData;
        $chartData .= '</series>';
        $chartData .= '<graphs>';
        foreach($this->graphData as $data)
        {
          $chartData .= $data;
        }
        $chartData .= '</graphs>';
        $chartData .= '</chart>';

        $settings = '<settings>';
        $settings .= '<graphs>';
        foreach($this->graphSettings as $data)
        {
          $settings .= $data;
        }
        $settings .= '</graphs>';
        $settings .= '</settings>';
        $this->assignRef("chartData", $chartData);
        $this->assignRef("settings", $settings);
        $this->assignRef("chartId", $this->chartId);

        $this->graphSettings = array();
        $this->graphData = array();

        //echo htmlentities($chartData);
        parent::display();
	}

    function render($chartId = '0', $tpl = null)
    {
       $this->chartId = 'flashchart-amline-'.$chartId;
       $this->display($tpl);
    }

    function createGraphByQuery($query, $valueColumn, $settings = array())
    {
        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();

        $date_from = null;
        $date_to = null;

        $engine =  JoomlaStats_Engine::getInstance();
        $engine->FilterTimePeriod->getTimePeriodsDatesAsLong( $date_from, $date_to );

        $steps = $engine->FilterTimePeriod->step;
        $period = $date_to - $date_from;
        $series = $period / 86400;

        $where = $settings['where'];
        if(empty($where))
        {
            $where = array();
        }

        if($steps == 'day' || $series <=31)
        {
            //$date_fromStr = date('Y-m-j', strtotime("-1 day", $date_from));
            //$date_toStr = date('Y-m-j', strtotime("+1 day", $date_to));
            //$date_fromStr = date('Y-m-j', $date_from);
            //$date_toStr = date('Y-m-j', $date_to);

            $query .= "
            WHERE ".$engine->JSDatabaseAccess->getConditionStringFromTimestamps($date_from, $date_to). ( count( $where ) ? ' AND '. implode( ' AND ', $where ) : '' )."
            GROUP BY DAYOFYEAR(FROM_UNIXTIME(v.changed_at))
            ORDER BY v.changed_at ASC
            "
            ;
        }
        else //if($steps == 'month')
        {
            //$date_fromStr = date('Y-m-j', strtotime("-1 month", $date_from));
            //$date_toStr = date('Y-m-j', strtotime("+1 month", $date_to));

            //$date_fromStr = date('Y-m-j', $date_from);
            //$date_toStr = date('Y-m-j', $date_to);

            $query .= "
            WHERE ".$engine->JSDatabaseAccess->getConditionStringFromTimestamps($date_from, $date_to). ( count( $where ) ? ' AND '. implode( ' AND ', $where ) : '' )."
            GROUP BY YEAR(FROM_UNIXTIME(v.changed_at)), MONTH(FROM_UNIXTIME(v.changed_at))
            ORDER BY v.changed_at ASC
            "
            ;
        }
        /**
         * Cache chart data if possible
         */
        $now = js_getJSNowTimeStamp();
        $rows = js_Cache::temporaryCachedQuery(null, $query, $date_from, $date_to);
        //$engine->JSDatabaseAccess->db->setQuery( $query );
        //$rows = $JSDatabaseAccess->db->loadObjectList();
        $this->createGraph($rows, $valueColumn, $settings);
    }

    function createGraphSettings($graphId, $settings = array() )
    {
        $title = $settings['title'];
        $bulletType = $settings['bullet'];

        $settings  = '<graph gid="'.$graphId.'">';
        $settings .= '<title>'.$title.'</title>';
        $settings .= '<bullet>'.$bulletType.'</bullet>';
        $settings .= '</graph>';

        $this->graphSettings[] = $settings;

        //return $settings;
    }

    function createGraph(&$rows, $valueColumn, $settings = array())
    {
        $graphId = count($this->graphData);
        $this->createGraphSettings($graphId, $settings );

        if($rows == null)
        {
            //echo 'Empty values array for '.$settings['title'];
            return;
        }

        $date_from = null;
        $date_to = null;

        $engine =  JoomlaStats_Engine::getInstance();
        $engine->FilterTimePeriod->getTimePeriodsDatesAsLong( $date_from, $date_to );

        $steps = $engine->FilterTimePeriod->step;

        $chartData = '<graph gid="'.$graphId.'">';

        $lastPosition = 0;

        $format = null;
        $inDays = false;

        $period = $date_to - $date_from;
        $series = $period / 86400;

        if($steps == 'day' || $series <=31)
        {
            $format = 'Y-m-j';
            $inDays = true;
            //$lastPosition = $date_from;
        }
        else
        {
            $format = 'Y-m';
            //$lastPosition = mktime(0,0,0, date('m', $date_from), 1, date('Y', $date_from));
        }
        $showEntry = false;
        foreach( $rows as $row  ) {
            $time = null;
            if(isset($row->timestamp))
            {
               $time = $row->timestamp;
            }
            else if(isset($row->changed_at))
            {
               $time = $row->changed_at; 
            }

            /**
             * We do not need the initial overhead of empty values.
             * This allows us to show the graph asap the first value is present
             */
            if(!$showEntry && (empty($row->$valueColumn)))
            {
                continue;
            }
            /**
             * Ignore entries without date & values at all
             */
            if(empty($time) || empty($row->$valueColumn))
            {
                continue;
            }
            $showEntry = true;
            if(empty($lastPosition))
            {
                $lastPosition = $time > $date_from ? $time : $date_from;
            }
            //We have to fill the gaps for empty series
            if($inDays)
            {
                if($lastPosition + 86400 < $time)
                {
                   $day = 1;
                   for($i = $lastPosition; $i < $time; $i = $i + 86400)
                   {
                       $chartData .= '<value xid="'.(date('Y-m-j', strtotime("+$day day",$lastPosition) )).'">0</value>';
                       $day++;
                   }
                }
            }
            else
            {
                $time = mktime(0,0,0, date('n', $time), 1, date('Y', $time));
                if(($lastPosition + (86400*31)) < $time)
                {
                   $month = 1;
                   for($i = $lastPosition; $i < $time; $i = $i + (86400*31))
                   {
                       $chartData .= '<value xid="'.(date('Y-m', strtotime("+$month month",$lastPosition) )).'">0</value>';
                       $month++;
                   }
                }
            }
            $lastPosition = $time;
            $chartData .= '<value xid="'.date($format,$time).'">'.$row->$valueColumn.'</value>';
        }
        $chartData .= '</graph>';
        $this->graphData[] = $chartData;

        //return $chartData;
    }
}
