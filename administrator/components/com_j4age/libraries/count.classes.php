<?php

              
/**
 * @package j4age
 * @copyright Copyright (C) 2009-@THISYEAR@ j4age Team. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * Thank you to the project j4age and it's team, on which roots this project is build on.
 */
             
          

if( !defined( '_JS_STAND_ALONE' ) && !defined( '_JEXEC' ) ) {
die( 'JS: No Direct Access to '.__FILE__ );
}


require_once( dirname(__FILE__) .DS. '..'.DS. 'api' .DS. 'ip.info.php' );
require_once( dirname(__FILE__) .DS. 'base.classes.php' );
require_once( dirname(__FILE__) .DS. '..'.DS. 'database' .DS. 'db.constants.php' );
require_once( dirname(__FILE__) .DS. '..'.DS. 'database' .DS. 'access.php' );


class js_JSCountVisitor
{
	/** 'JS' configuration object. Holds system and user settings */
	var $JSConf			= null; 

	/** database placeholder */
	var $db;

	/** below members hold current date and time in Joomla Local timezone */
	var $now_timestamp = null;
	var $now_date_str = null;
	var $now_time_str = null;

    var $visitor = null;
    var $client = null;
    var $location = null;

	function __construct() {
		$JSDatabaseAccess =& js_JSDatabaseAccess::getInstance();
		$this->db = $JSDatabaseAccess->db;

		{//set current JS time (time for anonymous front page users For details see http://www.joomlastats.org:8080/display/JS/FAQ+Wrong+time+in+JoomlaStats and http://www.joomlastats.org:8080/display/JS/FAQ+Time+and+Time+Zones+in+JoomlaStats )
			/**
             * GMT Timestamp is stored, not the local timestamp
             */
            $this->now_timestamp = js_getJSNowTimeStamp();
			$this->now_date_str = js_gmdate('Y-m-d', $this->now_timestamp);
			$this->now_time_str = js_gmdate('H:i:s', $this->now_timestamp);

			js_echoJSDebugInfo('Visit time in Joomla Local timezone: '.$this->now_date_str.' '.$this->now_time_str, '');
		}
		
		$this->JSConf = js_JSConf::getInstance();;
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
	function js_JSCountVisitor()
	{
		$args = func_get_args();
		call_user_func_array(array(&$this, '__construct'), $args);
	}	
	

		
	/**
	 *  Count visitor that visit 'Joomla CMS': 
	 *    - recognize visitor
	 *    - update JS DB about visitor
	 *    - update JS DB about page that visior request
	 *
	 *  @return mixed $visit_id       - false on failure; integer visit_id on success; 0 when page is excluded from counting; greater than 0 is valid visit_id
	 */
	function countVisitor( ) {

		// get user agent of visitor
        $VisitorObj = null;
        $location = null;
        $client = null;
        $requested_url = null;

        //We split the call as the plugin
        $visit_id = $this->phase1($client,$VisitorObj, $location, $requested_url);
        if($visit_id === false) return false;
        $this->phase2($client,$VisitorObj, $location, $requested_url, $visit_id);

	}

    public function phase1(&$client, &$VisitorObj, &$location, &$requested_url)
    {
		js_profilerMarker('Perform Visitor counting process (function: countVisitor())');

		$requested_url = $this->getRequestedUri();

		if ($requested_url != '') {
			$ignore = strpos($requested_url, 'jstatsIgnore');
			// Do not make counting on marked pages
			if ($ignore > 0) {
				js_echoJSDebugInfo('This page is excluded from counting', '');
				return false;
			}
		}

		// get user agent of visitor
		$VisitorUserAgent = $this->getVisitorUserAgent();
        $VisitorObj = null;
		// get IP address of visitor
        $VisitorIpStr = null;
		$this->getVisitorIp($VisitorIpStr);
        $VisitorIp = js_ip2long($VisitorIpStr); //@since 3.1.0 we are using long and not a string
        $location = null;
        $client = null;
        js_profilerMarker("loadKnownRecords start");
        $bResult = $this->loadKnownRecords( $VisitorIp, $VisitorUserAgent, $location, $client, $VisitorObj  );
        js_profilerMarker("loadKnownRecords end");

        //js_echoJSDebugInfo("Located IP-ID: $ip_id Client-ID: $client_id Visitor-ID: $visitor_id", '');

		if ($bResult == false)
        {
            return false; //@todo Andreas Halbig: I think this should not be there and might just cause, that entries are not captured and knowone knows why
            js_echoJSDebugInfo("loadKnownRecords failed. Skip counting process", '');
        }

        js_profilerMarker("client & ip handling start");

        /**
         * If nothing found in the DB, the reference will be null
         *
         * We have to collect all information for the IP address to store in the DB
         */
        if ( $location == null )
        {
            $result = IPInfoHelper::performLocationCheck($location, $VisitorIp, true );

            js_PluginManager::fireEventWithArgs('beforeCounting', array(&$client, &$VisitorObj, &$location ));
            $this->insertLocation($location);
            //todo $this->linkIPToClient( $VisitorObj, $client );

            if ($location == null) {
                js_echoJSDebugInfo('Something is wrong with Visitor recognition or storing data about IP', '');
                return false;
            }

        }
        else
        {
            js_echoJSDebugInfo('IP already stored '.$location->ip, '');
            js_PluginManager::fireEventWithArgs('beforeCounting', array(&$client, &$VisitorObj, &$location ));
        }

        if ($location->ip_exclude == true) {
            js_echoJSDebugInfo('Visitors IP is excluded from counting');
            return false;
        }

        if(!empty($client) && $client->cookie_support)
        {
            js_echoJSDebugInfo('Cookies supported - Available '.count($_COOKIE));
        }
        else
        {
            js_echoJSDebugInfo(count($_COOKIE).'Cookies availble');
        }

        if(!empty($client))
        {
            js_echoJSDebugInfo('Found client '.$client->client_id.' Visitor-ID ='.$client->visitor_id);
        }

        /**
         * We have to create a Visitor entry, when
         *
         * 1. We have a client, where no visitor is assigned and cookies are definitely working
         * 2. No client was found at all, but cookies are enabled
         *
         * @todo use the 'u' flag to check within the products, if cookies are enabled
         */
        if ( (!empty($client) && $client->cookie_support == true && empty($client->visitor_id)) || ($client == null && count($_COOKIE) > 0 ) )
        {
            if ($VisitorObj != null && !empty($VisitorObj->visitor_id) )
            {
                //We already found a existing visitor entry
                 js_echoJSDebugInfo('Visitor already created - ID = '.$VisitorObj->visitor_id);
            }
            else
            {
                $this->insertVisitor( $VisitorObj );

                if ($VisitorObj == null|| empty($VisitorObj->visitor_id) ) {
                    js_echoJSDebugInfo('Something is wrong. No Visitor entry return', '');
                    //return false;
                }

                //additional members (I am not sure if we need them)
                //$VisitorObj->RequestedPage = $requested_url;
                //$VisitorObj->joomla_userid = $this->getJoomlaCmsUserId();
            }
        }

        /**
         * We have to collect all client information for the Useragent to be able to store them in the DB
         */
		if ( $client == null || empty($client->client_id) )
        {
			js_echoJSDebugInfo('New Client', '');

            // get client data from request ------------------------------------------------
            $bResult = $this->retrieveClientDetails(  $VisitorUserAgent, $client);

            if ($bResult == false)
            {
                js_echoJSDebugInfo('Something went wrong whilst retrieving Client Details. Skip counting process', '');
                return false;
            }

            /**
             * If the IP is flag as a specific type (!= Unknown & != Browser), we copy the type over to the client
             *
             * This allows as example to identify bots/spiders just by specific IPs independent from the useragent string
             */
            if($location->ip_type > 1)
            {
               $client->client_type = $location->ip_type;
            }

            /**
             * We link the visitor to the client
             */
            if($VisitorObj != null && !empty($VisitorObj->visitor_id))
            {
                $client->visitor_id = $VisitorObj->visitor_id;
            }

			// insert new unique client ------------------------------------------------
			$this->insertClient( $client );

            if (empty($client->client_id)) {
                js_echoJSDebugInfo('Something is wrong with Client recognition or storing Client data', '');
                return false;
            }

		}
        else
        {
            js_echoJSDebugInfo('Client already known', '');
            $this->linkVisitorToClient( $VisitorObj, $client );
        }
        js_profilerMarker("client & ip handling end");

        /**
         * Link Client to IP, if not already done
         *
         * We always perform a insert using the 'ignore' option as we don't know if this client
         * is already assigned to a specific IP.
         * Keep in mind that we found a client, but not for sure with a link to a IP
         * @depricated The IP is now linked using the jstats_visits
         */
        /*if ( $location != null && $client != null )
        {
            js_echoJSDebugInfo('Link Client to IP', '');

            // get client data from request ------------------------------------------------
            $bResult = $this->linkClientToIP( $client, $location);

            if ($bResult == false)
            {
                js_echoJSDebugInfo('Something went wrong whilst linking Client to IP. Skip counting process', '');
                return false;
            }
        }  */

		if ($client->client_exclude == true) {
			js_echoJSDebugInfo("Client is excluded from counting", '');
			return false;
		}

/*        if ($client->visitor_exclude == true) {
            js_echoJSDebugInfo("Client is excluded from counting", '');
            return false;
        }   */

		$visit_id = $this->registerVisit( $client->client_id, $location->ip, $client  );
        js_echoJSDebugInfo("Visit $visit_id registered", '');

		if (empty($visit_id)) {
			js_echoJSDebugInfo('Something is wrong calling registerVisit', '');
			return false;
		}
        return $visit_id;
    }

    public function phase2(&$client, &$VisitorObj, &$location, $requested_url, &$visit_id )
    {
        $page_id = null;
        js_echoJSDebugInfo('URL called '.$_SERVER['HTTP_HOST']. '-'.$_SERVER['SERVER_PROTOCOL']. '-'.$_SERVER['PATH_INFO']);

		$impression_id = $this->registerPageImpression( $visit_id, $requested_url, $page_id, null, $_SERVER['SERVER_PROTOCOL'] );
        js_echoJSDebugInfo("Page impression $impression_id registered", '');

		$referred_id  = null; // is 0, false or null, if not set
		$keyword_id = null; // is 0, false or null, if not set
		$referrer_or_key_words_status = $this->registerReferrerOrKeyWords( $visit_id, $referred_id, $keyword_id );
        js_echoJSDebugInfo("Keyword & Referrer registered = ".($referrer_or_key_words_status? 'TRUE': 'False'), '');

		//if ($referrer_or_key_words_status === false)
		//	return false;  //even if something goes wrong with referrer or keywords we do not exit with error status - user was succesfully counted

        js_PluginManager::fireEventWithArgs('afterCounting', array(&$client, &$VisitorObj, &$location, &$visit_id, &$visit_id, &$impression_id, &$page_id, &$referred_id, &$keyword_id ));

        js_profilerMarker('Counting Visitor finished');

		return $impression_id;
    }

	/**
	 * Get user agent from Visitor (user that refresh page)
	 *   eg.: "mozilla/5.0 (windows; u; windows nt 5.1; en-gb; rv:1.8.1.15) gecko/20080623 firefox/2.0.0.15"
	 *
	 * @return string - '' (empty) string for failure
	 */
	function getVisitorUserAgent() {

		if( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			if( $_SERVER['HTTP_USER_AGENT'] != NULL ) {
				return trim( strtolower( $_SERVER['HTTP_USER_AGENT'] ) );
			}
		}
		
		return '';
	}

	/** If User is logged into 'Joomla CMS', Joomla CMS UserId is returned. //If user is not logged into 'Joomla CMS', 0 is returned */
	function getJoomlaCmsUserId()
	{
		if ( defined( '_JEXEC' ) ) {//outside joomla we can not check if user is logged
			$user =& JFactory::getUser();
			return (int)$user->id;
		}

		return 0; //JS stand alone version (defined('_JS_STAND_ALONE'))
	}

	function getRequestedUri()
	{
		$request_uri = '';

		if ((isset($_SERVER['REQUEST_URI'])) && ($_SERVER['REQUEST_URI'] != NULL))
        {
			$request_uri = $_SERVER['REQUEST_URI'];
		}
        else if ((isset($_SERVER['PHP_SELF'])) && ($_SERVER['PHP_SELF'] != NULL))
        {
			$request_uri = $_SERVER['PHP_SELF'];
			if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != NULL))
            {
                $request_uri .= '?'.$_SERVER['QUERY_STRING'];
            }
		}
        else if ((isset($_SERVER['SCRIPT_NAME'])) && ($_SERVER['SCRIPT_NAME'] != NULL))
        {
			$request_uri = $_SERVER['SCRIPT_NAME'];
			if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != NULL))
            {
                $request_uri .= '?'.$_SERVER['QUERY_STRING'];
            }
		}
		if (($request_uri == "/") || ($request_uri == "\\"))
        {
            $request_uri .= "index.php";
        }

		if ((strtolower(substr($request_uri, -4)) == '.ico') ||
		    (strtolower(substr($request_uri, -4)) == '.png') ||
		    (strtolower(substr($request_uri, -4)) == '.gif') ||
		    (strtolower(substr($request_uri, -4)) == '.jpg'))
        {
            return '';
        }

		if ($request_uri == '')
        {
            return '';
        }


		// Search Engine Friendly url
		/*if (defined( '_JEXEC' ))
        {
			$app =& JFactory::getApplication();
			if ( $app->getCfg('sef') )
            { //	if (($app->getCfg('sef')) && ($app->getCfg('sef_rewrite')) && !($app->getCfg('sef_suffix'))) {
				$request_uri = $this->sefRelToAbs('index.php?' . $_SERVER['QUERY_STRING']);
			}
		}
        echo $request_uri;
        */
		$request_uri = str_replace('http://', ':#:', $request_uri);
        $request_uri = str_replace('https://', ':#s:', $request_uri);
		$request_uri = str_replace('//', '/', $request_uri);
		$request_uri = str_replace(':#:', 'http://', $request_uri);
        $request_uri = str_replace(':#s:', 'https://', $request_uri);

        /*
        How-to for reading an URL within Joomla
        $application = &JApplication::getInstance('site');

        //SEF URL returned will be as example - http://<mydomain>/jsnew15/joomla-overview.html
        $uri = JURI::getInstance();

        $router = $application->getRouter();
        $parsedURI = $router->parse( $uri );

        //Content returned will be the parameters, we were looking for
        format=html
        Itemid=27
        option=com_content
        view=article
        id=19
        */
        
		return $request_uri;
	}

	/**
	 * Legacy function to convert an internal Joomla URL to a humanly readible URL.
	 *
	 * @deprecated	As of Joomla CMS version 1.5 (this is original Joomla CMS function from v1.5.11)
	 */
	/*function sefRelToAbs($value)
	{
		// Replace all &amp; with & as the router doesn't understand &amp;
		$url = str_replace('&amp;', '&', $value);
		if(substr(strtolower($url),0,9) != "index.php") return $url;
		$uri    = JURI::getInstance();
		$prefix = $uri->toString(array('scheme', 'host', 'port'));
		return $prefix.JRoute::_($url);
	}*/


	/**
	 *
	 *  @param $VisitorIp - valid only when true is returned
	 *  @return true on success
	 */
	function getVisitorIp(&$VisitorIp)
	{
        $VisitorIp = IPInfoHelper::getVisitorIp();
		return true;//@todo false never is returned but should be (I think it is possible to configure PHP that IP is unable to possess)
	}

    /**
     * @param  $VisitorIp The IP as long
     * @param  $VisitorUserAgent the useragent string
     * @param  $location <null> if not IP address match found
     * @param  $client <null> if not client found in DB, a object if match found, but client_id is set to null, if useragent string not matched.
     * @param  $visitor <null> if found client had empty visitor_id or cookie contained link to visitor
     * @return boolean <false> if an error occured in DB
     */
    function loadKnownRecords( $VisitorIp, $VisitorUserAgent, &$location, &$client, &$visitor  ) {

        //If there is a valid cookie, we also should find a corresponding entry in the DB
        $checkforvisitor = false;
        $checkforclientId = false;

        $cookieTrcCode = null;
        $cookieClientID = null;
        {
           /**
            * We like to trace individual visitors even if there are multiple clients behind the same IP/Browser combination and also if the IP is going to change
            * Cookies are good to identify, if we already had such a us visitor on our webpage, unfortunately this won't work so well if cookies are switched off or if the a bot does not
            * support cookies at all, so we basically want a mixure of the current logic as fallback and the cookie-method, if cookies are supported. Unfortunately again, we can't
            * figure out if cookies are supported straightaway without performing a redirect as "test".
            *
            * What we do now is as follows
            * 1. to mark visitors with an cookie-value "$$", if cookie is not set
            * 2. We create a new ip-address (=visitor) entry in parallel to the "$$" value without assignment to an specific "unique user"
            * 3. If the incoming request contains the a cookie value "$$", we are going to create a new "unique visitor". The previous create"ip-address" entry will be located and assigned to this "unique visitor"
            * 4. We set the cookie-value now the the ID of the unique visitor
            * 5. If a visitor comes in using a ID of an unique visitor as cookie-value, we are going to locate the corresponding "ip-address" entry and assign the statistic to this account
            * 6. If another unknown visitor comes in from the same IP, we assign again "$$" and create a new unsigend ip-addresss entry.
            *
            * To determine later the amount of total unique visitors, we only have to count the amount of all entries on the "unique visitor" table plus all unsigend entries of "ip-addresses"
            *
            * For the moment we just add the cookie, which we directly evaluate for release 3.1.0
            */
            $cookieTrcCode = isset($_COOKIE['trcusr'])? $this->db->getEscaped(JRequest::getVar( 'trcusr', null, 'cookie' )) : null;
            $cookieClientID = isset($_COOKIE['cltid'])? $this->db->getEscaped(JRequest::getVar( 'cltid', null, 'cookie' )) : null;
            js_echoJSDebugInfo('Trace Code from cookie: '.$cookieTrcCode, '');
            js_echoJSDebugInfo('Client-ID from cookie: '.$cookieClientID, '');
            if( empty($cookieTrcCode) )
            {
                //Set a dummy cookie to flag this visitor/client otherwise we are not sure if he supports cookies
                setcookie("trcusr", "$$", 2147483647, '/'); //todo make cookies available for subdomains
            }
            else 
            {
                //The Cookie value could be just '$$'
                if(is_Numeric($cookieTrcCode) && $cookieTrcCode > 0)
                {
                    $checkforvisitor = true;
                    $visitor = new js_Visitor();
                    $visitor->visitor_id = $cookieTrcCode;
                }
            }
            if(!empty($cookieClientID))
            {
                if(is_Numeric($cookieClientID) && $cookieClientID > 0)
                {
                    $checkforclientId = true;
                }
            }
        }

        //First get the IP address (a JOIN does not work in 100% of the cases)
        //Keep in mind the fact, that there might be a known user (browser), which has moved between different locations (IP)
        $query = 'SELECT * FROM #__jstats_ipaddresses AS a WHERE a.ip = \'' . $this->db->getEscaped($VisitorIp) . '\'';
        $this->db->setQuery( $query );
        $ipEntry = $this->db->loadObject();

        if ($this->db->getErrorNum() > 0)
        {
            js_echoJSDebugInfo("".$this->db->getErrorMsg(), '');
            return false;
        }
        if($ipEntry)
        {
           //There is a entry
           $location = new js_Location();
           $location->init($ipEntry);
        }
        else
        {
            /**
             * We have not found any ip address entry => a new ip address entry has to be created
             */
            $location = null;
            js_echoJSDebugInfo("No former entry found for IP $VisitorIp", '');
        }

		$query = 'SELECT c.* FROM #__jstats_clients AS c';

        /**
         * First we check for the client-id, then for visitor-id
         * and as fallback for a corresponding match for ip & useragent
         */
        if($checkforclientId)
        {
            //We know that the user was already on your page
           $query .= ' WHERE c.client_id = ' . $this->db->getEscaped($cookieClientID) . ';';
        }
        else if($checkforvisitor)
        {
            //We know that the user was already on your page
           $query .= ' WHERE c.visitor_id = ' . $this->db->getEscaped($cookieTrcCode) . ';';
        }
        else
        {
            //We don't know if the user was there, but we try to find a matching ip/useragent record
            $query .= ' LEFT OUTER JOIN #__jstats_visits AS v ON c.client_id = v.client_id';
            $query .= ' WHERE v.ip = \'' . $this->db->getEscaped($VisitorIp) . '\'';
            $query .= ' AND (c.visitor_id = 0 OR c.visitor_id IS NULL);';

            //. ' AND useragent = \'' . $VisitorUserAgent . '\'' for performance we do this in PHP (MySQL very bad deal with something like this. Additional column user_agent is not indexed (and it should not be indexed)). In main cases there shoud be one entry so PHP better
        }

		$this->db->setQuery( $query );
		$rows = $this->db->loadObjectList();

        if ($this->db->getErrorNum() > 0)
        {
            js_echoJSDebugInfo("".$this->db->getErrorMsg(), '');
            /**
             * Something is wrong => anyway, a new client entry has to be created
             */
            $client = null;
            return false;
        }

		if (!$rows)
        {
            /**
             * No match found, so we definetely have to create a new client
             */
            js_echoJSDebugInfo("No known client found for IP $VisitorIp", '');
			$client = null;
			return true;
		}

        if($checkforclientId)
        {
            /**
             * we know this was once our client, but this does not mean that the useragent string is still the same.
             * This can can happen by an version upgrade or the client has changed his IP, before we assigned the visitor
             */

            $row = current($rows);
            if(!empty($row->visitor_id))
            {
                $visitor = new js_Visitor();
                $visitor->visitor_id = $row->visitor_id;
            }

            /**
             * @todo We should be careful here if the useragent string is longer as the maximum possible length we store in the DB
             */
            if( strcmp($row->useragent, $VisitorUserAgent) == 0)
            {
                /**
                 * We found for what we were looking for
                 */
                $client = new js_Client();
                $client->init($row);
                /**
                 * This is important to know if the visitor_id is empty as we are going to create the
                 * visitor entry only, if we can identify him again
                 */
                $client->cookie_support = $client->cookie_support? true : count($_COOKIE) > 0;
                //$client_id = $row->client_id;
                //$client_exclude = $row->client_exclude;
                //$visitor_id = empty($row->visitor_id) ? $visitor_id : $row->visitor_id ; //This should be always empty and the $visitor_id should be still $$
                return true;
            }else
            {
                /**
                 * We partially found for what we were looking for, but
                 * we have to set the client_id to null so that we know later that
                 * this entry needs to be created using a different useragent string
                 */
                $client = new js_Client();
                $client->init($row);

                $client->client_id = null;
                $client->useragent = $VisitorUserAgent;
                /**
                 * This is important to know if the visitor_id is empty as we are going to create the
                 * visitor entry only, if we can identify him again
                 */
                $client->cookie_support = $client->cookie_support? true : count($_COOKIE) > 0;
                //todo if the useragent has changed, we would create now a new entry.
                // However, we know that cookies are working and the visitor-entry can be create, but we also should assign the visitor-id to the "old" client entry
            }
        }
        else
        {
            foreach( $rows as $row)
            {
                if( strcmp($row->useragent, $VisitorUserAgent) == 0)
                {
                    //$isKnownVisitor = true;//yes we found
                    if( $checkforvisitor && !empty($row->visitor_id))
                    {
                        /**
                         * This is a match by Visitor-ID / Useragent 
                         */
                        $client = new js_Client();
                        $client->init($row);
                        $client->cookie_support = $client->cookie_support? true : count($_COOKIE) > 0;

                        //There is a entry
                        $location = new js_Location();
                        $location->init($ipEntry);

                        return true;
                    }
                    else 
                    {
                        /**
                         * This is a match in DB by IP/Useragent
                         * Our only option to handle clients without enabled cookies
                         * IN this case the visitor_id has to be empty
                         */
                        $client = new js_Client();
                        $client->init($row);
                        /**
                         * This is important to know if the visitor_id is empty as we are going to create the
                         * visitor entry only, if we can identify him again
                         */
                        $client->cookie_support = $client->cookie_support? true : count($_COOKIE) > 0;
                        //In this case, we have to create the visitor entry

                        return true;
                    }
                }
            }
		}
		return true;
	}

	/**
	 * This function make visitor recognition. Basing on $IpAddress and $UserAgent it return information about
	 *   operationg system, browser, user type etc.
	 *
	 * recognize because data are taken directly from function arguments (not from user browser, PHP settings, cookies, javascript etc)
	 *
	 * @param out $Visitor - object of class js_Visitor
	 *
	 * @return bool - true on success
     * @depricated
	 */
	function retrieveClientDetails( $UserAgent, &$client ) {

		js_echoJSDebugInfo("Retrieve Client Details", '');

        // create client object ------------------------------------------------

        if($client == null)
        {
            $client = new js_Client();
        }

		// get browser --------------------------------------------------------------------------
		$BrowserVersion = '';
        $products = $this->parseUserAgent($UserAgent);
		$BrowserObj = $this->getBrowserFromUserAgent( $UserAgent, $BrowserVersion, $products );

		if ($BrowserObj == null)
        {
            /**
             * This should not happen
             */
            return false;
        }

        $BrowserObj->products = $products;

		//Some bots to identify themself as real browser, so we can't restrict it to browser_id = 0 ... if ($BrowserObj->browser_id == 0)
        { // look for bot if this is not regular visitor (if still unknown)
			js_JSCountVisitor::checkUnknownBotFromUserAgent($UserAgent, $BrowserObj);
		}

		// get OS version -----------------------------------------------------------------------
		$OS = $this->getOsFromUserAgent($UserAgent, $products);

		if ($OS == null) {
			//create unknown system
			$OS = new js_OS();
		}

        $client->OS = $OS;
        $client->Browser = $BrowserObj;
        $client->browser_version = $BrowserVersion;
        $client->useragent = $UserAgent;

        /**
         * The Client type is initial taken from the browser type
         *
         * Attention: the Location could later overwrote this value to be classified as Bot
         */
        $client->client_type = $BrowserObj->browser_type;

		//js_echoJSDebugInfo('Visitor', $Visitor);

		return true;
	}

    function insertNewUniqueVisitor(&$visitor_id, $client_id, $updateClient = false)
    {
        $visitor_token = $visitor_id;

        $createVisitor = false;
        /**
         * We only create a visitor entry, once we are sure that he has enabled cookies. This means on the second visit
         *
         * We could parse the useragent string to check, if there is the flag "u" placed. This indicates the
         * security level (incl. cookies enabled/disabled )
         *
         */
        if(!empty($visitor_id))
        {
            if(is_Numeric($visitor_id))
            {
                if($visitor_id > 0)
                {
                    $createVisitor = false;
                }
                else
                {
                    $createVisitor = true;
                }
            }
            else
            {
                if(strcmp($visitor_id, '$$') == 0)
                {
                    $createVisitor = true;
                }
            }
        }

        if( $createVisitor )
        {
            $query = 'INSERT IGNORE INTO #__jstats_visitors (note, visitor_exclude) VALUES ( NULL, 0 );';

            $this->db->setQuery( $query );
            if (!$this->db->query())
            {
               js_echoJSDebugInfo("".$this->db->getErrorMsg(), '');
               return false;
            }
            $visitor_id = $this->db->insertid();
            js_echoJSDebugInfo("Token: $visitor_token; New Visitor added using ID ".$visitor_id, '');

            setcookie("trcusr", "$visitor_id", 2147483647, '/'); //todo make cookie available for subdomains

            if($updateClient)
            {
                $query = 'UPDATE #__jstats_clients SET visitor_id = '.$this->db->getEscaped($visitor_id).' WHERE client_id = '.$this->db->getEscaped($client_id);
                $this->db->setQuery( $query );
                if (!$this->db->query())
                {
                   js_echoJSDebugInfo("".$this->db->getErrorMsg(), '');
                   return false;
                }
                js_echoJSDebugInfo("Client ".$client_id." assigned to unique Visitor ".$visitor_id, '');
                return true;
            }
        }
        return true;
    }

    function linkVisitorToClient(&$visitor, &$client)
    {
        if( !empty($visitor) && $visitor->visitor_id > 0 && !empty($client) && $client->client_id > 0 && empty($client->visitor_id) )
        {
            $query = 'UPDATE #__jstats_clients SET visitor_id = '.$this->db->getEscaped($visitor->visitor_id).' WHERE client_id = '.$this->db->getEscaped($client->client_id);
            $this->db->setQuery( $query );
            if (!$this->db->query())
            {
               js_echoJSDebugInfo("".$this->db->getErrorMsg(), '');
               return false;
            }
            $client->visitor_id = $visitor->visitor_id;
            js_echoJSDebugInfo("Client ".$client->client_id." assigned to visitor ".$visitor->visitor_id, '');

            /**
             * We have store the visitor ID to be able to find him again
             */
            setcookie("trcusr", ''.$visitor->visitor_id, 2147483647, '/'); //todo make cookie available for subdomains
            return true;
        }
        return false;
    }

    function insertVisitor(&$visitor)
    {
        if( empty($visitor) || empty($visitor->visitor_id) )
        {
            $query = 'INSERT IGNORE INTO #__jstats_visitors (note, visitor_exclude) VALUES ( NULL, 0 );';

            $this->db->setQuery( $query );
            if (!$this->db->query())
            {
               js_echoJSDebugInfo("".$this->db->getErrorMsg(), '');
               return false;
            }
            if($visitor == null)
            {
                $visitor = new js_Visitor();
            }
            $visitor->visitor_id = $this->db->insertid();
            js_echoJSDebugInfo("New Visitor added using ID ".$visitor->visitor_id, '');

            /**
             * We have store the visitor ID to be able to find him again
             */
            setcookie("trcusr", ''.$visitor->visitor_id, 2147483647, '/'); //todo make cookie available for subdomains
            return true;
        }
        return false;
    }

    function insertLocation(&$location)
    {
        if( !empty($location) && !($location->ip === null) )
        {
            if($location->nslookup == null)
            {
                $nslookupSQL = " NULL ";
            }
            else
            {
                $nslookupSQL = "'".$this->db->getEscaped($location->nslookup)."'";
            }

            $query = 'INSERT IGNORE INTO #__jstats_ipaddresses'
            . ' (ip, nslookup, ip_type, ip_exclude, code)'
            . ' VALUES (\'' . $location->ip . '\','
                . ' ' . $nslookupSQL . ' ,'
                . ' \'' . $location->ip_type . '\','
                . ' \'' . $location->ip_exclude . '\','
                . (empty($location->code)?'NULL':(' \'' . $location->code . '\''))
                //. ' NULL'
            . ')'
            ;

            $this->db->setQuery( $query );
            if (!$this->db->query())
            {
               js_echoJSDebugInfo("".$this->db->getErrorMsg(), '');
               return false;
            }
            js_echoJSDebugInfo("Add New IP using IP ".$location->ip, '');
        }
        return false;
    }

	/**
	 * Add new Visitor to #__jstats_ipaddresses table
	 *
	 * @param object $Visitor - object of class $js_Visitor
	 * @return bool - return true on success and object $Visitor has set member $Visitor->visitor_id
	 */
	function insertClient( &$client )
    {
        if( empty($client->client_id) || $client->client_id <= 0)
        {
            $query = 'INSERT IGNORE INTO #__jstats_clients'
            . ' ( os_id, visitor_id, browser_id, browser_version, useragent, client_type, client_exclude)'
            . ' VALUES ('
            . ' \'' . $client->OS->os_id . '\','
            . ' ' . ($client->visitor_id > 0? $this->db->getEscaped($client->visitor_id) : "NULL") . ','
            . ' \'' . $client->Browser->browser_id . '\','
            . ' \'' . $this->db->getEscaped($client->browser_version) . '\','
            . ' \'' . $this->db->getEscaped($client->useragent) . '\','
            . ' \'' . $client->client_type . '\','
            . ' \'' . $client->client_exclude . '\''
            . ')'
            ;

            $this->db->setQuery( $query );
            if (!$this->db->query())
            {
               js_echoJSDebugInfo("".$this->db->getErrorMsg(), '');
               return false;
            }

            $client->client_id = $this->db->insertid();
            js_echoJSDebugInfo("Added New Client using ID ".$client->client_id, '');
            /**
             * Set Cookie to help us later to find this user again
             */
            setcookie("cltid", "".$client->client_id, 2147483647, '/'); //todo make cookies available for subdomains

            return true;
        }

		//@todo $Visitor->visitor_id = $this->db->insertid();

		return false;
	}
    /*
    Can be removed as table dropped

	function linkClientToIP( &$client, &$location )
    {
        if( !empty($client->client_id) && $client->client_id > 0 && !empty($location->ip)  && $location->ip > 0)
        {

            $query = 'INSERT IGNORE INTO #__jstats_client_to_ip'
            . ' (ip, client_id)'
            . ' VALUES ('
                . $location->ip. ','
                . $client->client_id
            . ')'
            ;

            $this->db->setQuery( $query );
            if (!$this->db->query())
            {
               js_echoJSDebugInfo("".$this->db->getErrorMsg(), '');
               return false;
            }
            js_echoJSDebugInfo("Linked Client ".$client->client_id." to IP ".$location->ip.' Effected Rows '.$this->db->getAffectedRows(), '');
            return true;
        }
        return false;
    }
    */
    /**
     * @depricated Outsources to ip.info.php
     */
/*	function updateVisitor( $Visitor ) {

        return IPInfoHelper::updateVisitor( $Visitor );
	}
*/
	/**
	 * @param string  $UserAgent  eg.: "mozilla/5.0 (windows; u; windows nt 5.1; en-gb; rv:1.8.1.15) gecko/20080623 firefox/2.0.0.15"
	 *
	 * @return object of class js_OS or null when fail
	 */
	function getOsFromUserAgent( $UserAgent ) {

		if (strlen($UserAgent) == 0)//if ($UserAgent == '') - this not always works!
			return null;

		$query = ''
		. ' SELECT'
		. '   LENGTH(o.os_key) AS strlen,'
		. '   o.os_id        AS os_id,'
		. '   o.os_type      AS os_type,'
		. '   o.os_key       AS os_key,'
		. '   o.os_name      AS os_name,'
		. '   o.os_img       AS os_img'
		. ' FROM'
		. '   #__jstats_systems o'
		. ' WHERE'
		. '   o.os_id > 0'
		. ' ORDER BY'
		. '   o.os_ordering'
		;
		$this->db->setQuery( $query );
		$rows = $this->db->loadObjectList();
		if ($this->db->getErrorNum() > 0)
	    {
            js_echoJSDebugInfo("".$this->db->getErrorMsg(), '');
            return null;
        }

        $UserAgent = strtolower($UserAgent);
        
		/**
         * @todo Andreas H. this can be also improved as we did in the browser determination
         *
         */
		foreach( $rows as $row) {
			if( strpos( $UserAgent, strtolower($row->os_key), 0 ) !== false) {
				$OS = new js_OS();//we copy each member manually to be sure about that what is inside. Additional we use getEscaped() method
				$OS->os_id = $row->os_id;
				$OS->os_type = $row->os_type;
				$OS->os_key = $row->os_key;
				$OS->os_name = $this->db->getEscaped( $row->os_name );
				$OS->os_img = $row->os_img;

				#__jstats_ostype (with entries)
				$__jstats_ostype = unserialize(_JS_DB_TABLE__OSTYPE);//whole table
				//fill missing entries in $OS object
				$OS->ostype_name = $__jstats_ostype[$OS->os_type]['ostype_name'];
				$OS->ostype_img = $__jstats_ostype[$OS->os_type]['ostype_img'];

				return $OS;
			}
		}
	
		return null;
	}

	/**
	 * @depricated Outsources to ip.info.php
	 */
	function getTldFromTld( $tld_str ) {
        return IPInfoHelper::getTldFromTld($tld_str);
	}


	/**
	 * @param in  string  $UserAgent      eg.: "mozilla/5.0 (windows; u; windows nt 5.1; en-gb; rv:1.8.1.15) gecko/20080623 firefox/2.0.0.15"
	 * @param out string  $BrowserVersion eg.: "7.0" (Connected with $BrowserName gives "Internet Explorer 7.0") //could be empty
	 *
	 * @return object of class js_Browser if visitor has browser (if visitor is bot/spider null is returned)
	 */
	/**
	 * @param in  string  $UserAgent      eg.: "mozilla/5.0 (windows; u; windows nt 5.1; en-gb; rv:1.8.1.15) gecko/20080623 firefox/2.0.0.15"
	 * @param out string  $BrowserVersion eg.: "7.0" (Connected with $BrowserName gives "Internet Explorer 7.0") //could be empty
	 *
	 * @return object of class js_Browser if visitor has browser (if visitor is bot/spider null is returned)
	 */
	function getBrowserFromUserAgent( $UserAgent, &$BrowserVersion, &$products) {

		if (strlen($UserAgent) == 0)//if ($UserAgent == '') - this not always works!
			return new js_Browser();//return unknown

		$query = ''
		. ' SELECT *'
		. ' FROM'
		. '   #__jstats_browsers br'
		. ' WHERE'
        . '   br.browser_id > 0'
        . ' ORDER BY br.browser_ordering'
		;
		$this->db->setQuery( $query );
		$rows = $this->db->loadObjectList();
		if ($this->db->getErrorNum() > 0)
        {
            js_echoJSDebugInfo("".$this->db->getErrorMsg(), '');
            return null;
        }

        return $this->findBrowserFromUserAgent($this->db, $rows, $BrowserVersion, $UserAgent, $products );
	}

    function parseUserAgent( $useragent )
    {
        $agent = $useragent;
        $agent = trim($agent);
        $len = strlen($agent);

        $blockProduct = true;
        $blockVersion = false;
        $blockComment = false;

        $blockvalue = array();

        //yacybot (amd64 linux 2.6.24-16-server; java 1.6.0_07; europe/en) http://yacy.net/bot.html
        //wolfram research

        $products = array();
        $product = array("name" => null, "version" => null, "comment" => null);
        $name = null;
        $version = null;
        $comment = null;
        for($i = 0 ; $i < $len; $i++)
        {
            $c = $agent[$i];
            if( ($i+1) == $len )
            {
               if($c != '/' && $c != '(' && $c != ')' && $c != ' ')
               {
                   $blockvalue[] = $c;
               }
               if(count($blockvalue) > 0)
               {
                   if($blockProduct)
                   {
                       $name = implode('',$blockvalue);
                       $product['name'] = $name;
                   }
                   else if($blockVersion)
                   {
                       $version = implode('',$blockvalue);
                       $product['version'] = $version;
                   }
                   else if($blockComment)
                   {
                       $comment = trim(implode('',$blockvalue));
                       $product['comment'] = $comment;
                   }
               }
               if(!empty($name))
               {
                   $products[] = $product;
               }
               $name = null;
               $version = null;
               $comment = null;
            }
            else if($c == ' ')
            {
               //echo "<br/>Y Block P:$blockProduct V:$blockVersion C:$blockComment <br/>";
               if($blockComment)
               {
                  $blockvalue[] = $c;
               }
               else
               {
                   if($blockProduct)
                   {
                       /**
                        * If we previously read a comment, w
                        */
                       if(!empty($name))
                       {
                          $products[] = $product;
                          $name = null;
                          $version = null;
                          $comment = null;
                          $product = array("name" => null, "version" => null, "comment" => null);
                       }
                   }
                   if(count($blockvalue) > 0)
                   {
                       if($blockProduct)
                       {
                           $name = implode('',$blockvalue);
                           $product['name'] = $name;
                       }
                       else if($blockVersion)
                       {
                           $version = implode('',$blockvalue);
                           $product['version'] = $version;
                       }
                       else if($blockComment)
                       {
                           $comment = trim(implode('',$blockvalue));
                           $product['comment'] = $comment;
                       }
                   }
                   $blockvalue = array();

                   $blockProduct = true;
                   $blockVersion = false;
                   $blockComment = false;
               }
            }
            else if($c == '/')
            {
                /**
                 * This is only a indictor, if we have parsed the product name first
                 */
                if($blockProduct)
                {
                    $blockProduct = false;
                    $blockVersion = true;
                    $blockComment = false;

                    if(count($blockvalue)>0)
                    {
                        $name = implode('',$blockvalue);
                        $product['name'] = $name;
                        $blockvalue = array();
                    }
                }
                else
                {
                    $blockvalue[] = $c;
                }
            }
            else if($c == '(')
            {
                if(count($blockvalue)>0)
                {
                    if($blockVersion)
                    {
                        $version = implode('',$blockvalue);
                        $product['version'] = $version;
                        $blockvalue = array();
                    }
                    else if($blockProduct)
                    {
                        $name = implode('',$blockvalue);
                        $product['name'] = $name;
                        $blockvalue = array();
                    }
                }
                $blockProduct = false;
                $blockVersion = false;
                $blockComment = true;
               //store product name
               //start comment
            }
            else if($c == ')')
            {
                if($blockComment)
                {
                    $comment = trim(implode('',$blockvalue));
                    $product['comment'] = $comment;
                    $products[] = $product;
                    //echo "Store: ".count($products)." Name: ".$product['name']." Version: ".$product['version']." Comment: ".$product['comment']."<br/>";
                    $name = null;
                    $version = null;
                    $comment = null;
                    $product = array("name" => null, "version" => null, "comment" => null);
                    $blockvalue = array();
                }    
                $blockProduct = true;
                $blockVersion = false;
                $blockComment = false;
                //end comment
               //set comment
               //next product
            }
            else
            {
                /**
                 * If the product/version is read, we were also hoping to find a comment
                 */
                if($blockProduct && !empty($name))
                {
                   $products[] = $product;
                   $name = null;
                   $version = null;
                   $comment = null;
                   $product = array("name" => null, "version" => null, "comment" => null);
                }

               $blockvalue[] = $c;
            }
        }
        //This ereg function is slower and no more supported for PHP 6
        /*
        $products = array();
        $pattern  = "([^/[:space:]]*)" . "(/([^[:space:]]*))?";
        $pattern .= "([[:space:]]*\[[a-zA-Z][a-zA-Z]\])?" . "[[:space:]]*";
        $pattern .= "(\\((([^()]|(\\([^()]*\\)))*)\\))?" . "[[:space:]]*";

        while( strlen($agent) > 0 )
        {
          $a = array();
          if ($l = ereg($pattern, $agent, $a))
          {
            array_push($products, array("name" => $a[1], "version" => $a[3], "comment" => $a[6]));
            $agent = substr($agent, $l);
          }
          else break;//$agent = "";		// abort parsing, no match
        }  */
        return $products;
    }

    function parseUserAgentOld( $useragent )
    {
        $agent = $useragent;
        $products = array();
        $pattern  = "([^/[:space:]]*)" . "(/([^[:space:]]*))?";
        $pattern .= "([[:space:]]*\[[a-zA-Z][a-zA-Z]\])?" . "[[:space:]]*";
        $pattern .= "(\\((([^()]|(\\([^()]*\\)))*)\\))?" . "[[:space:]]*";

        while( strlen($agent) > 0 )
        {
          $a = array();
          if ($l = ereg($pattern, $agent, $a))
          {
            array_push($products, array("name" => $a[1], "version" => $a[3], "comment" => $a[6]));
            $agent = substr($agent, $l);
          }
          else break;//$agent = "";		// abort parsing, no match
        }
        return $products;
    }

	function findBrowserFromUserAgent( &$db, &$browser_keywords, &$BrowserVersion, $useragent, &$products)
    {
		if (count($products) < 1)//if ($UserAgent == '') - this not always works!
        {
            $browser = new js_Browser();
            $browser->browser_id = _JS_DB_BRWSR__ID_UNKNOWN;
            $browser->products = $products;
            return $browser;//return unknown
        }

		foreach( $browser_keywords as $keyword )
        {      
            $matched = false;
            $versionmatched = false;
            $commentmatched = false;
            $productmatch = false;
            $location = $keyword->browser_location;
            /**
             *  Each Bit represents one flag and we extract them by making a bit operation 1&1 = 1 ; 1&0 = 0
             */
            $checkUserAgentStr = $location & 1;
            $checkName = $location & 2;
            $checkComment = $location & 4;
            $checkVersion = $location & 8;

            if($checkUserAgentStr)
            {
                if( strpos( strtolower($useragent), $keyword->browser_key, 0 ) !== false )
                {
                    $commentmatched = true;
                    $matched = true;
                }
            }
            else if($checkName || $checkComment || $checkVersion)
            {
                foreach($products as $product)
                {
                    if($checkComment)
                    {
                        if( strpos( strtolower($product["comment"]), $keyword->browser_key, 0 ) !== false )
                        {
                            $commentmatched = true;
                            $matched = true;
                        }
                    }
                    if($checkName)
                    {
                        if( strpos( strtolower($product["name"]), $keyword->browser_key, 0 ) !== false )
                        {
                            $productmatch = true;
                            $matched = true;
                        }
                    }
                    if($checkVersion)
                    {
                        if( strpos( strtolower($product["version"]), $keyword->browser_key, 0 ) !== false )
                        {
                            $versionmatched = true;
                            $matched = true;
                        }
                    }
                    if($matched)
                    {
                        break;
                    }
                }
            }
            if($matched)
            {
                //this is browser, set and return arguments
                $Browser = new js_Browser();//we copy each member manually to be sure about that what is inside. Additional we use getEscaped() method
                $Browser->browser_id = $keyword->browser_id;
                $Browser->browsertype_id = $keyword->browsertype_id;
                $Browser->browser_key = $keyword->browser_key;
                $Browser->browser_name = $db->getEscaped( $keyword->browser_name );
                $Browser->browser_img = $keyword->browser_img;
                $Browser->browser_type = $keyword->browser_type;
                #__jstats_browserstype (with entries)
                $__jstats_browserstype = unserialize(_JS_DB_TABLE__BROWSERSTYPE);//whole table
                //fill missing entries in $Browser object
                $Browser->browsertype_name = $__jstats_browserstype[$Browser->browsertype_id]['browsertype_name'];
                $Browser->browsertype_img  = $__jstats_browserstype[$Browser->browsertype_id]['browsertype_img'];

                if(!$productmatch && $commentmatched)
                {
                    if( preg_match( '/' . $Browser->browser_key . '[\/\sa-z]*([\d\.]*)/i', strtolower($product["comment"]), $version ) ) {
                        if (isset($version[1])) {
                            $BrowserVersion = $version[1];
                        }
                    }
                }
                else if(!$productmatch && $versionmatched)
                {
                    if( preg_match( '/' . $Browser->browser_key . '[\/\sa-z]*([\d\.]*)/i', strtolower($product["version"]), $version ) ) {
                        if (isset($version[1])) {
                            $BrowserVersion = $version[1];
                        }
                    }
                }
                else
                {
                    $BrowserVersion = $product["version"];
                }
                $Browser->products = $products;
                $Browser->version = $BrowserVersion;
                return $Browser;
            }
		}

        $browser = new js_Browser();
        $browser->browser_id = _JS_DB_BRWSR__ID_UNKNOWN;
        js_JSCountVisitor::checkUnknownBotFromUserAgent($useragent, $browser);
        $browser->products = $products;

		return $browser;//return unknown
	}

	function findOsFromUserAgent( &$db, &$systems_keywords, $useragent, &$products)
    {
		if (count($products) < 1)
        {
            return js_OS::getUnknownOS();
        }

		foreach( $systems_keywords as $system )
        {
            $matched = false;
            $versionmatched = false;
            $commentmatched = false;
            $productmatch = false;
            $location = 7;//$system->location;
            /**
             *  Each Bit represents one flag and we extract them by making a bit operation 1&1 = 1 ; 1&0 = 0
             */
            $checkUserAgentStr = $location & 1;
            $checkName = $location & 2;
            $checkComment = $location & 4;
            $checkVersion = $location & 8;

            if($checkName || $checkComment || $checkVersion)
            {
                foreach($products as $product)
                {
                    if($checkComment)
                    {
                        if( strpos( strtolower($product["comment"]), $system->os_key, 0 ) !== false )
                        {
                            $commentmatched = true;
                            $matched = true;
                        }
                    }
                    if($checkName)
                    {
                        if( strpos( strtolower($product["name"]), $system->os_key, 0 ) !== false )
                        {
                            $productmatch = true;
                            $matched = true;
                        }
                    }
                    if($checkVersion)
                    {
                        if( strpos( strtolower($product["version"]), $system->os_key, 0 ) !== false )
                        {
                            $versionmatched = true;
                            $matched = true;
                        }
                    }
                    if($matched)
                    {
                        break;
                    }
                }
            }
            if(!$matched && $checkUserAgentStr)
            {
                if( strpos( strtolower($useragent), $system->os_key, 0 ) !== false )
                {
                    $commentmatched = true;
                    $matched = true;
                }
            }

            if($matched)
            {
                $OS = new js_OS();//we copy each member manualy to be sure about that what is inside. Additional we use getEscaped() method
                $OS->os_id = $system->os_id;
                $OS->os_type = $system->os_type;
                $OS->os_key = $system->os_key;
                $OS->os_name = $db->getEscaped( $system->os_name );
                $OS->os_img = $system->os_img;

                #__jstats_ostype (with entries)
                $__jstats_ostype = unserialize(_JS_DB_TABLE__OSTYPE);//whole table
                //fill missing entries in $OS object
                $OS->ostype_name = $__jstats_ostype[$OS->os_type]['ostype_name'];
                $OS->ostype_img = $__jstats_ostype[$OS->os_type]['ostype_img'];

                return $OS;
            }
		}

        return js_OS::getUnknownOS();
	}

	/**
	 * @param string  $UserAgent  eg.: "mozilla/5.0 (windows; u; windows nt 5.1; en-gb; rv:1.8.1.15) gecko/20080623 firefox/2.0.0.15"
	 * @param integer $Browser   
	 *
	 * @return bool - true on success
	 */
	function checkUnknownBotFromUserAgent($UserAgent, &$Browser /*in-out*/ ) {

		if (
		   	    ( strpos( $UserAgent, 'crawl',  0 ) !== false )
			|| 	( strpos( $UserAgent, 'spider', 0 ) !== false )
			|| 	( strpos( $UserAgent, 'bot',    0 ) !== false )
		) {
            if($Browser->browser_id <= 0)
            {
                $Browser->browser_id   = _JS_DB_BRWSR__ID_BOT_UNKNOWN;
                $Browser->browser_key  = _JS_DB_BRWSR__KEY_BOT_UNKNOWN;
                $Browser->browser_name = _JS_DB_BRWSR__NAME_BOT_UNKNOWN;
                $Browser->browser_img  = _JS_DB_BRWSR__IMG_BOT_UNKNOWN;
            }

            $Browser->browser_type = _JS_DB_IPADD__TYPE_BOT_VISITOR;
			return true;
		}

		return false;
	}

    /**
     * @depricated Outsources to ip.info.php
     */
	function isIpAddressIntranet( $ipAddressStr ) {
        return IPInfoHelper::isIpAddressIntranet( $ipAddressStr );
	}

    /**
     * @depricated Outsources to ip.info.php
     */
	function isIpAddressLocalHost( $ipAddressStr ) {
        return IPInfoHelper::isIpAddressLocalHost( $ipAddressStr );
	}
	
    /**
     * @depricated Outsources to ip.info.php
     */
	function isIpAddressValidRfc3330( $ipAddress ) {
        return IPInfoHelper::isIpAddressValidRfc3330( $ipAddress );
	}

    /**
     * @depricated Outsources to ip.info.php
     */
    function executeWhois( $server, $query ) {
        return IPInfoHelper::executeWhois( $server, $query );
    }


    /**
     * @depricated Outsources to ip.info.php
     */
    function queryWhois( $ip_to_check, $server, $query, &$ipFrom, &$ipTo, &$result ) {
         return IPInfoHelper::queryWhois( $ip_to_check, $server, $query, $ipFrom, $ipTo, $result );
   }

	/**
	 *  Create or update visits table and return its id
     *
     *  @return mixed - false on failure; integer visit_id on success
     */
	function registerVisit( $client_id, $ipaddress, &$client ) {

		js_echoJSDebugInfo('Perform Visit counting process', '');

		//@todo perf

        /**
         * Set Cookie to help us later to find this user again
         */
        setcookie("cltid", "".$client_id, 2147483647, '/'); //todo make cookies available for subdomains

        $onlinetime = (($client->client_type == _JS_DB_IPADD__TYPE_BOT_VISITOR) ? $this->JSConf->onlinetime_bots : $this->JSConf->onlinetime);

        $onlinetime_s = 0;
        /**
         * If set to ininity, we limit it to one year
         */
        if($onlinetime <= 0)
        {
           $onlinetime_s = 365 * 24 * 60 * 60;
        }
        else
        {
           $onlinetime_s = $onlinetime * 60;
        }

        $time_to = $this->now_timestamp;
        $time_from = $time_to - $onlinetime_s;
        $visit_id = null;
        /**
         * If we find a cookie, we could speed up the SQL
         */

        if(isset($_COOKIE['js_vsid']))
        {
            $js_vsid = JRequest::getVar( 'js_vsid', null, 'cookie' );
            if(!empty($js_vsid))
            {
                $js_vsid = $this->db->getEscaped($js_vsid);
                $query = "SELECT v.visit_id, v.changed_at FROM #__jstats_visits as v WHERE v.visit_id = $js_vsid";

                $this->db->setQuery( $query );
                $visit_row = $this->db->loadObject();
                if ($this->db->getErrorNum() > 0)
                {
                    js_echoJSDebugInfo("".$this->db->getErrorMsg(), '');
                }
                if(!empty($visit_row))
                {
                    $visit_id = $visit_row->visit_id;
                    js_echoJSDebugInfo('We found the last visit using the cookie value - ID='.$visit_id, '');

                    if($visit_row->changed_at < $time_from)
                    {
                        js_echoJSDebugInfo('Timestamp '.$visit_row->changed_at.' is after '.$time_from.' - online_time = max. '.$onlinetime_s.'s' );
                        $visit_id = null;
                    }
                }
            }
        }
        /**
         * Try to find the visit_id on the old way
         */
        if(empty($visit_id))
        {
            /**
             * Visits are per client_id and IP. We will create a new visit in the case of the IP has changed, even if the client is within the visit interval
             */
            $query = "SELECT v.visit_id
             FROM #__jstats_visits as v
             WHERE v.client_id = $client_id AND v.ip = $ipaddress AND v.changed_at >= $time_from";

            $this->db->setQuery( $query );
            $visit_id = (int)$this->db->loadResult();
            if ($this->db->getErrorNum() > 0)
            {
                js_echoJSDebugInfo("".$this->db->getErrorMsg(), '');
                return false;

            }
        }

		$joomla_userid = $this->getJoomlaCmsUserId();

		if( $visit_id ) {
			js_echoJSDebugInfo('Visit-Session still present. Continue this Visit '.$visit_id);
			// it's not the 1st page request, so update visits row

			$query = "UPDATE #__jstats_visits SET changed_at= $time_to";

			if( !empty($joomla_userid) ) {
				// if User login to Joomla CMS at least once in entire sesion, store his 'Joomla Id' //do not clear the UserId if the user logs out.
				$query .= ', joomla_userid = ' . $joomla_userid . ' ';
			}
			$query .= ' WHERE visit_id = \'' . $visit_id . '\'';

			$this->db->setQuery( $query );
			if (!$this->db->query())
            {
                js_echoJSDebugInfo("".$this->db->getErrorMsg(), '');
                return false;
            }
		} else {
			// this is 1st page request, lets create a visits entry
			js_echoJSDebugInfo('This is new Visit', '');

			$query = 'INSERT INTO #__jstats_visits (client_id, joomla_userid, changed_at, ip)'
			. ' VALUES ('
			. ' ' . $client_id . ','
            . ' ' . $joomla_userid . ','
            . ' ' . $time_to . ','
            . ' ' . $ipaddress
			. ' )'
			;
			$this->db->setQuery( $query );
			if (!$this->db->query())
            {
                js_echoJSDebugInfo("".$this->db->getErrorMsg(), '');
                return false;
            }

			$visit_id = $this->db->insertid();
		}

        /**
         * Remember the visit
         */
        if(!empty($visit_id))
        {
            setcookie("js_vsid", "".$visit_id, time() + $onlinetime_s , '/');
        }

		return $visit_id;
	}
	
	function getPageTitle() {
		$page_title = '';
		
		//outside joomla we can not check page title
		if( defined( '_JS_STAND_ALONE' ) )
        {
            $page_title = _JS_PHP__PAGE_TITLE_FOR_PAGES_OUTSIDE_JOOMLA_CMS;
        }
		else
        {
            $document=& JFactory::getDocument();
            $page_title = $this->db->getEscaped( $document->getTitle() );
        }

		// trim page title if longer than 255 characters
		if( strlen( $page_title ) > 255 ) {
			$page_title = substr( $page_title, 0, 254 );
		}
		
		return $page_title;
	}
	
	/** This function remove lang setting from page URL to treat multi language versions of one page as the same
	 *  
	 *  It is used when $this->JSConf->enable_i18n == true; "I18n Support"; "Multiple translations as one"
	 *
	 *  @todo we should check if SEF or i18n is enabled before we remove anything!
	 */
	function removeLanguageFromUrl( $url ) {

		// @todo mic 20081013: check if position 8 is correct ???
		if( strpos( $url, '?lang=' ) !== false ) {
			$url = str_replace( substr( $url, strpos( $url, '?lang=' ), 8 ), '', $url );
		} else if( strpos( $url, '&lang=' ) !== false ) {
			$url = str_replace( substr( $url, strpos( $url, '&lang=' ), 8 ), '', $url );
		} else if( strpos( $url, 'lang,' ) !== false ) { //for SEF urls
			$url = str_replace( substr( $url, strpos( $url, 'lang,' ), 8 ), '', $url );
		}
		
		return $url;
	}

	/** return page imprssion_id or 0 on fail */
	function registerPageImpression( $visit_id, $page_url, &$page_id, $domain = null, $protocol = null ) {

		js_echoJSDebugInfo('Perform Page counting process '.$domain, '');

		if( $page_url == '' )
			return 0;

		$page_title = $this->getPageTitle();

		if( $this->JSConf->enable_i18n ) {
			$page_url = $this->removeLanguageFromUrl( $page_url );
		}

		$query = ''
		. ' SELECT'
		. '   page_id,'
		. '   page_title'
		. ' FROM'
		. '   #__jstats_pages'
		. ' WHERE'
		. '   page = \'' . $this->db->getEscaped( $page_url ) . '\'';

        if(!empty($domain))
        {
           $query = $query . ' AND ( domain LIKE \'' . $this->db->getEscaped( $domain ) . '\' )';
           $query = $query. ' ORDER BY `domain` DESC';
        }

		$query = $query. ' LIMIT 1'
		;
		$this->db->setQuery( $query );
		$row = $this->db->loadObject();

		$page_id = 0;
        $create = true;
		if ( $row )
        {
            $create = false;
			$page_id = $row->page_id;

            $db_title = trim($row->page_title);
            $current_title = trim($page_title);
            /*if(strcmp($db_title, $current_title) == 0)
            {
               $create = true;
            }
            else*/
            {
                if( empty($db_title)  )
                {
                     //if(empty($domain))
                    {
                        $query = 'UPDATE #__jstats_pages'
                        . ' SET page_title = \'' . $this->db->getEscaped($page_title) . '\''
                        . ' WHERE page_id = \'' . $page_id . '\''
                        ;
                    }
                    /* else
                     {
                         $query = 'UPDATE #__jstats_pages'
                         . ' SET page_title = \'' . $this->db->getEscaped($page_title) . '\', domain = \'' . $this->db->getEscaped($domain) . '\''
                         . ' WHERE page_id = \'' . $page_id . '\''
                         ;
                     }*/
                    $this->db->setQuery( $query );
                    if (!$this->db->query())
                        return false;
                }
            }
		}
        if($create)
        {
            $query = null;

            if(empty($domain))
            {
                $query = 'INSERT INTO #__jstats_pages (page, page_title)'
                . ' VALUES (\'' . $this->db->getEscaped( $page_url ) . '\', \'' . $this->db->getEscaped($page_title) . '\')'
                ;
            }
            else
            {
                $query = 'INSERT INTO #__jstats_pages (page, page_title, domain)'
                . ' VALUES (\'' . $this->db->getEscaped( $page_url ) . '\', \'' . $this->db->getEscaped($page_title) . '\', \'' . $this->db->getEscaped($domain) . '\')'
                ;
            }
			$this->db->setQuery( $query );
			if (!$this->db->query())
				return false;

			$page_id = $this->db->insertid();
		}

        /**
         * This is supposed to be GMT. timezone If you like to change the timezone, please do it only on the presentation layer only.
         *
         * We do not use a automatic SQL Timestamp, because it might be not on line with the PHP time
         *
         * @author Andreas Halbig
         */
        $gmt_timestamp = $this->now_timestamp;
		$query = 'INSERT INTO `#__jstats_impressions` (`page_id`, `visit_id`, `timestamp`)'
		. ' VALUES ('.$page_id.','.$visit_id.','.$gmt_timestamp.')'
		;
		$this->db->setQuery( $query );
		if (!$this->db->query())
			return false;

		//imprssion_id not implemented yet
		//$imprssion_id = $this->db->insertid();
		//js_echoJSDebugInfo('This is imprssion_id=\''.$imprssion_id.'\'', '');
		//return $imprssion_id; 

		js_echoJSDebugInfo('Page counting process successful', '');

		return true;
	}

	/** return ref_url only when there was redirection from different domain or '' when there this is not redirection */
	function getReferrer() {
		
		if( !isset( $_SERVER['HTTP_REFERER'] ) ) {
			//js_echoJSDebugInfo('Referrer not set, nothing to register', '');
			return '';
		}

		$ref_url = trim( $_SERVER['HTTP_REFERER'] );

		if( $ref_url == '' ) {
			//js_echoJSDebugInfo('Referrer is empty, nothing to register', '');
			return '';
		}

		if( !isset( $_SERVER['HTTP_HOST'] ) ) {
			//js_echoJSDebugInfo('HTTP_HOST is not set, unable to determine if this is redirection or not. RegisterRefferer process ended', '');
			//return '';
		}

		//why We allow only http:// and https://?
		/*
		if ( (substr( $ref_url, 0, 7 ) != 'http://') && (substr( $ref_url, 0, 8 ) != 'https://') )
		{
			//js_echoJSDebugInfo('This is not http:// nor https:// - refferer not registered', '');
			//return '';
		}
		*/
		
		return empty($ref_url) ? $ref_url : $this->db->getEscaped($ref_url);
	}
	
	function getDomainFromUrl($url) {
		$dom = $url;
		
		//remove the protocol
        $pos = strpos( $dom , '://');

        if($pos > 0)
        {
           $dom = substr( $dom, $pos+3 );
        }
        
		if ( strtolower(substr( $dom, 0, 4 )) == 'www.' )
        {
            $dom = substr( $dom, 4 );
        }

		//cut domain
		$pos = strpos( $dom, '/' );
		if( $pos !== false )
			$dom = substr( $dom, 0, $pos );

		return $dom;
	}
	
	/** return referrer_id or 0 on fail */
	function registerReferrer( $visit_id, $ref_url, $ref_domain ) {

		js_echoJSDebugInfo('Perform RegisterReferrer process', '');

 		/*  NOTICE: visit_id was introduced in v3.0.0.372 - old data were NOT converted to this value, so it can not be used!! It is introduced to collect data for the future!! (not all data could be converted to new format, that is why now we duplicate data!) */
		$query = 'INSERT INTO #__jstats_referrer (referrer, domain, timestamp, visit_id)'
		. ' VALUES (\'' . $this->db->getEscaped($ref_url) . '\','
		. ' \'' . $this->db->getEscaped($ref_domain) . '\''
		. ','. $this->now_timestamp
		. ','. $visit_id
		. ')'
		;
		$this->db->setQuery( $query );
		if (!$this->db->query())
        {
            js_echoJSDebugInfo("".$this->db->getErrorMsg(), '');
            return false;
        }

        $referrer_id = $this->db->insertid();

		js_echoJSDebugInfo('Successfully registered referrer '.$ref_url, '');

		return $referrer_id; //$imprssion_id should be primary for #__jstats_referrer table and it should be returned here
	}

    /**
     * @param  $url
     * @param  $keys
     * @param boolean $localhost
     * @return #Fsubstr|#M#CJRequest.getVar|#M#Vrouter.parse|string|?
     */
	function getKeyWordsStr($url, $keys, $localhost = false)
	{
		$query = str_replace('?', '&', $url); //str_replace is faster than parse_url() function
	
		$ar = explode("|", $keys);
		for ($i = 0; $i < count($ar); $i++)
        {
            $key = $ar[$i];
            if (defined( '_JEXEC' ))
            {
                /**
                 * Remove the = after the keyword
                 */
                $keyName = substr($key, 0, strlen($key)-1);
                if($localhost)
                {
                    /**
                     * First we check if there is an post parameter, because in this case the search
                     * string does not appear again in the referrer URL. We  do not have to parse
                     * referrer URL in this case
                     */
                    $param = JRequest::getVar($keyName, '');
                    if(!empty($param))
                    {
                        return $param;
                    }
                }

               /**
                * To get the SEF working, we should use the Joomla Feature
                */
               $appSite = &JApplication::getInstance('site');
               $uri = JURI::getInstance($url);
               $router = $appSite->getRouter();
               $parsedURI = $router->getVars();
               js_echoJSDebugInfo('URI', $uri);
               js_echoJSDebugInfo('Router', $router);
               if(isset($parsedURI[$keyName]))
               {
                   $keyword = $parsedURI[$keyName];
                   /**
                    * If this enquiry is performed on the local host, we are called twice here.
                    * Once the search is performed and when the user is going to select one of the results.
                    *
                    * We simply want to prevent, that search keywords are counted twice, so we count only
                    * the first time the keyword.
                    *
                    * Unlike search engines, we would count the keyword on input and not on selecting a result set, this
                    * is supposed to be ok!
                    */
                   if($localhost)
                   {
                       $param = JRequest::getVar($keyName, '');
                       if(!empty($param) && $param != $keyword)
                       {
                           return $keyword;
                       }
                   }
                   else
                   {
                       return $keyword;
                   }
               }
                else
                {
                    $keyword = $uri->getVar($keyName);
                    if( !empty($keyword) )
                    {
                        return $keyword;
                    }
                }
            }
            else
            {
                $pos = strpos( $query, '&'.$key );
                if( $pos !== false ) {
                    $pos_begin = $pos+strlen($key)+1;
                    $pos_end = strpos( $query, '&', $pos_begin );

                    if( $pos_end !== false )
                        return substr( $query, $pos_begin, $pos_end-$pos_begin );
                    else
                        return substr( $query, $pos_begin );
                }
            }
		}
	
		// 1) below method is working correctly but it is above two times slower than operating on strings
		// 2) preg_match CRASHES! on some machines!! For details see [#18344] *** glibc detected *** double free or corruption (fasttop) with AOL serach results links
		//if( preg_match( '/[\?&]('.$keys.')(.+?)(&|$)/i', $url, $matches ) ) {
		//	for ($i=2; count($matches); $i++) { //we must start from 2 (not 0)
		//		if( $matches[$i] != null ) {
		//			return $matches[$i];
		//		}
		//	}
		//}
	
		return '';//no keywords in query
	}
	
	
	function getKeyWords( $ref_url, $ref_domain, &$kwrds, $localhost = false ) {

        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
		$kwrds = '';

		$query = ''
		. ' SELECT'
		. '   searcher_id      AS searcher_id,'
		. '   searcher_name    AS searcher_name,'
		. '   searcher_domain  AS searcher_domain,'
		. '   searcher_key     AS searcher_key'
		. ' FROM'
		. '   #__jstats_searchers'
		. ' WHERE'
		//. '   searcher_id>'._JS_DB_SERCH__ID_SEARCH_JOOMLA_CMS //this is special entry, we must omit it (entry for future use)
		. '    \''.$JSDatabaseAccess->db->getEscaped($ref_domain).'\' LIKE CONCAT(\'%\', `searcher_domain` , \'%\')'
		. ' LIMIT 1'
		;


		$JSDatabaseAccess->db->setQuery( $query );
		$row = $JSDatabaseAccess->db->loadObject();
		if ($JSDatabaseAccess->db->getErrorNum() > 0) {
            js_echoJSDebugInfo("".$JSDatabaseAccess->db->getErrorMsg(), '');
			return 0;
		}

		if (!$row)
			return 0;

		$kwrds = js_JSCountVisitor::getKeyWordsStr( $ref_url, $row->searcher_key, $localhost );
		$kwrds = urldecode( $kwrds );
		$kwrds = trim( $kwrds );
		if ( empty($kwrds) )
			return 0;
		
		$searcher_id = $row->searcher_id; //keywords not empty, so assign searcher_id

		return $searcher_id;
	}

	
	/**
	 * adds search items from search engines into database
	 *
	 * @param string $ref_domain
	 * @param string $ref_url
	 * @return keyword_id or null. On fail false is returned
	 */
	function registerKeyWords( $visit_id, $ref_url, $ref_domain, $referrer_id = null, $localhost = false ) {

		js_echoJSDebugInfo('Perform Register Key Words process', '');
		
		$kwrds = '';
		$searcher_id = $this->getKeyWords( $ref_url, $ref_domain, $kwrds, $localhost );

		if ( ( empty($kwrds) ) || ( $searcher_id == 0 ) ) {
			js_echoJSDebugInfo('Search engine (searcher) not recognized or empty keywords', '');
			return null;
		}

		/*  NOTICE: visit_id was introduced in v3.0.0.372 - old data were NOT converted to this value, so it can not be used!! It is introduced to collect data for the future!! (not all data could be converted to new format, that is why now we duplicate data!) */
		$query = 'INSERT INTO #__jstats_keywords (timestamp, searcher_id, keywords, visit_id, referrer_id)'
		. ' VALUES ('.$this->now_timestamp.', '
		. $searcher_id . ','
		. ' \'' . $this->db->getEscaped( $kwrds ) . '\','
        . $visit_id .','
        . (empty($referrer_id ) ? 'NULL' : $referrer_id)
        .')'
		;
		$this->db->setQuery( $query );
		if (!$this->db->query())
        {
            js_echoJSDebugInfo("".$this->db->getErrorMsg(), '');
            return false;
        }

        $keyword_id = $this->db->insertid();
		js_echoJSDebugInfo('Successfully registered keywords '.$kwrds, '');
		
		return $keyword_id;
	}
	
	/**
	 * This function recognize and register when visitor get to Your (Joomla CMS) pages
	 *    from other pages (like searches (eg. google.com) etc.)
	 *
	 * NOTICE: 
	 *   To get full information about entries and redirections from other sites,
	 *   You must sum results from 2 tables: #__jstats_keywords and #__jstats_referrer
	 *
	 * @param in  int $visit_id                - current visit_id. Needed to store data in database (visit_id store time, visitor data etc.)
	 * @param out int $was_keyword_registered  - when key words were registered returned integer will be greather then 0 (in fact $visit_id will be returned)
	 * @param out int $was_referrer_registered - when there was redirection (excluding search engines) registered returned integer will be greather then 0 (in fact $visit_id will be returned)
	 *
	 * @return bool - false when that was not redirection or on failure
	 */
	function registerReferrerOrKeyWords( $visit_id, &$was_keyword_registered, &$was_referrer_registered ) {

		$was_keyword_registered  = null;
		$was_referrer_registered = null;

		$ref_url = $this->getReferrer();
		if (empty($ref_url)) {
			js_echoJSDebugInfo('Referrer not set.', '');
			return false;
		}

		$ref_domain = $this->getDomainFromUrl($ref_url);
		/*
        if ($ref_domain == '') {
			js_echoJSDebugInfo('Empty domain! Registering Referrer or/and Keywords fail!', '');
			return false;
		}
        */

        $localReferrer = false;
        if( isset( $_SERVER['HTTP_HOST'] ) ) {
            $hst = trim( $_SERVER['HTTP_HOST'] );
            if ( strpos( $ref_url, $hst ) !== false ) {
                js_echoJSDebugInfo('This is not redirection from other domain - do not register referrer', '');
                $localReferrer = true;
                $ref_domain = 'localhost';
            }
        }

        $referrer_id = null;

        /**
         * First we like to store the whole referrer URL, but only if the URL is not from the own webserver
         */
        if(!$localReferrer)
        {
            $was_referrer_registered = $this->registerReferrer( $visit_id, $ref_url, $ref_domain );

            /**
             * Something went wrong
             */
            if ( $was_referrer_registered === false )
                return false;

            if ( $was_referrer_registered > 0 )
                $referrer_id = $was_referrer_registered;
        }

        /**
         * If the referrer_id was created, we pass it to the keyword process as we like to link the complete URL to the parse keywords
         */
		$was_keyword_registered = $this->registerKeyWords( $visit_id, $ref_url, $ref_domain, $referrer_id, $localReferrer );

		if ( $was_keyword_registered === false )
			return false;
		
		if ( $was_keyword_registered > 0 )
			return true;



		return true;
	}
}