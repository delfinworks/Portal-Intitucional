<?php

              
/**
 * @package j4age
 * @copyright Copyright (C) 2009-@THISYEAR@ j4age Team. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * Thank you to the project j4age and it's team, on which roots this project is build on.
 */
             
          

/**
 * This is file with basic classes
 *
 * It is used also in non-joomla environment
 *
 * Basic classes should:
 *	  - be small
 *	  - be well comented
 *	  - not generate any HTML code
 *    - should provide constants, defines
 *    - no bussines logic
 *    - no compatibility
 */
if( !defined( '_JS_STAND_ALONE' ) && !defined( '_JEXEC' ) ) {
	die( 'JS: No Direct Access to base classes' );
}



require_once( dirname(__FILE__) .DS. '..'.DS. 'database' .DS. 'db.constants.php' );
require_once( dirname(__FILE__) .DS. '..'.DS. 'database' .DS. 'access.php' );
require_once( dirname(__FILE__) .DS. '..'.DS. 'configuration.php' );

if (js_isJSDebugOn() == true) {
    jimport( 'joomla.error.profiler' );
}




/**
 * 'Joomla Stats' class that contain SYSTEM CONSTANTS (this class replace define('AAAA'); that are globals)
 *
 * All members are READ ONLY!
 */
class js_JSSystemConst
{
	/**
	 *  List of all JS tables
	 *  Use this list to uninstall datbase, optimize database etc.
	 */
	var $allJSDatabaseTables = array( '#__jstats_browsers', '#__jstats_configuration', '#__jstats_ipaddresses', '#__jstats_clients', '#__jstats_visitors', '#__jstats_keywords', '#__jstats_impressions', '#__jstats_pages', '#__jstats_referrer', '#__jstats_searchers', '#__jstats_systems', '#__jstats_visits', '#__ip2nation', '#__ip2nationCountries' );

	var $defaultPathToImagesTld     = 'tld-png-16x11-1';
	var $defaultPathToImagesOs      = 'os-png-14x14-1';
	var $defaultPathToImagesBrowser = 'browser-png-14x14-1';
}


/**
 * 'Joomla Stats' class that contain CURRENT 'Joomla Stats' configuration
 */
class js_JSConf extends js_JSConfDef
{
    //var $existingRows = array();
    var $show_installer = false;
    var $params = array();
    /**
     * Call this method as js_JSConf::getInstance();
     * @return #Vinstance_js_JSConf|js_JSConf|?
     */
    function &getInstance()
    {
       global $instance_js_JSConf;
       if(empty($instance_js_JSConf))
       {
          $instance_js_JSConf = new js_JSConf();
       }
       return $instance_js_JSConf;
    }

    function showInstaller()
    {
        return $this->show_installer;
    }

    function updateRequired()
    {
        $currentVersion = $this->JSVersion;
        $buildVersion = $this->BuildVersion;

        if( js_JSUtil::JSVersionCompare($currentVersion, $buildVersion, '<' ) )
        {
           return true;
        }
        if( $this->update_static_data )
        {
           return true;
        }
        return false;
    }

	/** Constructor load current configuration */
	function __construct( $initializeFromDatabase = true ) {
		parent::__construct();
		if( $initializeFromDatabase ) {
			$this->initializeByConfigurationFromDatabase();
		}
	}

	/**
	 *	This function read configuration stored in database and fill this class members
	 */
	function initializeByConfigurationFromDatabase() {

		$JSDatabaseAccess = js_JSDatabaseAccess::getInstance();

		$query = 'SELECT * FROM #__jstats_configuration';
		$JSDatabaseAccess->db->setQuery( $query );
		$rows = $JSDatabaseAccess->db->loadAssocList();
		if ($JSDatabaseAccess->db->getErrorNum() > 0) {
			$err_msg = 'Function: initializeByConfigurationFromDatabase() ' . $JSDatabaseAccess->db->getErrorMsg();
			js_echoJSDebugInfo($err_msg, '');
			return false;
		}


		foreach( $rows as $row )
        {
            /**
             * We try to remember what we have retrieved from the DB
             */
            $this->params[$row['description']] = $row['value'] == null ? null : $row['value'];

            if( $row['description'] == 'version' ) {
                $this->JSVersion = $row['value'] == null ? '0.0.0.0' : $row['value'];
            }
            if( $row['description'] == 'update_static_data' ) {
                $this->update_static_data = $row['value'] == null ? 0 : $row['value'];
            }
            else if( $row['description'] == 'onlinetime' ) {
				$this->onlinetime = $row['value'];
			}
            else if( $row['description'] == 'onlinetime_bots' ) {
				$this->onlinetime_bots = $row['value'];
			}
            else if( $row['description'] == 'current_step' ) {
				$this->current_step = $row['value'] == null ? 0 : $row['value'];
			}
            else if( $row['description'] == 'startoption' ) {
				$this->startoption = $row['value'];
			}
            else if( $row['description'] == 'startdayormonth' ) {
				$this->startdayormonth = $row['value'];
			}
            else if( $row['description'] == 'language' ) {
				$this->language = $row['value'];
			}
            else if( $row['description'] == 'include_summarized' ) {
				$this->include_summarized = ( $row['value'] === 'true' ) ? true : false;
			}
            else if( $row['description'] == 'show_summarized' ) {
				$this->show_summarized = ( $row['value'] === 'true' ) ? true : false;
			}
            else if( $row['description'] == 'enable_whois' ) {
				$this->enable_whois = ( $row['value'] === 'true' ) ? true : false;
			}
            else if( $row['description'] == 'enable_i18n' ) {
				$this->enable_i18n = ( $row['value'] === 'true' ) ? true : false;
			}
            else if( $row['description'] == 'show_charts_within_reports' ) {
				$this->show_charts_within_reports = ( $row['value'] === 'true' ) ? true : false;
			}
            else if( $row['description'] == 'component' ) {
				$this->component = $row['value'];
			}
		}

		return true;
	}

	/**
	 * This function write configuration (this class members) to database
	 *
	 * @param string $err_msg
	 * @return string
	 */
	function storeConfigurationToDatabase( &$err_msg ) {

        js_echoJSDebugInfo('Function: store() ', $this);
               
		$JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
        $this->store($JSDatabaseAccess, 'current_step', $this->current_step, $err_msg );
        $this->store($JSDatabaseAccess, 'enable_i18n', ( $this->enable_i18n ) ? 'true' : 'false', $err_msg );
        $this->store($JSDatabaseAccess, 'enable_whois', ( $this->enable_whois ) ? 'true' : 'false', $err_msg );
        $this->store($JSDatabaseAccess, 'show_summarized', ( $this->show_summarized ) ? 'true' : 'false', $err_msg );
        $this->store($JSDatabaseAccess, 'include_summarized', ( $this->include_summarized ) ? 'true' : 'false', $err_msg );
        $this->store($JSDatabaseAccess, 'startdayormonth', $this->startdayormonth, $err_msg );
        $this->store($JSDatabaseAccess, 'startoption', $this->startoption, $err_msg );
        $this->store($JSDatabaseAccess, 'onlinetime', $this->onlinetime, $err_msg );
        $this->store($JSDatabaseAccess, 'language', $this->language, $err_msg );
        $this->store($JSDatabaseAccess, 'version', $this->JSVersion, $err_msg );
        $this->store($JSDatabaseAccess, 'update_static_data', $this->update_static_data, $err_msg );
        $this->store($JSDatabaseAccess, 'onlinetime_bots', $this->onlinetime_bots, $err_msg );
        $this->store($JSDatabaseAccess, 'show_charts_within_reports', ( $this->show_charts_within_reports ) ? 'true' : 'false', $err_msg );
        $this->store($JSDatabaseAccess, 'component', $this->component, $err_msg );

		return true;
	}

    function store(&$JSDatabaseAccess, $key, $value, &$err_msg)
    {
        //$valueFromDB = $this->existingRows[$key];
        $query = $this->getSQL($key, $value);
        $JSDatabaseAccess->db->setQuery( $query );
        $JSDatabaseAccess->db->query();
        if ($JSDatabaseAccess->db->getErrorNum() > 0) {
            $err_msgEntry = $JSDatabaseAccess->db->getErrorMsg();
            js_echoJSDebugInfo('Function: store() ' . $err_msgEntry, '');
            $err_msg .= $err_msgEntry;
        }
    }

    function getSQL($key, $value)
    {
        //$valueFromDB = $this->existingRows[$key];
        $query = null;
        $key_exists = array_key_exists($key, $this->params);
        if($key_exists)
        {
            $query = "UPDATE #__jstats_configuration SET value = ". ($value == null ? 'NULL':"'$value'")." WHERE description = '$key'";
        }
        else
        {
            $query = "INSERT INTO #__jstats_configuration (description, value) VALUES ('$key',". ($value == null ? 'NULL':"'$value'").")";
        }
        return $query;
    }

    function getParam($key)
    {
        if(!isset($this->params[$key])) return null;
        return $this->params[$key];
    }

    function hasParam($key)
    {
        return array_key_exists($key, $this->params);
    }
}



/**
 *  This class contain (hold) data about visitor
 *
 *  This class is only container for data - to pass data through methods etc.
 *
 *  Members of this class corespond to database table #__jstats_ipaddresses (will be renamed to #__jstats_visitors) column names
 *
 *  NOTICE:
 *     Creating new object create unknown Visitor. This is proper feature.
 */
class js_Visitor
{
	/** visitor ID */
	var $visitor_id         = null;
    /** visitor ID */
    var $note         = null;
    /** visitor ID */
    var $visitor_exclude         = false;

    /** visitor ID */
//    var $ip_id         = 0;

    /** client ID */
//    var $client_id          = 0;

	/** visitor IP address //value directly taken from visitor //@todo: example is missing (v6 also?) //@todo: missing value initialization */
//	var $visitor_ip         = null;

	/** hold string //value directly taken from visitor //eg.: "mozilla/5.0 (windows; u; windows nt 5.1; en-gb; rv:1.8.1.15) gecko/20080623 firefox/2.0.0.15" */
//	var $visitor_useragent  = '';	// User agent (i.e. browser)

	/** Requested page URL //value directly taken from visitor //@todo: example is missing */
	//var $RequestedPage    = null;

	/** true if user is excluded from counting statistics */
//	var $visitor_exclude    = 0;//probably there must be int

//    var $client_exclude    = 0;//probably there must be int
//    var $ip_exclude    = 0;//probably there must be int

	/** Visitor type: _JS_DB_IPADD__TYPE_NOT_IDENTIFIED_VISITOR, _JS_DB_IPADD__TYPE_REGULAR_VISITOR, _JS_DB_IPADD__TYPE_BOT_VISITOR; Defines are in db.constants.php file
	 *
	 *  Visitor type depend on $this->Browser->browser_id
	 *    RANGES (browser_id):
	 *               0  - unknown
	 *       1 -   511  - JS defined internet browsers (1 - unknown browser)
	 *     512 -  1023  - user defined internet browsers (user can add here own browsers)
	 *    1024 -  2047  - JS defined bots/spiders/crawlers (1024 - unknown bot)
	 *    2048 - 65535  - user defined internet bots/spiders/crawlers (user can add here own bots/spiders/crawlers)
	 */
//	var $visitor_type       = _JS_DB_IPADD__TYPE_NOT_IDENTIFIED_VISITOR;

	/** It holds object of class js_OS */
//	var $OS                 = null;

	/** it contain object of class js_Browser (Visitor internet browser or Bot) - one data hold in two member - @todo)*/
//	var $Browser            = null;

	/** It holds object of class js_Tld */
//	var $Tld                = null;

	/** Valid only when $Type = _JS_DB_IPADD__TYPE_REGULAR_VISITOR; eg.: "7.0" //(Connected with $BrowserName gives "Internet Explorer 7.0") */
//	var $browser_version  = '';

	/** ? URL @todo: example is missing. See JS trackers for details */
	//var $screen_x		= 0;
	//var $screen_y		= 0;

	/** String returned by PHP method gethostbyaddr( $visitor_ip ); If gethostbyaddr( $visitor_ip ); return $visitor_ip this member will contain empty string (''). eg.: "crawl-66-249-70-72.googlebot.com", "sewer.com.eu", but not "66.249.70.72" */
//	var $nslookup		= '';//in PHP documentation it is called 'Internet host name'
}

/**
 *  This class contain (hold) data about the location of an visitor (IP Address)
 *
 *  This class is only container for data - to pass data through methods etc.
 *
 *  Members of this class corespond to database table #__jstats_ipaddresses
 *
 *  NOTICE:
 *     Creating a new instance of the object creates a unknown Location (IP = 0).
 */
class js_Location
{
    /** visitor IP address //value directly taken from visitor //@todo: example is missing (v6 also?) //@todo: missing value initialization */
    var $ip         = null;

    var $ip_exclude    = null;

	/** IP type: _JS_DB_IPADD__TYPE_NOT_IDENTIFIED_VISITOR OR _JS_DB_IPADD__TYPE_BOT_VISITOR; Defines are in db.constants.php file
     *
     * It currently makes only sense to leave the value on 0 (=unknown => not effect) or set to 2 (= Bot => make all visitors from the IP to bots)
	 *
	 *  IP type is manually assigned to entries, which the user identifies as bots and are not recognised automatically with your JS logic
	 */
	var $ip_type       = null;

	/** It holds object of class js_Tld */
	var $Tld                = null;

	/** Holds the city, if determined */
	var $city  = null;

    /** Holds the country 2 letters, if determined */
    var $code  = null;

	/** String returned by PHP method gethostbyaddr( $visitor_ip ); If gethostbyaddr( $visitor_ip ); return $visitor_ip this member will contain empty string (''). eg.: "crawl-66-249-70-72.googlebot.com", "sewer.com.eu", but not "66.249.70.72" */
	var $nslookup		= '';//in PHP documentation it is called 'Internet host name'

    function init(&$dbentry)
    {
       $this->ip = $dbentry->ip;
       $this->ip_exclude = $dbentry->ip_exclude;
       $this->ip_type = $dbentry->ip_type;

       if(!empty($dbentry->code))
       {
          $this->Tld = new js_Tld();
          //$this->Tld->tld_id = $dbentry->tld_id;
          //Needs to be empty otherwise we would indicate that we have retrieved the data from the DB
          $this->Tld->tld       = $dbentry->code;
          $this->Tld->tld_name  = null;
          $this->Tld->tld_img   = $dbentry->code;
       }
       if(isset($dbentry->city))
       {
           $this->city = $dbentry->city;
       }
       if(isset($dbentry->code))
       {
           $this->code = $dbentry->code;
       }
       if(isset($dbentry->nslookup))
       {
           $this->nslookup = $dbentry->nslookup;
       }
    }
}

/**
 *  This class contain (hold) data about visitor
 *
 *  This class is only container for data - to pass data through methods etc.
 *
 *  Members of this class corespond to database table #__jstats_ipaddresses (will be renamed to #__jstats_visitors) column names
 *
 *  NOTICE:
 *     Creating new object create unknown Visitor. This is proper feature.
 */
class js_Client
{
    /** client ID */
    var $client_id          = 0;

    /** client ID */
    var $visitor_id          = 0;

	/** hold string //value directly taken from visitor //eg.: "mozilla/5.0 (windows; u; windows nt 5.1; en-gb; rv:1.8.1.15) gecko/20080623 firefox/2.0.0.15" */
	var $useragent  = null;	// User agent (i.e. browser)

	/** Requested page URL //value directly taken from visitor //@todo: example is missing */
	//var $RequestedPage    = null;

    var $client_exclude    = 0;

	/** Visitor type: _JS_DB_IPADD__TYPE_NOT_IDENTIFIED_VISITOR, _JS_DB_IPADD__TYPE_REGULAR_VISITOR, _JS_DB_IPADD__TYPE_BOT_VISITOR; Defines are in db.constants.php file
	 *
	 *  Visitor type depend on $this->Browser->browser_id
	 *    RANGES (browser_id):
	 *               0  - unknown
	 *       1 -   511  - JS defined internet browsers (1 - unknown browser)
	 *     512 -  1023  - user defined internet browsers (user can add here own browsers)
	 *    1024 -  2047  - JS defined bots/spiders/crawlers (1024 - unknown bot)
	 *    2048 - 65535  - user defined internet bots/spiders/crawlers (user can add here own bots/spiders/crawlers)
	 */
	var $client_type       = _JS_DB_IPADD__TYPE_NOT_IDENTIFIED_VISITOR;

	/** It holds object of class js_OS */
	var $OS                 = null;

	/** it contain object of class js_Browser (Visitor internet browser or Bot) - one data hold in two member - @todo)*/
	var $Browser            = null;

	/** Valid only when $Type = _JS_DB_IPADD__TYPE_REGULAR_VISITOR; eg.: "7.0" //(Connected with $BrowserName gives "Internet Explorer 7.0") */
	var $browser_version  = null;

    /** This flag is set to true during the counting process, if we see existing cookies or the "u" flag in the useragent string */
    var $cookie_support  = false;

    function init(&$dbentry)
    {
       $this->client_id = $dbentry->client_id;
       $this->visitor_id = $dbentry->visitor_id;
       $this->useragent = $dbentry->useragent;
       $this->client_exclude = $dbentry->client_exclude;
       $this->client_type = $dbentry->client_type;
       $this->browser_version = $dbentry->browser_version;

       /**
        * We won't have a visitor_id, if the client would not support cookies at all
        */
       if(!empty($this->visitor_id))
       {
           $this->cookie_support = true;
       }
       if(!isset($dbentry->os_id))
       {
           $this->OS = new js_OS();
           $this->OS->init($dbentry);
       }
       if(!isset($dbentry->browser_id))
       {
           $this->Browser = new js_Browser();
           $this->Browser->init($dbentry);
           //This is stored on client leve, but we move it down, too
           $this->Browser->version = $this->browser_version;
       }
    }
}

/**
 *  This class contain (hold) data about Operating System (OS)
 *
 *  This class is only container for data - to pass data through methods etc.
 *
 *  Members of this class corespond to database table #__jstats_systems (will be renamed to #__jstats_os) column names
 *     and virtual table #__jstats_ostype (those tables will be merged soon)
 *
 *  NOTICE:
 *     Creating new object create unknown OS. This is proper feature.
 */
class js_OS
{
	/** Primary Key from table #__jstats_os from column sys_id */
	var $os_id          = null;//_JS_DB_OS__ID_UNKNOWN is equeal 0

	/** Primary Key from table #__jstats_ostype from column sys_id */
	var $os_type      = null;

	/** String that idetify OS eg.: "winme"; "windows nt 6.0"; "linux"; */
	var $os_key         = null;

	/** Human friendly OS name eg.: "Windows XP"; "Windows Vista"; "Mac OS"; "Linux"; */
	var $os_name        = null;

	/** Name of image file without extension eg.: "windowsxp"; "windowsvista"; "mac"; Extension is taken from directory name */
	var $os_img         = null;

	/** Human friendly OS Type name eg.: "Windows"; "PDA or Phone"; "Other"; */
	var $ostype_name    = null;

	/** Name of image file without extension eg.: "unknown"; "windowsxp"; "linux"; "other"; "pda"; See defines _JS_DB_OSTYP for all available names. Extension is taken from directory name. */
	var $ostype_img     = null;

    /** Name of image file without extension eg.: "unknown"; "windowsxp"; "linux"; "other"; "pda"; See defines _JS_DB_OSTYP for all available names. Extension is taken from directory name. */
    var $os_ordering     = null;

    /**
     * Returns the entry for an Unknown client.
     *
     * Attention: Do not set the variables directly to the "default" values as the system needs to know if a value is set or not
     *
     * @return void
     */
    function getUnknownOS()
    {
        $OS = new js_OS();
        $OS->os_id          = _JS_DB_OS__ID_UNKNOWN;
        $OS->os_type      = _JS_DB_OSTYP__ID_UNKNOWN;
        $OS->os_key         = _JS_DB_OS__KEY_UNKNOWN;
        $OS->os_name        = _JS_DB_OS__NAME_UNKNOWN;
        $OS->os_img         = _JS_DB_OS__IMG_UNKNOWN;
        $OS->ostype_name    = _JS_DB_OSTYP__NAME_UNKNOWN;
        $OS->ostype_img     = _JS_DB_OSTYP__IMG_UNKNOWN;
        return $OS;
    }

    function init(&$dbentry)
    {
       if(!isset($dbentry->os_id))
       {
           $this->os_id          = $dbentry->os_id;
           $this->os_type        = $dbentry->os_type;
           $this->os_key         = $dbentry->os_key;
           $this->os_name        = $dbentry->os_name;
           $this->os_img         = $dbentry->os_img;
           $this->os_ordering    = $dbentry->os_ordering;
       }
    }
}


/**
 *  This class contain (hold) data about Browsers
 *
 *  This class is only container for data - to pass data through methods etc.
 *
 *  Members of this class corespond to database table #__jstats_browsers merged with #__jstats_browserstype (virtual table) column names
 *
 *  NOTICE:
 *     Creating new object create unknown Browser. This is proper feature.
 */
class js_Browser
{
	/** Primary Key from table #__jstats_browser from column browser_id */
    //Andreas Halbig: Keep the id null otherwise it gets hard to see for an update process if the value is supposed to be empty or ID 0 (=Unknown browser)
	var $browser_id        = null; //_JS_DB_BRWSR__ID_UNKNOWN;//_JS_DB_BRWSR__ID_UNKNOWN is equeal 0

	/** Primary Key from table #__jstats_browsertype from column browsertype_id */
	var $browsertype_id    = _JS_DB_BRTYP__ID_UNKNOWN;

	/** String that idetify browser eg.: "msie"; "firefox" */
	var $browser_key       = _JS_DB_BRWSR__KEY_UNKNOWN;

	/** Human friendly Browser name eg.: "Internet Explorer"; "Google Chrome"; "FireFox"; "Netscape" */
	var $browser_name      = _JS_DB_BRWSR__NAME_UNKNOWN;

	/** Name of image file without extension eg.: "explorer"; "netscape"; "noimage"; "firefox"; Extension is taken from directory name. */
	var $browser_img       = _JS_DB_BRWSR__IMG_UNKNOWN;

	/** not enough time to implement - @todo */
	var $browsertype_name  = _JS_DB_BRTYP__NAME_UNKNOWN;

	/** not enough time to implement - @todo */
	/** Name of image file without extension eg.: "unknown"; "explorer"; "other"; "pda"; See defines _JS_DB_BRWSR__TYPE_ for all available names. Extension is taken from directory name. */
	var $browsertype_img   = _JS_DB_BRTYP__IMG_UNKNOWN;

    var $browser_location = 2; //Search Product Name

    var $browser_type = 0;  // 0 = Unknown

    var $browser_ordering = 0;

    var $products = array();
    var $version = '';

    function init(&$dbentry)
    {
       if(!isset($dbentry->browser_id))
       {
           $this->browser_id     = $dbentry->browser_id;
           if(isset($dbentry->browsertype_id))
           {
               $this->browsertype_id = $dbentry->browsertype_id;
               $__jstats_browserstype = unserialize(_JS_DB_TABLE__BROWSERSTYPE);//whole table
               //fill missing entries in $Browser object
               $this->browsertype_name = $__jstats_browserstype[$this->browsertype_id]['browsertype_name'];
               $this->browsertype_img  = $__jstats_browserstype[$this->browsertype_id]['browsertype_img'];
           }

           $this->browser_key    = $dbentry->browser_key;
           $this->browser_name   = $dbentry->browser_name;
           $this->browser_img    = $dbentry->browser_img;

           $this->browser_location       = $dbentry->browser_location;
           $this->browser_type           = $dbentry->browser_type;
           $this->browser_ordering = $dbentry->browser_ordering;

           if(isset($dbentry->browser_version))
           {
               $this->version = $this->browser_version;
           }
       }

    }
}



/**
 *  This class contain (hold) data about Top Level Domains (TLD)
 *
 *  This class is only container for data - to pass data through methods etc.
 *
 *  Members of this class corespond to database table #__jstats_topleveldomains (will be renamed to #__jstats_tlds) column names
 *
 *  NOTICE:
 *     Creating new object create unknown TLD. This is proper feature.
 */
class js_Tld
{
	/** Primary Key from #__jstats_tldstable - integer. */
	//var $tld_id    = _JS_DB_TLD__ID_UNKNOWN;

	/** Shortcuted name. Always lowercase eg.: "us", "de", "pl", "" (empty for unknown) */
	var $tld       = _JS_DB_TLD__TLD_UNKNOWN;

	/** Human redable country name eg.: "United States", "Germany", "Unknown" */
	var $tld_name  = _JS_DB_TLD__NAME_UNKNOWN;

	/** NOTICE: This variable is only for code clarity - it contains the same as $tld! Name of image file without extension eg.: "us"; "de"; "pl"; "unknown"; Extension is taken from directory name. */
	var $tld_img   = _JS_DB_TLD__TLD_UNKNOWN;

    function &getDefault()
    {
       $tld = new js_Tld();
       //$tld->tld_id    = _JS_DB_TLD__ID_UNKNOWN;
       $tld->tld       = _JS_DB_TLD__TLD_UNKNOWN;
       $tld->tld_name  = _JS_DB_TLD__NAME_UNKNOWN;
       $tld->tld_img   = _JS_DB_TLD__TLD_UNKNOWN;
       return $tld;
    }
}

/** This function return timezone for JoomlaStats.
 *  Returned time zone is for anonymous front page users!
 *  @return double (eg. 1, 2, -9.5, 10.5)
 *
 *  Timezone should be always get through this function.
 *  For details see http://www.joomlastats.org:8080/display/JS/FAQ+Wrong+time+in+JoomlaStats and http://www.joomlastats.org:8080/display/JS/FAQ+Time+and+Time+Zones+in+JoomlaStats
 */
function js_getJSTimeZone() {

	$TZOffset = 0;

	//one of this HAVE TO be defined - if not this is serious bug
	if( defined( '_JEXEC' ) ) {
		// Joomla! 1.5
        //$mainframe = JFactory::getApplication();

		//$TZOffset = $mainframe->getCfg( 'offset' );
        $config =& JFactory::getConfig();
        $TZOffset = $config->getValue('config.offset');

		//// code from JDate
		//$_date = strtotime(gmdate("M d Y H:i:s", time()));
		//$date_a = $_date + $offset*3600;
		//$date_str = date('Y-m-d H:i:s', $date_a);
		//js_echoJSDebugInfo('Loc:', $date_str);
		//
		//$gm_date = gmdate("M d Y H:i:s", time());
		//js_echoJSDebugInfo('GMT time:', $gm_date);
	} else if( defined( '_JS_STAND_ALONE' ) ) {
		//stand alone
		require_once( dirname(__FILE__) .DS. '..'.DS. 'database' .DS. 'stand.alone.configuration.php' );
		$JSStandAloneConfiguration = new js_JSStandAloneConfiguration();
		$TZOffset = $JSStandAloneConfiguration->JConfigArr['offset'];
	}

	return $TZOffset;
}

/** This function return timestamp for now for j4age.
 *  Current time should be always get through this function.
 *
 *  Returned timestamp is in timezone for anonymous front page users!
 *
 *  For details see http://www.joomlastats.org:8080/display/JS/FAQ+Wrong+time+in+JoomlaStats and http://www.joomlastats.org:8080/display/JS/FAQ+Time+and+Time+Zones+in+JoomlaStats
 */
function js_getJSNowTimeStamp() 
{
    global $js_nowTimestamp;
    if($js_nowTimestamp == null){
        $js_nowTimestamp = time();
    }
    return $js_nowTimestamp;
	//return (time() + (js_getJSTimeZone() * 3600));
}

/** Use this function insted of PHP gmdate() to format date!!!
 *
 *  This function is connected with js_getJSNowTimeStamp() and js_getJSTimeZone()
 *  and provided to easier and reliable change in case of replace gmdate() to date() etc.
 */
function js_gmdate($format, $timestamp=null) {
	if ($timestamp===null)
		return gmdate($format, js_getJSNowTimeStamp());

	return gmdate($format, $timestamp);
}

/** This function return true if debug mode is turned on */
function js_isJSDebugOn($globalDebugging = false) {

    if($globalDebugging)
    {
        //one of this HAVE TO be defined - if not this is serious bug
        if( defined( '_JEXEC' ) ) {
            // Joomla! 1.5
            $conf =& JFactory::getConfig();
            $isJSDebugOn = (boolean) $conf->getValue('config.debug');
        } else if( defined( '_JS_STAND_ALONE' ) ) {
            //stand alone
            require_once( dirname(__FILE__) .DS. '..'.DS. 'database' .DS. 'stand.alone.configuration.php' );
            $JSStandAloneConfiguration = new js_JSStandAloneConfiguration();
            $isJSDebugOn = (boolean) $JSStandAloneConfiguration->JConfigArr['debug'];
        }
        return $isJSDebugOn;
    }

    /**
     * This is called quite often, so we have to prevent any recurrent tasks
     */
    global $isJSDebugOn;
    if($isJSDebugOn === null)
    {
        //This enables to debug any browser without falling in the debug mode for all visitors
        $isJSDebugOn = false;

        $isJSDebugOnFlag = JRequest::getVar('js_debug', null, "GET");

        if($isJSDebugOnFlag !== null)
        {
            if($isJSDebugOnFlag)
            {
                $isJSDebugOn = true;
                setcookie("js_debug", "1", 2147483647 , '/');
            }
            else
            {
                $isJSDebugOn = false;
                if(isset($_COOKIE['js_debug']))
                {
                    setcookie("js_debug", "", time(NULL) - 3153600 , '/');
                }
            }

        }
        else
        {
            $isJSDebugOn = false;
            if(isset($_COOKIE['js_debug']))
            {
               $isJSDebugOn = true;
            }
        }

        if($isJSDebugOn)
        {
           $JSDatabaseAccess =& js_JSDatabaseAccess::getInstance();
           $JSDatabaseAccess->debug(true);
           return $isJSDebugOn;
        }
        else
        {

        }

        //one of this HAVE TO be defined - if not this is serious bug
        if( defined( '_JEXEC' ) ) {
            // Joomla! 1.5
            $conf =& JFactory::getConfig();
            $isJSDebugOn = (boolean) $conf->getValue('config.debug');
        } else if( defined( '_JS_STAND_ALONE' ) ) {
            //stand alone
            require_once( dirname(__FILE__) .DS. '..'.DS. 'database' .DS. 'stand.alone.configuration.php' );
            $JSStandAloneConfiguration = new js_JSStandAloneConfiguration();
            $isJSDebugOn = (boolean) $JSStandAloneConfiguration->JConfigArr['debug'];
        }
    }
    return $isJSDebugOn;
}

/**
 *  Print info when Debug is turned on.
 *  $title - use '' to not display title in bold (<b></b>)
 *  $pre   - use '' to not display pre in preformated block (tabulations, spaces and end of lines are visible) (<pre></pre>)
 *
 *  $pre accept also objects!!
 */
function js_echoJSDebugInfo($title, $pre = '') {

	if (js_isJSDebugOn() == true) {
        global $js_lastdebugMsg;
        global $js_lastSQLIndex;

        if($js_lastSQLIndex == null)
        {
           $js_lastSQLIndex = 0;
        }

        list($usec, $sec) = explode(' ', microtime());
        $timestamp = ((float)$usec + (float)$sec);

        if($js_lastdebugMsg == null)
        {
            $js_lastdebugMsg = $timestamp;
        }
        $secondspassed = $timestamp - $js_lastdebugMsg;
        $js_lastdebugMsg = $timestamp;
        
		$msg = '<br/>['.sprintf('%.4f', $secondspassed).'s] DEBUG info j4age: <b>'.$title.'</b>';
		if ( $pre !== '' ) {
			if ( (is_object($pre) == true) || (is_array($pre) == true)) {
				$msg .= '<pre>'.print_r($pre, true).'</pre>';
			} else {
				$msg .= ': \''.$pre.'\'';
			}
		}
		$msg .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

        $JSDatabaseAccess =& js_JSDatabaseAccess::getInstance();

        $logs =& $JSDatabaseAccess->db->getLog();
        if(!empty($logs))
        {
            $msg .= '<p><strong>Performed SQL enquiries</strong></p>';
           for(;$js_lastSQLIndex < count($logs);$js_lastSQLIndex++)
           {
                  $log = $logs[$js_lastSQLIndex];
                  $msg .= '<p>'.htmlentities($log).'</p>';
           }
           $logs =  array();     //$JSDatabaseAccess->db->_log =
        }

		echo $msg;
	}
}

/**
 *  Print info when Debug is turned on.
 *  $title - use '' to not display title in bold (<b></b>)
 *  $pre   - use '' to not display pre in preformated block (tabulations, spaces and end of lines are visible) (<pre></pre>)
 *
 *  $pre accept also objects!!
 */
function js_profilerMarker($title, $instance = 'JS') {

	if (js_isJSDebugOn() == true) {
        $prof = & JProfiler::getInstance( $instance );
        js_echoJSDebugInfo($prof->mark($title));
	}
}


function js_ip2long($ipaddressStr)
{
   $long = ip2long($ipaddressStr); //signed
   return sprintf('%u', $long); //unsigned
}


// needed if php4 is used, because stripos is a php5 > only function
if( !function_exists( 'stripos' ) ) {
	function stripos( $haystack, $needle, $offset = 0 ) {
		return strpos( strtolower( $haystack ), strtolower( $needle ), $offset );
	}
}

/**
 * We are using the Toolbar logic to render buttons as they are done within the Toolbar
 *
 * Simply pass the same paramters as you would to for the JToolbarHelper!
 *
 * @return #M#Vbar.renderButton|?
 *
 */
function js_renderButton()
{
    // Push button onto the end of the toolbar array
    $btn = func_get_args();
    $bar = & JToolBar::getInstance('toolbar');

    $type = $btn[0];

    $button = &$bar->loadButtonType($type);

    /**
     * Error Occurred
     */
    if ($button === false) {
        return JText::_('Button not defined for type').' = '.$type;
    }
    return call_user_func_array(array(&$button, 'fetchButton'), $btn);
    //return $button->fetchButton($btn);
}

function js_renderPopupIcon( $img = '', $text = '', $url = '', $tooltip = '', $width=640, $height=480, $top=0, $left=0, $imgwidth=16, $imgheight=16 )
{
    return js_renderIcon($img, $text, $url, $tooltip, $width, $height, $top, $left, $imgwidth, $imgheight, true);
}

function js_renderIcon( $img = '', $text = '', $url = '', $tooltip = '', $width=640, $height=480, $top=0, $left=0, $imgwidth=16, $imgheight=16, $popup = false )
{
    JHTML::_('behavior.modal');

    $bar = & JToolBar::getInstance('toolbar');

    $button = &$bar->loadButtonType('Popup');

    $text	= JText::_($text);
    //$class	= $this->fetchIconClass($name);
    $doTask = $url;
    if (substr($url, 0, 4) !== 'http') {
        $doTask = JURI::base().$doTask;
    }
    //does not work for Joomla 1.6: $doTask	= $button->_getCommand($img, $url, $width, $height, $top, $left);
    $relStr = '';
    $classStr = '';
    if($popup)
    {
       $relStr = "rel=\"{handler: 'iframe', size: {x: $width, y: $height}}\"";
       $classStr = "class=\"modal\"";
    }

    $html	= "<a $classStr href=\"$doTask\" $relStr>\n";
    if(!empty($img))
    {
        $html .= "<img src=\"$img\" border=\"0\" height=\"$imgheight\" width=\"$imgwidth\" title=\"$tooltip\"/>";
    }
    $html	.= "$text\n";
    $html	.= "</a>\n";

    return $html;
}

function js_formatGMTTimestamp($gmttimestamp)
{
    $time =& js_getDate($gmttimestamp);
    $time_str = $time->toFormat();
    return $time_str;
}

function js_getDate($gmttimestamp)
{
    global $js_difference;
    if($js_difference == null)
    {
        $serveroffset = date('Z') / 3600;
        $offset =  js_getJSTimeZone();
        $js_difference = $serveroffset - $offset;
    }
    $time =& JFactory::getDate($gmttimestamp, $js_difference);
    return $time;
}


/**
 * @param  $viewName
 * @param  $type
 * @return view object, if exists or <null> if it does not exist
 */
function js_getView($viewName, $type = '')
{
    $viewName = strtolower(trim($viewName));
    /**
     * We only show links, if there view is physically available
     * @author Andreas Halbig
     */
    if(!empty($type))
    {
       $type = '.'.$type;
    }
    $config = js_JSConf::getInstance();

    if(!$config->show_charts_within_reports)
    {
        if($viewName == 'amline' || $viewName == 'ampie')
        {
            return null;
        }
    }
    if( !file_exists( dirname(__FILE__) .DS. '..'.DS.'views' .DS. $viewName .DS. 'view'.$type.'.php' ) )
    {
        return null;
    }     

    global $js_controller;

    return  $js_controller->getView($viewName, $type);
}

function js_gethostbyaddr( $IpAddressStr )
{
    global $js_gethostbyaddr;

    if($js_gethostbyaddr == null)
    {
        $js_gethostbyaddr = array();
    }
    $nsLookup = isset($js_gethostbyaddr[$IpAddressStr])? $js_gethostbyaddr[$IpAddressStr] : null;
    if(!empty($nsLookup))
    {
       return $nsLookup;
    }
    $nsLookup = gethostbyaddr( $IpAddressStr );
    $js_gethostbyaddr[$IpAddressStr] = $nsLookup;
    return $nsLookup;
}

/**
 * This class is supposed to be used by any kind of JS extension, to give us a central control for all extensions
 *
 * This class represents the interface between the JS component and any kind of functionality to be extended.
 */
class ComponentExtension
{
    var $id = null;
    var $published = 1;
    var $config;
    var $name;
    var $creationdate;
    var $author;
    var $authorUrl;
    var $authorEmail;
    var $copyright;
    var $version;

    /**
     * The type helps us to determine, if this plugin needs to be loaded
     *
     * value = 1 always
     * value = 2 component
     * value = 4 module
     *
     * never use value 0, which indicates that this plugin is not published
     *
     * @return int
     */
    function getPluginType()
    {
        return 1;
    }

    function isProtected()
    {
        return false;
    }

    function __construct( $config = null ) {
        if($config == null)
        {
            $config = new stdClass();
        }
        $this->config = $config;
        $this->init();
    }

    /**
     * Do not overwrite!!
     *
     * @return void
     */
    function trigger( $event, $args = array())
    {
       js_profilerMarker('Execute Module '.$this->id. ' - Start '.$event);
       $this->execute($event, $args);
       js_profilerMarker('Execute Module '.$this->id. ' - End '.$event);
    }

    /**
     * Overwrite & Fill your logic in to init the module
     * @return void
     */
    function init()
    {

    }

    /**
     * Overwrite & Fill your logic hin here
     * @return void
     */
    function execute($event, $args = array())
    {
       return call_user_func_array(array($this, $event), $args);

       //$this->$event(&$source);
       //$this->display();
    }

    /**
     * Returns a array of event names on which this plugin should be called.
     *
     * Please make sure that there is a corresponding method available in the format of
     *
     * <eventname>( $source, $options )
     *
     * @return array()
     */
    function getObservedEvents()
    {
        return array();
    }
}

class js_PluginManager
{
	/** list all extension files found in the extensions directory */
	function &loadAvailablePlugins( $type = 1, $loadXML = false )
    {
        global $js_event_observers;

        if($js_event_observers == null)
        {
           $js_event_observers = array();
        }

        $JSDatabaseAccess =& js_JSDatabaseAccess::getInstance();

		$list = array();
//                ini_set('display_errors','Off');
//                error_reporting(E_ALL);
        $filter = '';
        if($type == -1)
        {
            //show all
           $filter =  " and (value not like '0' or value IS NOT NULL)";
        }
        else
        {
           $filter =  " and value = '".($type)."'"; 
        }

		$query="select * from `#__jstats_configuration` where description like 'ext_%' $filter";
		$JSDatabaseAccess->db->setQuery($query);
		$rows = $JSDatabaseAccess->db->loadObjectList();
        /*$handle = opendir($path);
        while (($file = readdir($handle)) !== false)
		{
           $dir = $path . DS . $file;
		   $isDir = is_dir($dir);
           if($isDir)
           {
              //Ignore subfolders
              continue;
           }
           if(strripos('.xml'))
           {
               if(stripos('ext_'))
               {
                   
               }
           }
        }*/
        js_echoJSDebugInfo(count($rows)." Plugins found in DB");
        $plugin = null;
		foreach ($rows as $row)
        {
            $plugin_name = strtolower(trim($row->description));
            $plugin_access = trim($row->value);

            if(!file_exists(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_j4age'.DS.'extensions'.DS.$plugin_name.'.php'))
            {
               continue; 
            }
            require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_j4age'.DS.'extensions'.DS.$plugin_name.'.php');
            
            $name = substr ( $plugin_name , 4 );
            $classname	= 'extComponent'.$name;
            $config = js_PluginManager::stringToObject($row->params);
            unset($plugin);
            $plugin = new $classname( $config );
            $plugin->id = $plugin_name;
            $plugin->published = $plugin_access;

            $list[$plugin_name] =& $plugin;
            
            if($loadXML)
            {
                $xml = JFactory::getXMLParser('Simple');

                /*if (!defined('DOMIT_INCLUDE_PATH') )
                {
                    require_once (JPATH_SITE.DS.'libraries'.DS.'domit'.DS.'xml_domit_lite_parser.php');
                }*/

                // path to module directory
                $extensionBaseDir = JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions'.DS;

                // xml file for module
                $xmlfile = $extensionBaseDir. $plugin_name. ".xml";

                if (file_exists( $xmlfile )) {

                    if (!$xml->loadFile( $xmlfile))
                    {
                        js_echoJSDebugInfo("Unable to load extension xml");
                        continue;
                    }

                    $root =& $xml->document;
                    //$root = &$xmlDoc->documentElement;

                    if ($root->name() != 'install' && $root->name() != 'mosinstall')
                    {
                        js_echoJSDebugInfo("Unexpected root element");
                        continue;
                    }
                    if ($root->attributes( "type" ) != "js_ext")
                    {
                        js_echoJSDebugInfo("Unexpected type in xml");
                        continue;
                    }


                    $element 			= &$root->getElementByPath( 'name' );
                    $plugin->name		= trim($element ? $element->data() : '');

                    $element 			= &$root->getElementByPath( 'creationdate');
                    $plugin->creationdate = trim($element ? $element->data() : '');

                    $element 			= &$root->getElementByPath( 'author' );
                    $plugin->author 	= trim($element ? $element->data() : '');

                    $element 			= &$root->getElementByPath( 'copyright');
                    $plugin->copyright 	= trim($element ? $element->data() : '');

                    $element 			= &$root->getElementByPath( 'authoremail' );
                    $plugin->authorEmail 	= trim($element ? $element->data() : '');

                    $element 			= &$root->getElementByPath( 'authorurl');
                    $plugin->authorUrl 	= trim($element ? $element->data() : '');

                    $element 			= &$root->getElementByPath( 'version');
                    $plugin->version 	= trim($element ? $element->data() : '');

                }else {
                    echo "Missing file '$xmlfile'";
                }

            }

            $observedEvents = $plugin->getObservedEvents();
            foreach($observedEvents as $observedEvent)
            {
                js_PluginManager::registerEventObserver($plugin, $observedEvent);
            }
            //$list[$plugin_name] = $plugin;
            
		}
		return $list;
	}

    function getPlugin($pluginId)
    {
        global $js_PluginsLists;

        if($js_PluginsLists === null || !isset($js_PluginsLists[$pluginId]))
        {
           $js_PluginsLists = js_PluginManager::loadAvailablePlugins( ) ;
        }
        if(!isset($js_PluginsLists[$pluginId]))
        {
           js_echoJSDebugInfo('Cannot find plugin', $js_PluginsLists); 
           return null;
        }

        $pluginId = strtolower(trim($pluginId));
        return $js_PluginsLists[$pluginId];
    }

    /**
     * @param  $plugin the plugin instance
     * @param  $task in lower case and make sure that whitespaces are all removed
     * @return void
     */
    function registerEventObserver(&$plugin, $event)
    {
       global $js_event_observers;

       if($js_event_observers == null)
       {
          $js_event_observers = array();
       }

       $event = strtolower(trim($event));
       $plugins = null;
       if(!isset($js_event_observers[$event]))
       {
          $plugins = array();
          $js_event_observers[$event] =& $plugins;
       }
       else
       {
           $plugins =& $js_event_observers[$event];
       }

       $plugins[$plugin->id] = $plugin;
       js_echoJSDebugInfo("Register plugin ".$plugin->id." for event $event", "");
    }

    function fireEventUsingSource($event, &$source )
    {
       $args = array(&$source);
       js_PluginManager::fireEventWithArgs( $event, $args); 
    }

    function fireEvent($event )
    {
       $args = array();
       js_PluginManager::fireEventWithArgs( $event, $args);
    }


    function fireEventWithArgs( $event, $args = array())
    {
       $event = strtolower(trim($event));
       js_echoJSDebugInfo("Fire Event => $event");

       global $js_PluginsLists;

       if($js_PluginsLists === null)
       {
          $js_PluginsLists = js_PluginManager::loadAvailablePlugins( ) ;
       }

       global $js_event_observers;

       if(!isset($js_event_observers[$event]))
       {
           //js_echoJSDebugInfo("No Plugin available for Event $event");
           return;
       }
       $plugins =& $js_event_observers[$event];
       foreach($plugins as $key=>$plugin)
       {
          js_echoJSDebugInfo("Event $key", $plugin);
          $plugin->trigger( $event, $args);
       }
    }

    /**
	 * Parse an .ini string, based on phpDocumentor phpDocumentor_parse_ini_file function
	 *
	 * @access public
	 * @param mixed The INI string or array of lines
	 * @param boolean add an associative index for each section [in brackets]
	 * @return object Data Object
	 */
	function &stringToObject( $data, $process_sections = false )
	{
		static $inistocache;

		if (!isset( $inistocache )) {
			$inistocache = array();
		}

		if (is_string($data))
		{
			$lines = explode("\n", $data);
			$hash = md5($data);
		}
		else
		{
			if (is_array($data)) {
				$lines = $data;
			} else {
				$lines = array ();
			}
			$hash = md5(implode("\n",$lines));
		}

		if(array_key_exists($hash, $inistocache)) {
			return $inistocache[$hash];
		}

		$obj = new stdClass();

		$sec_name = '';
		$unparsed = 0;
		if (!$lines) {
			return $obj;
		}

		foreach ($lines as $line)
		{
			// ignore comments
			if ($line && $line{0} == ';') {
				continue;
			}

			$line = trim($line);

			if ($line == '') {
				continue;
			}

			$lineLen = strlen($line);
			if ($line && $line{0} == '[' && $line{$lineLen-1} == ']')
			{
				$sec_name = substr($line, 1, $lineLen - 2);
				if ($process_sections) {
					$obj-> $sec_name = new stdClass();
				}
			}
			else
			{
				if ($pos = strpos($line, '='))
				{
					$property = trim(substr($line, 0, $pos));

					// property is assumed to be ascii
					if ($property && $property{0} == '"')
					{
						$propLen = strlen( $property );
						if ($property{$propLen-1} == '"') {
							$property = stripcslashes(substr($property, 1, $propLen - 2));
						}
					}
					// AJE: 2006-11-06 Fixes problem where you want leading spaces
					// for some parameters, eg, class suffix
					// $value = trim(substr($line, $pos +1));
					$value = substr($line, $pos +1);

					if (strpos($value, '|') !== false && preg_match('#(?<!\\\)\|#', $value))
					{
						$newlines = explode('\n', $value);
						$values = array();
						foreach($newlines as $newlinekey=>$newline) {

							// Explode the value if it is serialized as an arry of value1|value2|value3
							$parts	= preg_split('/(?<!\\\)\|/', $newline);
							$array	= (strcmp($parts[0], $newline) === 0) ? false : true;
							$parts	= str_replace('\|', '|', $parts);

							foreach ($parts as $key => $value)
							{
								if ($value == 'false') {
									$value = false;
								}
								else if ($value == 'true') {
									$value = true;
								}
								else if ($value && $value{0} == '"')
								{
									$valueLen = strlen( $value );
									if ($value{$valueLen-1} == '"') {
										$value = stripcslashes(substr($value, 1, $valueLen - 2));
									}
								}
								if(!isset($values[$newlinekey])) $values[$newlinekey] = array();
								$values[$newlinekey][] = str_replace('\n', "\n", $value);
							}

							if (!$array) {
								$values[$newlinekey] = $values[$newlinekey][0];
							}
						}

						if ($process_sections)
						{
							if ($sec_name != '') {
								$obj->$sec_name->$property = $values[$newlinekey];
							} else {
								$obj->$property = $values[$newlinekey];
							}
						}
						else
						{
							$obj->$property = $values[$newlinekey];
						}
					}
					else
					{
						//unescape the \|
						$value = str_replace('\|', '|', $value);

						if ($value == 'false') {
							$value = false;
						}
						else if ($value == 'true') {
							$value = true;
						}
						else if ($value && $value{0} == '"')
						{
							$valueLen = strlen( $value );
							if ($value{$valueLen-1} == '"') {
								$value = stripcslashes(substr($value, 1, $valueLen - 2));
							}
						}

						if ($process_sections)
						{
							$value = str_replace('\n', "\n", $value);
							if ($sec_name != '') {
								$obj->$sec_name->$property = $value;
							} else {
								$obj->$property = $value;
							}
						}
						else
						{
							$obj->$property = str_replace('\n', "\n", $value);
						}
					}
				}
				else
				{
					if ($line && $line{0} == ';') {
						continue;
					}
					if ($process_sections)
					{
						$property = '__invalid'.$unparsed ++.'__';
						if ($process_sections)
						{
							if ($sec_name != '') {
								$obj->$sec_name->$property = trim($line);
							} else {
								$obj->$property = trim($line);
							}
						}
						else
						{
							$obj->$property = trim($line);
						}
					}
				}
			}
		}

		$inistocache[$hash] = clone($obj);
		return $obj;
	}
}

/**
 * Sets a redirect to the URL, which was called from the visitor
 * 
 * @param  $max
 * @param int $min
 * @return boolean true, if current process should be stopped
 */
function js_checkForTimeOut($max = -1, $min = 15)
{
    if(!js_JSDatabaseAccess::executionTimeAvailable($max, $min))
    {
        global $js_controller;
        $uri = JURI::getInstance();
        $uriStr = $uri->toString();
        $text = JText::_("PHP Timeout prevented. Request was redirected", 'message');
        $js_controller->setRedirect($uriStr, $text);
        return true;
    }
    return false;
}
