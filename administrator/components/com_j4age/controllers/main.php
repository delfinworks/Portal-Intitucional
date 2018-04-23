<?php

if( !defined( '_JS_STAND_ALONE' ) && !defined( '_JEXEC' ) )
{
	die( 'JS: No Direct Access to '.__FILE__ );
}


              
/**
 * @package j4age
 * @copyright Copyright (C) 2009-@THISYEAR@ j4age Team. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * Thank you to the project j4age and it's team, on which roots this project is build on.
 */
             
          

jimport('joomla.application.component.controller');


require_once( dirname( __FILE__ ) .DS.'..' .DS.'api'.DS. 'toolbar.j4age.php' );
require_once( dirname( __FILE__ ) .DS.'..' .DS. 'libraries'.DS.'statistics.common.php' );

/**
 * Component Controller
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class j4ageControllerMain extends JController
{
    var $JoomlaStatsEngine = null;

    /**
     * Constructor that retrieves the ID from the request
     *
     * @access    public
     * @return    void
     */
    function __construct($config = array())
    {
        if(empty( $this->_basePath ))
            $this->_basePath = JPATH_COMPONENT_ADMINISTRATOR;
        if(!array_key_exists('base_path', $config) || empty($config['base_path']))
            $config['base_path']  = $this->_basePath;
        if(empty( $this->basePath ))
            $this->basePath = $this->_basePath;


        parent::__construct($config);
    }

    function initContrl(&$task)
    {
        JRequest::setVar('task', JRequest::getVar('task', 'stats'));

        //$model = $this->setModelToCurrentView();
        //JToolBarHelper::preferences($mainframe->scope, '550');

        // create JoomlaStats engine for reporting
        $this->JoomlaStatsEngine = JoomlaStats_Engine::getInstance();
    }

    /**
     * Method to get a reference to the current view and load it if necessary.
     *
     * @access    public
     * @param    string    The view name. Optional, defaults to the controller
     * name.
     * @param    string    The view type. Optional.
     * @param    string    The class prefix. Optional.
     * @param    array    Configuration array for view. Optional.
     * @return    object    Reference to the view or an error.
     * @since    1.5
     */
    function &getView( $name = '', $type = '', $prefix = '', $config = array() )
    {
        if(empty($config))
        {
            $config = $config = array( 'base_path'=>$this->_basePath);
        }
        return parent::getView($name, $type,$prefix, $config);
    }

    /**
     * Method to display the view
     *
     * @access    public
     */
    function display($surroundings = true, $printform = null, $toolbar = true)
    {
        if($printform == null)
        {
           $printform = $surroundings;
        }
        $document =& JFactory::getDocument();
        $task    = JRequest::getVar('task', 'default');
        $this->setModelToCurrentView();

        js_echoJSDebugInfo('display view: \''.JRequest::getVar('view').'\'<br/>', '');

        //use parameter header to hide the navigation
        $surroundings = JRequest::getBool('header', $surroundings);


        js_profilerMarker('Render View');
        $JSTemplate = new js_JSTemplate();
        $view = JRequest::getVar('view', '');
        $task = JRequest::getVar('task');
		echo $JSTemplate->generateBeginingOfAdminForm( $task, $view );
        JoomlaStats_Engine::renderMainNavigation($surroundings);
        parent::display();
        echo $JSTemplate->generateEndOfAdminForm();
        js_profilerMarker('Render View End');

        if($toolbar)
        {
            $this->setupToolbar($task);
        }
    }

    function setModelToCurrentView()
    {
        $document =& JFactory::getDocument();

        $model =& $this->getModel('common');
        $viewName    = JRequest::getVar('view');
        if(empty($viewName))
        {
            return $model;
        }
        $viewType    = $document->getType();

        // Set the default view name from the Request
        $view = $this->getView($viewName, $viewType);


        // Push a model into the view
        if (!JError::isError( $model )) {
            $modelold = $view->getModel();
            if( $view->getModel() == null)
            {
                $view->setModel( $model, true );
            }
            else
            {
                $model = $modelold;
            }
        }
        return $model;
    }

    function uninstall()
    {
        js_echoJSDebugInfo('called method uninstall<br/>', '');
        JRequest::setVar('view', 'uninstall');
        $this->display();
    }

    function uninstalltask()
    {
        JRequest::setVar('view', 'uninstalltask');
        $this->display();
    }

    function ysummary()
    {
        JRequest::setVar('view', 'ysummary');
        $this->display(true);
    }

    function msummary()
    {
        JRequest::setVar('view', 'msummary');
        $this->display(true);

    }

    function visitors()
    {
        $this->JoomlaStatsEngine->FilterSearch->show_search_filter = true;
        $this->JoomlaStatsEngine->FilterDomain->show_domain_filter = true;
        $this->JoomlaStatsEngine->FilterDomain->show_time_period_filter = true;

        JRequest::setVar('view', 'visitors');
        $this->display(true);
    }
    
    function visits()
    {
        $this->JoomlaStatsEngine->FilterSearch->show_search_filter = true;
        $this->JoomlaStatsEngine->FilterDomain->show_domain_filter = true;
        $this->JoomlaStatsEngine->FilterDomain->show_time_period_filter = true;

        JRequest::setVar('view', 'visits');
        $this->display(true);
    }

    function visitorsByCountry()
    {
        JRequest::setVar('view', 'visitorsByCountry');
        $this->display(true);

    }

    function botsByDomain()
    {
        JRequest::setVar('view', 'botsbydomain');
        $this->display(true);
    }
    
    function pageHits()
    {
        JRequest::setVar('view', 'pagehits');
        $this->display(true);
    }

    function systems()
    {
        $this->JoomlaStatsEngine->FilterSearch->show_search_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_domain_filter = false;
        
        JRequest::setVar('view', 'systems');
        $this->display(true);
    }

    function browsers()
    {
        JRequest::setVar('view', 'browsers');
        $this->display(true);
    }

    function bots()
    {
        $this->JoomlaStatsEngine->FilterSearch->show_search_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_domain_filter = true;
        
        JRequest::setVar('view', 'bots');
        $this->display(true);
    }

    function referrersByDomain()
    {
        $this->JoomlaStatsEngine->FilterSearch->show_search_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_domain_filter = true;

        JRequest::setVar('view', 'referrers');

        $document =& JFactory::getDocument();
        $viewName    = JRequest::getVar('view');
        $viewType    = $document->getType();
        $view =& $this->getView($viewName, $viewType);
        $this->setModelToCurrentView();
        $model =& $view->getModel( );
        $model->isByPage = false;

        $this->display(true);
    }

    function referrersByPage()
    {
        $this->JoomlaStatsEngine->FilterSearch->show_search_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_domain_filter = true;

        JRequest::setVar('view', 'referrers');

        $document =& JFactory::getDocument();
        $viewName    = JRequest::getVar('view');
        $viewType    = $document->getType();
        $view =& $this->getView($viewName, $viewType);
        $this->setModelToCurrentView();
        $model =& $view->getModel( );
        $model->isByPage = true;

        $this->display(true);
    }

    function unknownVisitors()
    {
        JRequest::setVar('view', 'notidentified');
        $this->display(true);
    }

    function unknownBotsSpiders()
    {
        JRequest::setVar('view', 'unknownBotsSpiders');
        $this->display(true);
    }

    function morevisitorinfo()
    {
        JRequest::setVar('view', JRequest::getVar('view', 'morevisitorinfo'));
        $this->display(true);
    }

    function morePathInfo()
    {
        JRequest::setVar('view', JRequest::getVar('view', 'morepathinfo'));
        $this->display(true);
    }

    function exclude()
    {
        $this->JoomlaStatsEngine->FilterSearch->show_search_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_domain_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_time_period_filter = false;
        $this->JoomlaStatsEngine->FilterTimePeriod->hide = true;
        JRequest::setVar('view', 'exclude');
        $this->display(false);
    }

    function jsexport2csv()
    {
        JRequest::setVar('view', 'jsexport2csv');
        $this->JoomlaStatsEngine->JSExport2CVS( $act );
    }

    function viewPageHits()
    {
        JRequest::setVar('view', 'pagehits');
        $this->display(true);
    }

    function viewSystems()
    {
        JRequest::setVar('view', 'systems');
        $this->display(true);
    }

    function notIdentifiedVisitors()
    {
        $this->JoomlaStatsEngine->FilterSearch->show_search_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_domain_filter = false;

        JRequest::setVar('view', 'notidentifiedvisitors');
        $this->display(true);
    }

    function searchEngines()
    {
        $this->JoomlaStatsEngine->FilterSearch->show_search_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_domain_filter = false;
        JRequest::setVar('view', 'searches');

        $document =& JFactory::getDocument();
        $viewName    = JRequest::getVar('view');
        $viewType    = $document->getType();
        $view =& $this->getView($viewName, $viewType);
        $this->setModelToCurrentView();
        $model =& $view->getModel( );
        $model->isKeywords = false;

        $this->display(true);
    }

    function keywords()
    {
        $this->JoomlaStatsEngine->FilterSearch->show_search_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_domain_filter = true;

        JRequest::setVar('view', 'searches');

        $document =& JFactory::getDocument();
        $viewName    = JRequest::getVar('view');
        $viewType    = $document->getType();
        $view =& $this->getView($viewName, $viewType);
        $this->setModelToCurrentView();
        $model =& $view->getModel( );
        $model->isKeywords = true;

        $this->display(true);
    }

    function detailVisitInformation()
    {
        $this->JoomlaStatsEngine->FilterSearch->show_search_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_domain_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_time_period_filter = false;
        $this->JoomlaStatsEngine->FilterTimePeriod->hide = true;
        JRequest::setVar('view', 'detailvisitinformation');

        $this->display(true);
    }

    function tools()
    {
        $this->JoomlaStatsEngine->FilterSearch->show_search_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_domain_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_time_period_filter = false;
        $this->JoomlaStatsEngine->FilterTimePeriod->hide = true;
        JRequest::setVar('view', 'tools');
        $this->display(false);
    }

    function doGraphic()
    {
        $this->JoomlaStatsEngine->FilterSearch->show_search_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_domain_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_time_period_filter = false;
        $this->JoomlaStatsEngine->FilterTimePeriod->hide = true;
        JRequest::setVar('view', 'graphics');
        $this->display(false);
    }

    function configuration()
    {
        $this->JoomlaStatsEngine->FilterSearch->show_search_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_domain_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_time_period_filter = false;
        $this->JoomlaStatsEngine->FilterTimePeriod->hide = true;
        JRequest::setVar('view','configuration');
        $this->display(false);
    }

    function help()
    {
        $this->JoomlaStatsEngine->FilterSearch->show_search_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_domain_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_time_period_filter = false;
        $this->JoomlaStatsEngine->FilterTimePeriod->hide = true;
        JRequest::setVar('view', 'help');
        $this->display(false);
    }

    function whois()
    {
        $this->JoomlaStatsEngine->FilterSearch->show_search_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_domain_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_time_period_filter = false;
        $this->JoomlaStatsEngine->FilterTimePeriod->hide = true;
        JRequest::setVar('view', 'whois');
        $this->display(false);
    }

    function status()
    {
        $this->JoomlaStatsEngine->FilterSearch->show_search_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_domain_filter = false;
        $this->JoomlaStatsEngine->FilterDomain->show_time_period_filter = false;
        $this->JoomlaStatsEngine->FilterTimePeriod->hide = true;

        JRequest::setVar('view', 'status');
        $this->display(false, false);
    }

    /**
    Todo refectoring to separated methods
     **/
    function execute($task)
    {
        if($task == 'stats')
        {
            $JSConf = js_JSConf::getInstance();
            $task = $JSConf->startoption;
        }

        switch ($task)
        {
            case "r01": $task = "ysummary";
                break;
            case "r02": $task = "msummary";
                break;
            case "r03": $task = "visitors";
                break;
            //case "r04": $task = "bots";
            //    break;
            case "r05": $task = "VisitorsByCountry";
                break;
            case "r06": $task = "pageHits";
                break;
            case "r07": $task = "systems";
                break;
            case "r08": $task = "browsers";
                break;
            case "r09": $task = "botsByDomain";
                break;
            //case "r09": $task = "botsspiders";
            //    break;
            //case "r09a": $task = "moreVisitInfo";
            //    break;
            case "r10": $task = "bots";
                break;
            case "r11": $task = "notidentifiedvisitors";
                break;
            case "r12": $task = "unknownBotsSpiders";
                break;
            case "r14": $task = "searchEngines";
                break;
            case "r15": $task = "keywords";
                break;
            case "r16": $task = "referrersByDomain";
                break;
            case "r17": $task = "referrersByPage";
                break;
            case "r18": $task = "detailVisitInformation";
                break;
            case "rNotUsed": $task = "resolutions";
                break;
            case "js_view_tools": $task = "tools";
                break;
            case "js_view_uninstall": $task = "uninstall";
                break;
            case "js_do_uninstall": $task = "douninstall";
                break;
            case "js_view_summarize": $task = "summarize";
                break;
            case "graphics": $task = "doGraphic";
                break;
            case "js_graphics": $task = "doGraphic";
                break;
            case "js_view_configuration": $task = "configuration";
                break;
            case "js_do_configuration_save": $task = "saveConfiguration";
                break;
            case "js_do_configuration_apply": $task = "applyConfiguration";
                break;
            case "js_do_configuration_set_default": $task = "setDefaultConfiguration";
                break;
            case "js_view_status": $task = "status";
                break;
            case "js_view_exclude": $task = "exclude";
                break;
            case "js_do_ip_exclude": $task = "doIpExclude";
                break;
            case "js_do_ip_include": $task = "doIpInclude";
                break;
            case "js_export_do_js2csv": $task = "doExportCSV";
                break;
            case "js_view_help": $task = "help";
                break;
            case "js_view_whois_popup": $task = "whois";
                break;

            //case "r03a": $task = "morevisitorinfo";
            //    break;
            //case "r03b": $task = "morePathInfo";
            //    break;

        }
        js_echoJSDebugInfo('converted to task: \''.$task.'\'<br/>', '');

        JRequest::setVar('task', $task);
        $this->initContrl($task);

        parent::execute($task);
    }

    function setupToolbar($task)
    {
        $JSToolBarMenu = new js_JSToolBarMenu();
        js_echoJSDebugInfo('toolbar for: \''.$task.'\'<br/>', '');

        switch( $task ) {

            case 'js_view_configuration':
            case 'js_do_configuration_apply':
            case 'js_do_configuration_set_default':
            case 'applyConfiguration':
            case 'setDefaultConfiguration':
            case 'configuration':
                $JSToolBarMenu->CONFIG_MENU();
                break;
            case 'maintenance':
            case 'dooptimizedatabase':
            case 'doOptimizeDatabase':
            case 'js_view_tools':
                $JSToolBarMenu->TOOLS_MENU();
                break;
            case 'tools':
                $JSToolBarMenu->TOOLS_MENU();
                break;

            case 'js_view_uninstall':
            case 'uninstall':
            case 'js_do_uninstall'://this page is never shown except uninstall errors
                $JSToolBarMenu->UNINSTALL_MENU();
                break;

            case 'js_maintenance_do_database_backup_partial':
            case 'js_maintenance_do_database_backup_full':
            case 'js_maintenance_do_database_initialize_with_sample_data':
                $JSToolBarMenu->BACK_TO_MAINTENANCE_MENU( JTEXT::_( 'Tools' ) );
                break;

            case 'js_view_status':
            case 'status':
                $JSToolBarMenu->DEFAULT_MENU( JTEXT::_('Status') );
                break;

            //@todo 'js_view_exclude' should have own menu due to issue: 'Missing action buttons at 'Exclude' option'
            case 'js_view_exclude':
                //$JSToolBarMenu->BACK_TO_STAT_MENU( JTEXT::_('Exclude Manager') );//@todo 'js_view_exclude' should have own menu due to issue: 'Missing action buttons at 'Exclude' option'
                $JSToolBarMenu->DEFAULT_MENU( JTEXT::_('Exclude Manager') );
                break;

            case 'js_graphics':
                $JSToolBarMenu->DEFAULT_MENU( JTEXT::_('Graphics') );
                break;

            //all statistic pages
            default:

                $menuItems = js_JSStatisticsCommon::getJSStatisticsMenu();

                $menuId = JRequest::getVar( 'mid', JRequest::getVar( 'task', '' ) );
                $ReportTitle = null;
                if(isset($menuItems[$menuId]))
                {
                    $ReportTitle = $menuItems[$menuId];
                }
                if($ReportTitle)
                {
                    $ReportTitle = $ReportTitle['label'];
                }
                else
                {
                    $ReportTitle = JTEXT::_('Statistics');
                }

                $JSToolBarMenu->DEFAULT_MENU( $ReportTitle );
                break;
        }
    }
}

?>
