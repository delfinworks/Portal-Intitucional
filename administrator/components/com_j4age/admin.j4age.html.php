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


require_once( dirname(__FILE__) .DS. 'libraries' .DS. 'base.classes.php' );
require_once( dirname(__FILE__) .DS. 'libraries' .DS. 'template.html.php' );
require_once( dirname(__FILE__) .DS. 'database' .DS. 'access.php' );
require_once( dirname(__FILE__) .DS. 'database' .DS. 'select.one.value.php' );
require_once( dirname(__FILE__) .DS. 'api' .DS. 'ip.info.php' );
require_once( dirname(__FILE__) .DS. 'libraries'.DS. 'filters.php' );

jimport( 'joomla.error.profiler' );

define( '_JSAdminImagePath',	JURI::base() . '/components/com_j4age/images/' );//works in j1.0.15   //use function getUrlPathToJSAdminImages() instead of this define


/**
 * NOTICE: This class will be divided to 2 classes: js_JSStatistics and js_JSStatisticsTpl
 *         Maybe the code, that You are looking for, already has been moved there!
 */
class JoomlaStats_Engine
{
    var $JSStatisticsCommon = null;
	var $FilterTimePeriod = null;	//hold TimePeriod control (it is used on all pages)
	var $dom = null; 			// screenselection - domain
	var $vid = null; 			// screenselection - visitors id
	var $moreinfo = null;		// screenselection - moreinfo //not used. Should be removed!!!
	var $updatemsg= null;		// update message used for purge

	// internal
	var $add 		= array(); // holds purged datas

	//use getStyleForDetailView() instead of below line
	var $add_dstyle	= '<span style="font-weight:normal; font-style:italic; color:#007BBD">%s</span>';	// style 4 detail view
	
	//use getStyleForSummarizedNumber() instead of below line
	var $add_style	= '&nbsp;<span style="font-weight:normal; font-style:italic;">[ %s ]</span>';		// style 4 summary view

	var $JSConf		= null; // 'JS' configuration object. Holds system and user settings

	var $JSDatabaseAccess = null;
	/** database placeholder */
	var $db;
    var $FilterSearch = null;
    var $FilterDomain = null;

    /**
     * @static Because of PHP 4.0 this is not explicit defined as static
     * 
     * @param  $task
     * @return #VjoomlastatsEngineinstance|JoomlaStats_Engine|?
     */
	function &getInstance()
	{
	   global $joomlastatsEngineinstance;
	   if(!$joomlastatsEngineinstance)
	   {
	      $joomlastatsEngineinstance = new JoomlaStats_Engine();
       }
	   return $joomlastatsEngineinstance;
    }

	/** @todo $task argument should be removed */
	function __construct( ) {
		//global $monthLong;
        $this->JSConf = js_JSConf::getInstance();

		$this->JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
		$this->db = $this->JSDatabaseAccess->db;

        $this->FilterSearch = new js_JSFilterSearch();
        $this->FilterDomain = new js_JSFilterDomain();

        $this->FilterTimePeriod = new js_JSFilterTimePeriod();
        $this->FilterTimePeriod->readTimePeriodFromRequest( $this->JSConf->startdayormonth );


		//@at 2 bugs were here - now should be OK
		//  - $this->dom = 'all'; - $this->dom could not have value all (becouse $this->dom is used in SQL querys)
		//  - value of $this->dom could not depend DIRECTLY on $this->JSConf->startdayormonth option (Compare SVN revision 102 and 103 for details)

		// new mic (better compatibility to J.1.5
		$this->dom = JRequest::getVar( 'dom' );
		$this->vid = JRequest::getVar( 'vid' );
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
	function JoomlaStats_Engine()
	{
		$args = func_get_args();
		call_user_func_array(array(&$this, '__construct'), $args);
	}	


	/**
	 * returns first id from current table page_request for checking inside page_request_c
	 * used where queries should be done and result is shown/included with purged data
	 *
	 * @return integer
	 */
	function buid() {
		require_once( dirname( __FILE__ ) .DIRECTORY_SEPARATOR. 'database' .DIRECTORY_SEPARATOR.'select.one.value.php' );

		$buid = 0;
		
		$JSDbSOV = new js_JSDbSOV();
		$JSDbSOV->getBuid($buid);
		
		return $buid;
	}

    function renderMainNavigation($showMenu = true)
    {
        if($showMenu)
        {
            $menuItems = js_JSStatisticsCommon::getJSStatisticsMenu();
            js_JSSubToolBarMenu::addMenuItems($menuItems);
            echo js_JSSubToolBarMenu::render();
        }
    }

    function renderFilters($show_search_filter = false, $show_typefilter = false, $show_timefilter = null)
    {
        require_once( dirname( __FILE__ ) .DS. 'libraries'.DS. 'statistics.common.php' );
        $instance = JoomlaStats_Engine::getInstance();

        $instance->FilterSearch = new js_JSFilterSearch();
        $instance->FilterSearch->readSearchStringFromRequest();
        $seach_hint = JTEXT::_('Date').'/'.JTEXT::_('Time').'/'.JTEXT::_('Username').'/'.JTEXT::_('TLD').'/'.JTEXT::_('IP').'/'.JTEXT::_('NS-Lookup').'/'.JTEXT::_('OS').'/'.JTEXT::_('Browser'); //this is hint for 'r03'
        $instance->FilterSearch->setSearchHint( JTEXT::sprintf('Search (%s)', $seach_hint) );
        $instance->FilterSearch->show_search_filter = $show_search_filter;

        $instance->FilterDomain->readDomainStringFromRequest();
        //$instance->FilterDomain->show_domain_filter = false;

        global $JSStatisticsCommon;
        if($JSStatisticsCommon == null)
        $JSStatisticsCommon = new js_JSStatisticsCommon($instance->JSConf);

        $JSStatisticsCommon->renderFilters($instance->FilterSearch, $instance->FilterTimePeriod, $instance->vid, $instance->moreinfo, $instance->FilterDomain, $show_typefilter, $show_timefilter);
    }

    function renderFooter()
    {
    }
}