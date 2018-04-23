<?php

              
/**
 * @package j4age
 * @copyright Copyright (C) 2009-@THISYEAR@ j4age Team. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * Thank you to the project j4age and it's team, on which roots this project is build on.
 */
             
          

if( !defined( '_JS_STAND_ALONE' ) && !defined( '_JEXEC' ) )
{
	die( 'JS: No Direct Access to '.__FILE__ );
}



require_once( dirname( __FILE__ ) .DS. 'statistics.common.html.php' );
require_once( dirname( __FILE__ ) .DS. '..'.DS. 'database' .DS. 'select.one.value.php' );




/**
 *	This class generate statistics and show them in joomla back end (administrator panel)
 *
 *	NOTICE: methods from class JoomlaStats_Engine will be moved here
 *
 *	NOTICE: This class should contain only argument less functions that are called by task/action
 */
class js_JSStatisticsCommon
{
	/** hold JoomlaStats configuration */
	var $JSConf = null;


	var $MenuArrIdAndText = null;


	function __construct( $JSConf ) {

		$this->JSConf = $JSConf;


		//initialize itself
		$this->MenuArrIdAndText = js_JSStatisticsCommon::getJSStatisticsMenu();
	}

	/**
	 * A hack to support __construct() on PHP 4
	 *
	 * Hint: descendant classes have no PHP4 class_name() constructors,
	 * so this constructor gets called first and calls the top-layer __construct()
	 * which (if present) should call parent::__construct()
	 *
	 * code from Joomla CMS 1.5.10 (thanks!)
	 *
	 * @access	public
	 * @return	Object
	 * @since	1.5
	 */
	function js_JSStatisticsCommon()
	{
		$args = func_get_args();
		call_user_func_array(array(&$this, '__construct'), $args);
	}	
	
	/**
	 * build the menu items
	 *
	 * @param array $MenuArrIdAndText
	 * @since 2.3.x (mic): building the text with JTEXT
	 */
	function getJSStatisticsMenu() {
        /**
         * If you want to remove a view simply make sure that the corresponding "view" folder isn't part of the installation
         *
         * This is important, because it enables to provide "smaller" JS versions using a reduced amount of views
         */
        if($this->MenuArrIdAndText === null)
        {
            $this->MenuArrIdAndText = array();
            $this->MenuArrIdAndText['ysummary'] =             array( 'linkType' => 'get','label' => JTEXT::_( 'Summary Year' ), 'view' => 'ysummary' );
            $this->MenuArrIdAndText['msummary'] =             array( 'linkType' => 'get','label' => JTEXT::_( 'Summary Month' ), 'view' => 'msummary', 'task' => 'msummary', 'controller' => 'main' );
            $this->MenuArrIdAndText['visits']   =             array( 'linkType' => 'get','label' => JTEXT::_( 'Visits' ), 'view' => 'visits' );
            $this->MenuArrIdAndText['visitors'] =             array( 'linkType' => 'get','label' => JTEXT::_( 'Visitors' ), 'view' => 'visitors' );
            $this->MenuArrIdAndText['visitorsbycountry'] =    array( 'linkType' => 'get','label' => JTEXT::_( 'Visitors by country' ), 'view' => 'visitorsbycountry' );
            $this->MenuArrIdAndText['pagehits'] =             array( 'linkType' => 'get','label' => JTEXT::_( 'Page Hits' ), 'view' => 'pagehits' );
            $this->MenuArrIdAndText['systems'] =              array( 'linkType' => 'get','label' => JTEXT::_( 'Systems' ), 'view' => 'systems' );
            $this->MenuArrIdAndText['browsers'] =             array( 'linkType' => 'get','label' => JTEXT::_( 'Browsers' ), 'view' => 'browsers' );
            $this->MenuArrIdAndText['botsbydomain'] =         array( 'linkType' => 'get','label' => JTEXT::_( 'Bots by domain' ), 'view' => 'botsbydomain' );
            //$this->MenuArrIdAndText['bots'] =                 array( 'linkType' => 'get','label' => JTEXT::_( 'Bots' ), 'view' => 'bots' );
            //$this->MenuArrIdAndText['notidentifiedvisitors']= array( 'linkType' => 'get','label' => JTEXT::_( 'Not identified visitors' ), 'view' => 'notidentifiedvisitors' );
            //$this->MenuArrIdAndText['unknownbotsspiders'] =   array( 'linkType' => 'get','label' => JTEXT::_( 'Unknown bots/spiders' ), 'view' => 'unknownbotsspiders' );
            $this->MenuArrIdAndText['searchEngines'] =        array( 'linkType' => 'get','label' => JTEXT::_( 'Search Engines' ), 'view' => 'searches', 'task' => 'searchEngines'  );
            $this->MenuArrIdAndText['keywords'] =             array( 'linkType' => 'get','label' => JTEXT::_( 'Keywords' ), 'view' => 'searches', 'task' => 'keywords'  );
            $this->MenuArrIdAndText['referrersByDomain'] =    array( 'linkType' => 'get','label' => JTEXT::_( 'Referrers by domain' ), 'view' => 'referrers', 'task' => 'referrersByDomain' );
            $this->MenuArrIdAndText['referrersByPage'] =      array( 'linkType' => 'get','label' => JTEXT::_( 'Referrers by page' ), 'view' => 'referrers', 'task' => 'referrersByPage' );
            $this->MenuArrIdAndText['debugbrowser'] =         array( 'linkType' => 'get','label' => JTEXT::_( 'Debug Useragents' ), 'view' => 'debugbrowser' );
            //$this->MenuArrIdAndText['rNotUsed'] = JTEXT::_( 'Resolutions' );

            js_PluginManager::fireEventWithArgs('getMenu', array(&$this->MenuArrIdAndText));
        }

        return $this->MenuArrIdAndText;
	}

	/**
	 * collecting and pass thru several datas for building the html.header (incl. <form> tag)
	 *
	 * @param string $FilterSearch
	 * @param string $FilterDate
	 * @param integer $vid
	 * @param string $moreinfo
	 * @param string $DatabaseSizeHtmlCode
	 * @param string $FilterDomain
	 * @return string
	 */
	function renderFilters( &$FilterSearch, &$FilterDate, $vid, $moreinfo, &$FilterDomain, $show_typefilter = false, $show_timefilter = null ) {

		$JSDbSOV = new js_JSDbSOV();
		$task = JRequest::getVar( 'task', 'js_view_statistics_default' ); // mic: changed to J.1.5-style

		//title to pages that are not in menu
		//$this->MenuArrIdAndText['detailVisitInformation']['text'] = JTEXT::_( 'Detail visit information' );


		$JSStatisticsCommonTpl = new js_JSStatisticsCommonTpl();
		$JSStatisticsCommonTpl->task = $task; // new mic

        $menuId = JRequest::getVar( 'mid', $task );
        /*$ReportTitle = null;
        if(isset($this->MenuArrIdAndText[$menuId]))
        {
            $ReportTitle = $this->MenuArrIdAndText[$menuId];
        }
        if($ReportTitle)
        {
            $ReportTitle = $ReportTitle['label'];
        } */
	    $JSStatisticsCommonTpl->renderFilters($FilterSearch, $FilterDate, $vid, $moreinfo, $FilterDomain, $show_typefilter, $show_timefilter);
	}
}
