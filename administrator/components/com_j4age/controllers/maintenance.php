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


//require_once( dirname(__FILE__).'/base.classes.php' );
require_once( dirname(__FILE__) .DS.'..'.DS. 'libraries'.DS. 'util.classes.php' );
require_once( dirname(__FILE__) .DS.'..'.DS.'api'.DS. 'tools.php' );
jimport('joomla.application.component.controller');

require_once( dirname( __FILE__ ) .DS. 'main.php' );


/**
 *  Joomla Stats Tools class
 *
 *  This contain features from 'Tools' tab in 'Joomla Stats' Configuration panel.
 *  Basicly contain maintenance functions
 *
 *  NOTICE: This class should contain only set of static, argument less functions that are called by task/action
 */
class j4ageControllerMaintenance extends j4ageControllerMain
{
    /**
     * This function optimize all JoomlaStats database tables
     * new from v2.3.0.170, tested - OK
     *
     * return true on success
     */
	function doOptimizeDatabase() {

        //$this->JoomlaStatsEngine->FilterDomain->show_time_period_filter = false;

        JRequest::setVar('view', JRequest::getVar('view', 'tools'));

		$JSUtil = new js_JSUtil();
		$res = $JSUtil->optimizeAllJSTables();

		if ($res == false) {
			$msg = JTEXT::_( 'Database optimization failed' );
            JError::raiseNotice( 0, $msg );
		}
        else
        {
            $msg = JTEXT::_( 'Database successfully optimized' );
//            $this->setMessage( $msg );
//            $this->setRedirect();
            JError::raiseNotice( 0, $msg );            
        }

        $this->display();
	}


   function doDropOldData()
   {
       JRequest::setVar('view', JRequest::getVar('view', 'tools'));
       $days = JRequest::getInt('periodIndays', 730);

       $JSUtil = new js_JSUtil();
       $res = $JSUtil->dropData("#__jstats_visits", "changed_at", $days);
       if($res == true) $res = $JSUtil->dropData("#__jstats_referrer", "timestamp", $days);
       if($res == true) $res = $JSUtil->dropData("#__jstats_keywords", "timestamp", $days);
       if($res == true) $res = $JSUtil->dropData("#__jstats_impressions", "timestamp", $days);
       if($res == true) $res = $JSUtil->releaseUnusedData("#__jstats_pages", "#__jstats_impressions", "page_id", "page_id");
       //We should consider to also clean-up all clients & ipaddresses, which are no more used

       if ($res != true) {
           $msg = JTEXT::_( $res );
           JError::raiseNotice( 0, $msg );
       }
       else
       {
           $msg = JTEXT::_( 'Data successfully dropped' );
           JError::raiseNotice( 0, $msg );
       }

       $this->display();

   }

    
	/**
	 *  backup database
	 *
	 *  function removed due to to many deprecated and not working code. Previus version do not make a backup! (in many cases it brake database!)
	 */
    function backupDatabase() {
    }


    function douninstall()
    {
        JRequest::setVar('view', 'uninstall');

        $JSTools = new js_JSTools();
        $JSTools->doJSUninstall();
        //$this->display(false);
    }


    function saveConfiguration()
    {
        //JRequest::setVar('view', JRequest::getVar('view', 'configuration'));
        //$this->display(false);
        $JSConf 	= js_JSConf::getInstance();
        $this->SetConfiguration( $JSConf->startoption );
    }

    function applyConfiguration()
    {
        //JRequest::setVar('view', JRequest::getVar('view', 'configuration'));
        //$this->display(false);
        $this->SetConfiguration( 'js_view_configuration' );
    }

    function setDefaultConfiguration()
    {
        JRequest::setVar('view', JRequest::getVar('view', 'configuration'));

        //convienient way to get default configuration within 'current configuration' object
        $JSConf    = new js_JSConf(false);

        $msg		= JTEXT::_( 'Default j4age configuration has been set' );
        $err_msg	= '';
        $res		= $JSConf->storeConfigurationToDatabase( $err_msg );

        if( $res == false) {
            $msg		= JTEXT::_( '' );//@todo missing message
            $this->setRedirect( 'index.php?option=com_j4age&task=js_view_configuration', $msg, 'error' );//third argument: 'message', 'notice', 'error'
            return false;
        }

        $msg		= JTEXT::_( 'Changes sucessfully saved' );
        $this->setRedirect( 'index.php?option=com_j4age&task=js_view_configuration', $msg, 'message' );//third argument: 'message', 'notice', 'error'
        //return true;


        //$this->display(false);
    }

    	/**
	 * Stores the JoomlaStats configuration
	 *
	 * @deprecated language since 2.3.x (mic)
	 * @param unknown_type $redirect_to_task
	 */
	function SetConfiguration( $redirect_to_task ) {
		$JSConfDef 	= new js_JSConfDef();
		$JSConf 	= js_JSConf::getInstance();

		$JSConf->onlinetime 		= isset($_POST['onlinetime'])			? $_POST['onlinetime']		: $JSConfDef->onlinetime;
        $JSConf->onlinetime_bots    = isset($_POST['onlinetime_bots'])	    ? $_POST['onlinetime_bots']	: $JSConfDef->onlinetime_bots;

		$JSConf->startoption		= isset($_POST['startoption'])			? $_POST['startoption'] 	: $JSConfDef->startoption;
		$JSConf->startdayormonth	= isset($_POST['startdayormonth'])		? $_POST['startdayormonth'] : $JSConfDef->startdayormonth;
		//$JSConf->language			= isset($_POST['language']) 			? $_POST['language']		: $JSConfDef->language;
		$JSConf->include_summarized	= isset($_POST['include_summarized'] )	? true						: false; //this is checkbox. It have to be serve in different way //$JSConfDef->include_summarized;
		{//temporary solution
			//$JSConf->show_summarized	= isset($_POST['show_summarized'] )	? true						: false; //this is checkbox. It have to be serve in different way //$JSConfDef->show_summarized;
			$JSConf->show_summarized	= isset($_POST['include_summarized'] )	? true					: false; //this is checkbox. It have to be serve in different way //$JSConfDef->show_summarized;
		}
		$JSConf->enable_whois		= isset($_POST['enable_whois']) 		? true						: false; //this is checkbox. It have to be serve in different way //$JSConfDef->enable_whois;
        $JSConf->enable_i18n		= isset($_POST['enable_i18n'])			? true						: false; //this is checkbox. It have to be serve in different way //$JSConfDef->enable_i18n;
        $JSConf->show_charts_within_reports	= isset($_POST['show_charts_within_reports']) ? true	    : false; 

        $enable_index_clients_useragent = JRequest::getBool('enable_index_clients_useragent', false);
        $enable_index_impressions_visit = JRequest::getBool('enable_index_impressions_visit', false);
        $enable_index_visits_changed_at = JRequest::getBool('enable_index_visits_changed_at', false);
        $enable_index_visits_ip         = JRequest::getBool('enable_index_visits_ip', false);

		$err_msg	= '';
		$res		= $JSConf->storeConfigurationToDatabase( $err_msg );

        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
        if($enable_index_clients_useragent)
        {
            if(!$JSDatabaseAccess->hasTableIndexForColumn('#__jstats_clients', 'useragent'))
            {
                $JSDatabaseAccess->addIndex('#__jstats_clients', 'useragent');
            }
        }
        else
        {
            if($JSDatabaseAccess->hasTableIndexForColumn('#__jstats_clients', 'useragent'))
            {
                $JSDatabaseAccess->dropIndex('#__jstats_clients', 'useragent');
            }
        }

        if($enable_index_impressions_visit)
        {
            if(!$JSDatabaseAccess->hasTableIndexForColumn('#__jstats_impressions', 'visit_id'))
            {
                $JSDatabaseAccess->addIndex('#__jstats_impressions', 'visit_id');
            }
        }
        else
        {
            if($JSDatabaseAccess->hasTableIndexForColumn('#__jstats_impressions', 'visit_id'))
            {
                $JSDatabaseAccess->dropIndex('#__jstats_impressions', 'visit_id');
            }
        }

        if($enable_index_visits_changed_at)
        {
            if(!$JSDatabaseAccess->hasTableIndexForColumn('#__jstats_visits', 'changed_at'))
            {
                $JSDatabaseAccess->addIndex('#__jstats_visits', 'changed_at');
            }
        }
        else
        {
            if($JSDatabaseAccess->hasTableIndexForColumn('#__jstats_visits', 'changed_at'))
            {
                $JSDatabaseAccess->dropIndex('#__jstats_visits', 'changed_at');
            }
        }

        if($enable_index_visits_ip)
        {
            if(!$JSDatabaseAccess->hasTableIndexForColumn('#__jstats_visits', 'ip'))
            {
                $JSDatabaseAccess->addIndex('#__jstats_visits', 'ip');
            }
        }
        else
        {
            if($JSDatabaseAccess->hasTableIndexForColumn('#__jstats_visits', 'ip'))
            {
                $JSDatabaseAccess->dropIndex('#__jstats_visits', 'ip');
            }
        }

		if( $res == false) {
			$msg		= JTEXT::_( '' );//@todo missing message
			$this->setRedirect( 'index.php?option=com_j4age&task='.$redirect_to_task, $msg, 'error' );//third argument: 'message', 'notice', 'error'
			return false;
		}

		$msg		= JTEXT::_( 'Changes sucessfully saved' );
		$this->setRedirect( 'index.php?option=com_j4age&task='.$redirect_to_task, $msg, 'message' );//third argument: 'message', 'notice', 'error'
		return true;
	}

    function applyParsedUseragents()
    {
        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();

        $client_ids	= JRequest::getVar( 'cid', array() );
        $browser_ids	= JRequest::getVar( 'browser_id', array() );
        $browser_versions	= JRequest::getVar( 'browser_version', array() );
        $visitor_types	= JRequest::getVar( 'visitor_type', array() );
        $os_ids	= JRequest::getVar( 'os_id', array() );
        $afilter	= JRequest::getVar( 'afilter', '' );

        $this->setRedirect( 'index.php?option=com_j4age&view=debugbrowser&afilter='.$afilter, $msg, 'message' );//third argument: 'message', 'notice', 'error'
        
        foreach($client_ids as $index=>$client_id)
        {
            $browser_id = $browser_ids[$index];
            $browser_version = $browser_versions[$index];
            $visitor_type = $visitor_types[$index];
            $os_id = $os_ids[$index];
            $Client = new js_Client();
            $Client->client_id = $client_id;
            $Client->browser_version = $browser_version;
            $Client->client_type = $visitor_type;
            $Client->Browser = new js_Browser();
            $Client->Browser->browser_id = $browser_id;
            if($os_id != null)
            {   
                $Client->OS = new js_Os();
                $Client->OS->os_id = $os_id;
            }
            IPInfoHelper::updateClient($Client, true);
        }

        //$this->display(true);

    }

	/**
	 * This function include/exclude addresses that are in $_REQUEST['cid'] array
	 * old name excludeIpAddress
	 *
	 * @param string	$action = 'include': to include addresses; 'exclude': to exlcude addresses
	 */
	function excludeIpAddressArr( $exclude = false ) {
		$JSDatabaseAccess = js_JSDatabaseAccess::getInstance();

		$cidv	= JRequest::getVar( 'cid', 0 );
		$vid	= JRequest::getVar( 'vid', array( 0 ) );
		$cid	= array( 0 );
        $block = $exclude;

		if( is_array( $cidv ) ) {
			$cid = $cidv;
		}else{
			if( $cidv !== 0 ) {
				$cid[0] = $cidv;
			}
		}

		if( ( count( $vid ) > 0 ) && ( $vid[0] != 0 ) ) {
			$cid[0] = $vid;
		}

		if( count( $cid ) < 1 ) {
			$task_name = $block ? 'js_do_ip_exclude' : 'js_do_ip_include';
			echo '<script type="text/javascript">alert(\'' . JTEXT::_( 'Please choose an entry to' ) . ': ' . $task_name . '\'); window.history.go(-1);</script>' . "\n";
			exit;
		}

		$cids = 0;
		if( count( $cid ) > 1 ) {
			$cids = implode( ',', $cid );
		}else{
			$cids = $cid[0];
		}

        //this is a better approach: $query = 'UPDATE #__jstats_ipaddresses SET ip_exclude = MOD(ip_exclude + 1, 2) WHERE ip IN (' . $cids . ')';

		$query = 'UPDATE #__jstats_ipaddresses'
		. ' SET ip_exclude = \'' . $block . '\''
		. ' WHERE ip IN (' . $cids . ')'
		;
		$JSDatabaseAccess->db->setQuery( $query );

		if( !$JSDatabaseAccess->db->query() ) {
			echo '<script type="text/javascript">alert(\''.$JSDatabaseAccess->db->getErrorMsg().'\');window.history.go(-1);</script>' ."\n";
			exit();
		}

		if( $block ) {
			$msg = JTEXT::_( 'IP address successfully excluded' );
		}else{
			$msg = JTEXT::_( 'IP address successfully included' );
		}
       
		//redirect to approprate page
		$task = 'js_view_exclude'; // return to 'Exclude Manager' page

		// use vid parameter, because we didn't want to use extra paramater to parse
		if( ( count( $vid ) > 0 ) && ( $vid[0] !=0 ) ) {
			//in case if function is called from the statistics page return to it
			$task = 'r03';
		}

        $view = JRequest::getVar('returnView', null);
        if(empty($view))
        {
            $view = JRequest::getVar('view', 'visitors');
        }
        $returnTask = JRequest::getVar('returnTask', null);

        $controller = JRequest::getVar('controller', null);
        $tpl = JRequest::getVar('tpl', null);
        $d = JRequest::getVar( 'd', '' );
		$m = JRequest::getVar( 'm', '' );
		$y = JRequest::getVar( 'y', '' );

        $this->setRedirect( "index.php?option=com_j4age&view=$view".($controller == null ? '': '&controller='.$controller).($tpl == null ? '': '&tpl='.$tpl).($returnTask == null ? '': '&task='.$returnTask)."&d=$d&m=$m&y=$y", $msg, 'message' );
	}

	/**
	 * This function include/exclude clients which are in $_REQUEST['cid'] array
	 *
	 */
	function clientExclution( $exclude = false  ) {
        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();

        $cidv	= JRequest::getVar( 'cid', 0 );
        $vid	= JRequest::getVar( 'vid', array( 0 ) );
        $cid	= array( 0 );

        if( is_array( $cidv ) ) {
            $cid = $cidv;
        }else{
            if( $cidv !== 0 ) {
                $cid[0] = $cidv;
            }
        }

        if( ( count( $vid ) > 0 ) && ( $vid[0] != 0 ) ) {
            $cid[0] = $vid;
        }

        if( count( $cid ) < 1 ) {
            $task_name = $exclude ? 'excludeClients' : 'includeClients';
            echo '<script type="text/javascript">alert(\'' . JTEXT::_( 'Please choose an entry to' ) . ': ' . $task_name . '\'); window.history.go(-1);</script>' . "\n";
            exit;
        }

        $cids = '0';
        if( count( $cid ) > 1 ) {
            $cids = implode( ',', $cid );
        }else{
            $cids = $cid[0];
        }

        //this is a better approach: $query = 'UPDATE #__jstats_ipaddresses SET ip_exclude = MOD(ip_exclude + 1, 2) WHERE ip IN (' . $cids . ')';
        $query = 'UPDATE #__jstats_clients'
        . ' SET client_exclude = \'' . ($exclude? 1:0) . '\''
        . ' WHERE client_id IN (' . $cids . ')'
        ;

        $JSDatabaseAccess->db->setQuery( $query );
        if( !$JSDatabaseAccess->db->query() ) {
            echo '<script type="text/javascript">alert(\''.$JSDatabaseAccess->db->getErrorMsg().'\');window.history.go(-1);</script>' ."\n";
            exit();
        }

        if( $exclude ) {
            $msg = JTEXT::_( 'Client(s) successfully excluded' );
        }else{
            $msg = JTEXT::_( 'Client(s) successfully included' );
        }

        //redirect to approprate page

        $view = JRequest::getVar('returnView', null );
        if(empty($view))
        {
          $view = JRequest::getVar('view', 'visitors');
        }
        $returnTask = JRequest::getVar('returnTask', null);

        $controller = JRequest::getVar('controller', null);
        $tpl = JRequest::getVar('tpl', null);
        $d = JRequest::getVar( 'd', '' );
		$m = JRequest::getVar( 'm', '' );
		$y = JRequest::getVar( 'y', '' );

        $this->setRedirect( "index.php?option=com_j4age&view=$view".($controller == null ? '': '&controller='.$controller).($tpl == null ? '': '&tpl='.$tpl).($returnTask == null ? '': '&task='.$returnTask)."&d=$d&m=$m&y=$y", $msg, 'message' );
	}

    /**
	 * This function include/exclude clients which are in $_REQUEST['cid'] array
	 *
	 */
	function changeClientType( $type = 0  ) {
        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();

        $cidv	= JRequest::getVar( 'cid', 0 );
        $vid	= JRequest::getVar( 'vid', array( 0 ) );
        $cid	= array( 0 );

        if( is_array( $cidv ) ) {
            $cid = $cidv;
        }else{
            if( $cidv !== 0 ) {
                $cid[0] = $cidv;
            }
        }

        if( ( count( $vid ) > 0 ) && ( $vid[0] != 0 ) ) {
            $cid[0] = $vid;
        }

        if( count( $cid ) < 1 ) {
//            $task_name = $exclude ? 'excludeClients' : 'includeClients';
//            echo '<script type="text/javascript">alert(\'' . JTEXT::_( 'Please choose an entry to' ) . ': ' . $task_name . '\'); window.history.go(-1);</script>' . "\n";
            exit;
        }

        $cids = 0;
        if( count( $cid ) > 1 ) {
            $cids = implode( ',', $cid );
        }else{
            $cids = $cid[0];
        }

        //this is a better approach: $query = 'UPDATE #__jstats_ipaddresses SET ip_exclude = MOD(ip_exclude + 1, 2) WHERE ip IN (' . $cids . ')';

        $query = 'UPDATE #__jstats_clients'
        . ' SET client_type = \'' . ($type) . '\''
        . ' WHERE client_id IN (' . $cids . ')'
        ;

        $JSDatabaseAccess->db->setQuery( $query );
        if( !$JSDatabaseAccess->db->query() ) {
            echo '<script type="text/javascript">alert(\''.$JSDatabaseAccess->db->getErrorMsg().'\');window.history.go(-1);</script>' ."\n";
            exit();
        }

        if( $type = 0 ) {
            $msg = JTEXT::_( 'Type of Client(s) changed to unknown client' );
        }else if($type = 1){
            $msg = JTEXT::_( 'Type of Client(s) changed to browser' );
        }else{
            $msg = JTEXT::_( 'Type of Client(s) changed to bot' );
        }

        //redirect to approprate page
        $view = JRequest::getVar('returnView', 'visitors');

        // use vid parameter, because we didn't want to use extra paramater to parse
        if( ( count( $vid ) > 0 ) && ( $vid[0] !=0 ) ) {
            //in case if function is called from the statistics page return to it
          //  $view = 'r03';
        }

        $view = JRequest::getVar('returnView', JRequest::getVar('view', 'visitors'));
        $returnTask = JRequest::getVar('returnTask', null);

        $controller = JRequest::getVar('controller', null);
        $tpl = JRequest::getVar('tpl', null);
        $d = JRequest::getVar( 'd', '' );
		$m = JRequest::getVar( 'm', '' );
		$y = JRequest::getVar( 'y', '' );

        $this->setRedirect( "index.php?option=com_j4age&view=$view".($controller == null ? '': '&controller='.$controller).($tpl == null ? '': '&tpl='.$tpl).($returnTask == null ? '': '&task='.$returnTask)."&d=$d&m=$m&y=$y", $msg, 'message' );
	}

    function classifyAsBrowser()
    {
        $this->changeClientType( 1 );
    }

    function classifyAsBot()
    {
        $this->changeClientType( 2 );
    }
    function classifyAsGhost()
    {
        $this->changeClientType( 0 );
    }

        /**
	 * This function include/exclude clients which are in $_REQUEST['cid'] array
	 *
	 */
	function changeIPType( $type = 0  ) {
        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();

        $cidv	= JRequest::getVar( 'cid', 0 );
        $vid	= JRequest::getVar( 'vid', array( 0 ) );
        $cid	= array( 0 );

        if( is_array( $cidv ) ) {
            $cid = $cidv;
        }else{
            if( $cidv !== 0 ) {
                $cid[0] = $cidv;
            }
        }

        if( ( count( $vid ) > 0 ) && ( $vid[0] != 0 ) ) {
            $cid[0] = $vid;
        }

        if( count( $cid ) < 1 ) {
//            $task_name = $exclude ? 'excludeClients' : 'includeClients';
//            echo '<script type="text/javascript">alert(\'' . JTEXT::_( 'Please choose an entry to' ) . ': ' . $task_name . '\'); window.history.go(-1);</script>' . "\n";
            exit;
        }

        $cids = 0;
        if( count( $cid ) > 1 ) {
            $cids = implode( ',', $cid );
        }else{
            $cids = $cid[0];
        }

        //this is a better approach: $query = 'UPDATE #__jstats_ipaddresses SET ip_exclude = MOD(ip_exclude + 1, 2) WHERE ip IN (' . $cids . ')';

        $query = 'UPDATE #__jstats_ipaddresses'
        . ' SET ip_type = \'' . ($type) . '\''
        . ' WHERE ip IN (' . $cids . ')'
        ;

        $JSDatabaseAccess->db->setQuery( $query );
        if( !$JSDatabaseAccess->db->query() ) {
            echo '<script type="text/javascript">alert(\''.$JSDatabaseAccess->db->getErrorMsg().'\');window.history.go(-1);</script>' ."\n";
            exit();
        }

        if( $type = 0 ) {
            $msg = JTEXT::_( 'Type of IP(s) changed to unknown client' );
        }else if($type = 1){
            $msg = JTEXT::_( 'Type of IP(s) changed to browser' );
        }else{
            $msg = JTEXT::_( 'Type of IP(s) changed to bot' );
        }

        /*$query = 'UPDATE #__jstats_clients'
        . ' SET client_type = \'' . ($type) . '\''
        . ' WHERE client_id IN (' . $cids . ')'
        ;

        $JSDatabaseAccess->db->setQuery( $query );
        if( !$JSDatabaseAccess->db->query() ) {
            echo '<script type="text/javascript">alert(\''.$JSDatabaseAccess->db->getErrorMsg().'\');window.history.go(-1);</script>' ."\n";
            exit();
        }*/

        //redirect to approprate page
        $view = JRequest::getVar('returnView', 'visitors');

        // use vid parameter, because we didn't want to use extra paramater to parse
        if( ( count( $vid ) > 0 ) && ( $vid[0] !=0 ) ) {
            //in case if function is called from the statistics page return to it
          //  $view = 'r03';
        }

        $view = JRequest::getVar('returnView', JRequest::getVar('view', 'visitors'));
        $returnTask = JRequest::getVar('returnTask', null);

        $controller = JRequest::getVar('controller', null);
        $tpl = JRequest::getVar('tpl', null);
        $d = JRequest::getVar( 'd', '' );
		$m = JRequest::getVar( 'm', '' );
		$y = JRequest::getVar( 'y', '' );

        $this->setRedirect( "index.php?option=com_j4age&view=$view".($controller == null ? '': '&controller='.$controller).($tpl == null ? '': '&tpl='.$tpl).($returnTask == null ? '': '&task='.$returnTask)."&d=$d&m=$m&y=$y", $msg, 'message' );
	}

    function classifyIPAsBrowser()
    {
        $this->changeIPType( 1 );
    }

    function classifyIPAsGhost()
    {
        $this->changeIPType( 0 );
    }

    function classifyIPAsBot()
    {
        $this->changeIPType( 2 );
    }


    function excludeClients()
    {
        $this->clientExclution( true );
    }
    
    function includeClients()
    {
        $this->clientExclution( false );
    }

    function doIpExclude()
    {
        $this->excludeIpAddressArr( true );
    }
    function doIpInclude()
    {
        $this->excludeIpAddressArr( false );
    }

    function doExportCSV()
    {
        require_once( dirname(__FILE__) .DS.'..'.DS. 'libraries'.DS. 'export.php' );
        $JSExport = new js_JSExport();
        echo $JSExport->exportJSToCsv();
    }


    /**
     * Method to display the view
     *
     * @access    public
     */
    function display($surroundings = false)
    {
       //$this->JoomlaStatsEngine->FilterTimePeriod->hide = true;
       parent::display($surroundings);
    }
        /**
     * Method to display the view
     *
     * @access    public
     */
/*    function execute($task)
    {
       parent::execute($task);
    }*/
}

