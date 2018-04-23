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
class j4ageViewAmpie extends JView
{
    var $charts;

     function __construct($config = array())
     {
          parent::__construct($config);
          $this->charts = array();
     }
	/**
	 * display method of Hello view
	 * @return void
	 **/
	function display($tpl = null)
	{
        $this->assignRef("charts", $this->charts);
        parent::display();
        $this->charts = array();
	}

    function render($tpl = null)
    {
       $this->display($tpl);
    }

    function &createChart()
    {
        /**
         * this needs to be global to be on the save side, that we have at all time a unique ID
         */
        global $js_chart_ampie_counter;
        if(empty($js_chart_ampie_counter))
        {
            $js_chart_ampie_counter = 1;
        }
        //$chart = null;

        $chart = new js_AmPieChart();
        $chart->chartId = $js_chart_ampie_counter++;
        $this->charts[] =& $chart;
        return $chart;
    }
}

class js_AmPieChart
{
    var $graphData;
    var $graphSettings;

    var $chartId;

    function __construct()
    {
         $this->graphData = array();
         $this->graphSettings = array();
         $this->chartId = null;
    }


    function createGraphSettings($graphId, $settings = array() )
    {
        $title = $settings['title'];

        $settings = '<labels>';
        $settings .= '<label lid="0">';
        $settings .= '<x>0</x>';
        $settings .= '<y>0</y>';
        $settings .= '<align>right</align>';
        $settings .= '<text>'.$title.'</text>';
        $settings .= '</label>';
        $settings .= '</labels>';

        $this->graphSettings[] = $settings;

        //return $settings;
    }
    function appendSlice($title, $value, $url, $description, $alpha = null, $settings = array() )
    {
        $slice  = '<slice';
        if(!empty($title))
        {
            $slice  .= ' title="'.htmlspecialchars(addslashes($title)).'"';
        }
        if(!empty($url))
        {
            $slice  .= ' url="'.$url.'"';
        }
        if(!empty($description))
        {
            $slice  .= ' description="'.$description.'"';
        }
        if(!empty($alpha))
        {
            $slice  .= ' alpa="'.$alpha.'"';
        }
        $slice .= '>';
//        $slice .= '<![CDATA[';
        $slice .= htmlspecialchars(addslashes($value));
        //$slice .= ']]>';
        $slice .= '</slice>';

        $this->graphData[] = $slice;
        //return $settings;
    }

    function appendSlices(&$rows, $titleColumn, $valueColumn, $urlColumn = null, $descrpColumn = null, $settings = array())
    {
        foreach( $rows as $row  )
        {
            $title = empty($titleColumn)? null : $row->$titleColumn;
            $value = empty($valueColumn)? null : $row->$valueColumn;
            $description = empty($descrpColumn)? null : $row->$descrpColumn;
            $url = empty($urlColumn)? null : $row->$urlColumn;

            $this->appendSlice($title, $value, $url, $description, null, $settings);
        }
        $this->createGraphSettings(null, $settings);
    }

    function &getDataXML()
    {
        $chartData = '<pie>';
        foreach($this->graphData as $data)
        {
          $chartData .= $data;
        }
        $chartData .= '</pie>';
        return $chartData;
    }

    function &getSettingsXML()
    {

        $settings = '<settings>';
        foreach($this->graphSettings as $data)
        {
          $settings .= $data;
        }
        $settings .= '</settings>';
        return $settings;
    }

    function getId()
    {
       return 'flashchart-ampie-'.$this->chartId;
    }
}
