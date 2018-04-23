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

require_once( dirname(__FILE__) .DS.'..'.DS. 'libraries'.DS. 'base.classes.php' );
require_once( dirname(__FILE__) .DS.'..'.DS. 'database' .DS. 'db.constants.php' );
require_once( dirname(__FILE__) .DS.'..'.DS. 'database' .DS. 'access.php' );

/**
 * .
 * User: ahalbig
 * Date: 17-Nov-2009
 * Time: 00:16:55
 *
 */
class IPInfoHelper {

	/**
     * @static Because of PHP 4.0 this is not explicit defined as static
     *
     * Added the purpose to improve the performance and load address information in
     * the admin page and not during the visitors requests itself
     *
	 * This function loads visitor address information. Basing on $IpAddress it returns information about
	 *   IP & location.
	 *
	 * @param out $Visitor - object of class js_Visitor
	 * @param out $updateTldInJSDatabase - true or false
	 * @param out $$IpAddress - the IP to check
	 *
	 * @return bool - true on success
     *
     * @author Andreas Halbig
     * @modified 16.11.2009
     *
     * @depricated
	 */
	 function performLocationCheck( &$Location, $IpAddress , $updateTldInJSDatabase) {

        global $js_whoisCache;

        if($js_whoisCache == null)
        {
            $js_whoisCache = array();
        }

		js_echoJSDebugInfo('Retrieve IP Infos', '');
        $JSConf = js_JSConf::getInstance();
		$visitor_tld		= '';//@todo define or whole object of class js_Tld should be here
        $IpAddressStr = long2ip($IpAddress);
        $visitor_nslookup	= null;//$IpAddressStr;
        $Tld = null;

        if( $Location == null )
        {
           $Location = new js_Location();
           $Location->ip = $IpAddress;
        }

        /**
         * Retrieve any previous results
         */
        $cacheEntry = null;

        if(isset($js_whoisCache[$IpAddressStr]))
        {
           $cacheEntry = $js_whoisCache[$IpAddressStr];
        }
        if( $cacheEntry != null )
        {
           $Tld = $cacheEntry["tld"];

           $visitor_nslookup = $cacheEntry["nslookup"];
           $Location->nslookup = $visitor_nslookup;
           $Location->code = $Tld->tld;

            //The msnbot does not show his identity within the useragent string, so we have to catch it on the ip level
           if(!empty($Location->nslookup) && strpos($Location->nslookup, "msnbot") !== false)
           {
              $Location->ip_type = _JS_DB_IPADD__TYPE_BOT_VISITOR;
           }

           js_echoJSDebugInfo('NSLookup '.$Location->nslookup.' & Tld from Cache '.$Location->code, '');

            if(!empty($Location->code))
            {
                return true;
            }
        }

        if($Location == null)
        {
            js_echoJSDebugInfo('TLD 1 still empty', '');
        }

        /**
         * Call our plugins - this would be as example required to retrieve data from the ip2nation integration
         */
        js_PluginManager::fireEventUsingSource( 'resolveLocation', $Location);

        /**
         * The plugin could have set the tld
         */
        $Tld = $Location->Tld;

        if($Tld == null)
        {
            if( IPInfoHelper::isIpAddressIntranet( $IpAddressStr ) )
            {
                $visitor_tld		= 'intranet';//@todo define or whole object of class js_Tld should be here
                js_echoJSDebugInfo('This IP address is INTRANET. We do not search TLD for this address', '');
            }
            else if( IPInfoHelper::isIpAddressLocalHost( $IpAddressStr ) )
            {
                $visitor_tld		= 'localhost';//@todo define or whole object of class js_Tld should be here
                js_echoJSDebugInfo('This IP address is LOCALHOST. We do not search TLD for this address', '');
            }
            else if( !IPInfoHelper::isIpAddressValidRfc3330( $IpAddressStr ) )
            {
                js_echoJSDebugInfo("This IP address $IpAddressStr ($IpAddress) is NOT VALID according to RFC3330. We do not search TLD for this address", '');
            }
            else
            {
                js_echoJSDebugInfo("This IP address is valid", '');

                $visitor_nslookup = js_gethostbyaddr( $IpAddressStr );

                IPInfoHelper::getTldFromNslookupString( $visitor_nslookup, $visitor_tld );
                if( $visitor_tld === '' || $visitor_tld === 'eu' || strlen( $visitor_tld ) > 2 || is_numeric( $visitor_tld ) )
                {
                    $tld_res = null;
                    if(!empty($Location->code))
                    {
                        $tld_res = $Location->code;
                    }
                    else
                    {
                        //below function return CountryCode not TLD. Is below code correct?
                        /*$tld_res = IPInfoHelper::getCountryCodeFromJSDatabase( $IpAddressStr, $visitor_tld );

                        // Enzo: enable_whois should control the WHOIS only, not nslookup and database query
                        if( $tld_res == false && $JSConf->enable_whois) {
                            $ipFrom	= '0.0.0.0';
                            $ipTo	= '255.255.255.255';

                            IPInfoHelper::getTldFromRipeServers( $IpAddressStr, $ipFrom, $ipTo, $visitor_tld );
                            IPInfoHelper::updateTldInJSDatabase( $ipFrom, $ipTo, $visitor_tld );
                        } else {
                            js_echoJSDebugInfo('WHOIS option is turned OFF', '');
                        }

                        // GB is the only country code not matching the country TLD
                        if( strcasecmp($visitor_tld, 'gb') == 0 ) {
                            $visitor_tld = 'uk';
                        }   */
                    }
                }
                if(strcmp($visitor_nslookup, $IpAddressStr) == 0)
                {
                    $visitor_nslookup = null;
                }
            }
            $Tld = IPInfoHelper::getTldFromTld( $visitor_tld );
            $js_whoisCache[$IpAddressStr] = array("tld" => $Tld, "nslookup" => $visitor_nslookup);
            js_echoJSDebugInfo("NSLookup $visitor_nslookup - Tld ".$Tld->tld , '');
            $Location->Tld = $Tld;
            $Location->nslookup = $visitor_nslookup;
        }

		// create location object ------------------------------------------------
		if ($Tld == null) {
			//create unknown tld
			$Tld = js_Tld::getDefault();
            $Location->Tld = $Tld;
		}

        //The msnbot does not show his identity within the useragent string, so we have to catch it on the ip level
        if(!empty($Location->nslookup) && strpos($Location->nslookup, "msnbot") !== false)
        {
           $Location->ip_type = _JS_DB_IPADD__TYPE_BOT_VISITOR;
        }


        //@todo city not yet applied
        /**
        */
		js_echoJSDebugInfo('Location resolved', $Location);

		return true;
	}

    function getCityForIP($visitor_ip)
    {
        $ip_information = file_get_contents('http://ipinfodb.com/ip_query.php?ip='.$visitor_ip);
        preg_match("/<City(.*?)>(.+?)<\/City>/s", $ip_information, $matches);
        $city = "-";
        if(count($matches) > 2)
        {
           $city = $matches[2];
        }
        return $city;
    }

    /**
     * @static Because of PHP 4.0 this is not explicit defined as static
	 * Find and return TLD. This function operate on string.
	 *
	 * @param string $visitor_nslookup   - string returned by PHP method gethostbyaddr( $visitor_ip ); eg.: "crawl-66-249-70-72.googlebot.com", "sewer.com.eu", "66.249.70.72" (for this false will be returned)
	 * @param string $tld                - eg.: "us", "de", "pl"
	 * @return bool - true on success
	 */
	function &getTldFromNslookupString( $visitor_nslookup, &$tld ) {

		$pos = strrpos( $visitor_nslookup, '.' ) + 1;

		if( $pos > 1 ) {
			$xt = trim( substr( $visitor_nslookup, $pos ) );

			if( ereg( '([a-zA-Z])', $xt ) ) {
				$tld = strtolower( $xt );
				return true;
			}
		}

		return false;
	}


	/**
	 * @static Because of PHP 4.0 this is not explicit defined as static
     *
     * Find and return TLD. This function get Visitor IP and check it in JS database.
	 *
	 * @param string $visitor_ip    - eg.: "66.249.70.72"
	 * @param string $country_code  - eg.: "us", "de", "pl"     NOTICE: this is not TLD!!!
	 * @return bool - return false if there is no entry in JS DB for such IP
	 */
/*	function getCountryCodeFromJSDatabase( $visitor_ip, &$country_code ) {

        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
        $db = $JSDatabaseAccess->db;

		$query = 'SELECT country_code2'
		. ' FROM #__jstats_iptocountry'
		. ' WHERE inet_aton(\'' . $visitor_ip . '\') >= ip_from'
		. ' AND inet_aton(\'' . $visitor_ip . '\') <= ip_to'
		;
		$db->setQuery( $query );
		$tmp_country_code = $db->loadResult();
		if ($db->getErrorNum() > 0)
			return false;

		if( $tmp_country_code ) {
			$country_code = $tmp_country_code;
			return true;
		}

		return false;
	}  */

    /**
	 * @static Because of PHP 4.0 this is not explicit defined as static
     *
     * Query RIPE servers in internet about tld for given IP address
	 *
	 *    EU is used as the IANA generic country code; it is always returned
	 *      for 0.0.0.0 to 255.255.255.255 and some other generic IANA networks
	 *
	 *    AP is used as the APNIC generic country code; the real
	 *      country code can be obtained from the 'route' entry
	 *
	 * @param string $visitor_ip   - eg.: "66.249.70.72"
	 * @param string $tld          - eg.: "us", "de", "pl"
	 *
	 * NOTICE:
	 *    This function rise PHP warning very often! eg.:
	 *
	 * @return bool - return false if something goes wrong or could not determine tld
	 */
/*	function &getTldFromRipeServers( $ip_to_check, &$ipFrom, &$ipTo, &$tld) {

		$visitor_tld	= '';
		$countryCode	= '';
		$ipFrom			= '0.0.0.0';
		$ipTo			= '255.255.255.255';
		$whois			= array();
		$whoisResult	= array();

		// do RIPE Whois lookup for the IP address

		// Andreas: removed VERIO added AFRINIC,NTTCOM
		// mic 20081014: IMPORTANT the \n at the end of the query!
		$query		= '-s RIPE,ARIN,APNIC,RADB,JPIRR,AFRINIC,NTTCOM -T inetnum -G ' . $ip_to_check . "\n";
		$countryCode = IPInfoHelper::queryWhois( $ip_to_check, 'whois.ripe.net', $query, $ipFrom, $ipTo, $whoisResult );

		if( $countryCode === 'LACNIC' || $countryCode === 'EU' || $countryCode === 'AP' || $countryCode ===''){
			$query			= $ip_to_check . "\n";
			$countryCode	= IPInfoHelper::queryWhois( $ip_to_check, 'whois.lacnic.net', $query, $ipFrom, $ipTo, $whoisResult );
		}else{
            $whois = $whoisResult;
		}

		if( $countryCode === 'AfriNIC' || $countryCode === 'EU' || $countryCode === 'AP' || $countryCode===''){
			$query = '-T inetnum -r ' . $ip_to_check . "\n";
			$countryCode = IPInfoHelper::queryWhois( $ip_to_check, 'whois.afrinic.net', $query, $ipFrom, $ipTo, $whois );
		}else{
            $whois = $whoisResult;
		}

		js_echoJSDebugInfo('Answer from RIPE server', $whois);

        //if( array_key_exists( 'descr', $whois ) ) {
        //	$visitor_nslookup .= "\n" . $whois['descr'];
        //}
        //if( array_key_exists( 'role', $whois ) ) {
        //	$visitor_nslookup .= "\n" . $whois['role'];
        //}

		//$tld = strtolower( $countryCode );
        //$whoisCache[$ip_to_check] = $tld;
		return true;//@todo false should be returned on fail
	}*/

    /**
	 * @static Because of PHP 4.0 this is not explicit defined as static
     *
     * update TLDs in JS database
	 *
	 * @param string $
	 * @param string $
	 * @return bool - return false on fail
	 */
	  /*function &updateTldInJSDatabase( $ipFrom, $ipTo, $countryCode ) {

        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
        $db = $JSDatabaseAccess->db;

		// EU is used as the IANA generic country code; it is always returned
		// for 0.0.0.0 to 255.255.255.255 and some other generic IANA networks

		// AP is used as the APNIC generic country code; the real
		// country code can be obtained from the 'route' entry

		if( $countryCode !== '' && $countryCode !== 'eu' && $countryCode !== 'ap' ) {
			// found country code, enter it into iptocountry
			$query = 'INSERT INTO #__jstats_iptocountry (ip_from, ip_to, country_code2)'
			. ' VALUES (' . js_ip2long( $ipFrom ) . ',' . js_ip2long( $ipTo ) . ',\''	. $countryCode . '\')'
			;
			$db->setQuery( $query );
			$db->query();
			if ($db->getErrorNum() > 0)
				return false;
		}

		return false;
	}*/

    /**
	 * @static Because of PHP 4.0 this is not explicit defined as static
     *
     * checks if ip.address is a local address, therefore we do not check the whois or make a tld-lookup!
	 * needed for e.g. intranet cms
	 *
	 * @param string $ip
	 * @return bool
	 */
	function isIpAddressIntranet( $ipAddressStr ) {

		// mic: ONLY FOR DEBUG SET TO FALSE
		//return false;

		$local = '/^10|^169\.254|^172\.16|^172\.17|^172\.18|^172\.19|^172\.20|^172\.21|^172\.22|^172\.23|^172\.24|^172\.25|^172\.26|^172\.27|^172\.28|^172\.29|^172\.30|^172\.31|^192|0:0:0:0:0:0:0:1/';

		if( preg_match( $local, $ipAddressStr ) ) {
			return true;
		}

		return false;
	}

    /**
	 * @static Because of PHP 4.0 this is not explicit defined as static
     *
     * checks if ip.address is a local address, therefore we do not check the whois or make a tld-lookup!
	 * needed for e.g. intranet cms
	 *
	 * @param string $ip
	 * @return bool
	 */
	function isIpAddressLocalHost( $ipAddressStr ) {

		$substr4 = substr( $ipAddressStr, 0, 4 );

		if ( $substr4 === '127.' )
			return true;

		return false;
	}

	/**
	 * @static Because of PHP 4.0 this is not explicit defined as static
     *
     * checks if the given ip-address is valid
	 *
	 * From where we should get list of reserved blocks?
	 *    1) http://www.rfc-editor.org/rfc/rfc3330.txt
	 *    2) http://www.iana.org/assignments/ipv4-address-space/
	 * 	        "Many of the IP blocks which were formally unallocated are allocated now", so we use RFC3330
	 *
	 *
	 *
	 * Part of: http://www.rfc-editor.org/rfc/rfc3330.txt
	 *
	 *    Address Block             Present Use                       Reference
	 *    ---------------------------------------------------------------------
	 *    0.0.0.0/8            "This" Network                 [RFC1700, page 4]
	 *    10.0.0.0/8           Private-Use Networks                   [RFC1918]
	 *    14.0.0.0/8           Public-Data Networks         [RFC1700, page 181]
	 *    24.0.0.0/8           Cable Television Networks                    --
	 *    39.0.0.0/8           Reserved but subject
	 *                            to allocation                       [RFC1797]
	 *    127.0.0.0/8          Loopback                       [RFC1700, page 5]
	 *    128.0.0.0/16         Reserved but subject
	 *                            to allocation                             --
	 *    169.254.0.0/16       Link Local                                   --
	 *    172.16.0.0/12        Private-Use Networks                   [RFC1918]
	 *    191.255.0.0/16       Reserved but subject
	 *                            to allocation                             --
	 *    192.0.0.0/24         Reserved but subject
	 *                            to allocation                             --
	 *    192.0.2.0/24         Test-Net
	 *    192.88.99.0/24       6to4 Relay Anycast                     [RFC3068]
	 *    192.168.0.0/16       Private-Use Networks                   [RFC1918]
	 *    198.18.0.0/15        Network Interconnect
	 *                            Device Benchmark Testing            [RFC2544]
	 *    223.255.255.0/24     Reserved but subject
	 *                            to allocation                             --
	 *    224.0.0.0/4          Multicast                              [RFC3171]
	 *    240.0.0.0/4          Reserved for Future Use        [RFC1700, page 4]
	 *
	 *
	 * @param string $ipAddress
	 * @return string
	 */
	function isIpAddressValidRfc3330( $ipAddressStr ) {

		$substr2 = substr( $ipAddressStr, 0, 2 );
		$substr3 = substr( $ipAddressStr, 0, 3 );
		$substr4 = substr( $ipAddressStr, 0, 4 );
		$substr6 = substr( $ipAddressStr, 0, 6 );
		$substr8 = substr( $ipAddressStr, 0, 8 );
		$substr10 = substr( $ipAddressStr, 0, 10 );
		$substr12 = substr( $ipAddressStr, 0, 12 );
		$IpAsLong = js_ip2long( $ipAddressStr );

		return ( ( $ipAddressStr != NULL ) &&
			( $substr2 !== '0.' )     // Reserved IP block
			&& ( $substr3 !== '10.' ) // Reserved for private networks
			&& ( $substr3 !== '14.' ) // IANA Public Data Network
			&& ( $substr3 !== '24.' ) // Reserved IP block
			&& ( $substr3 !== '27.' ) // Reserved IP block
			&& ( $substr3 !== '39.' ) // Reserved IP block
			&& ( $substr4 !== '127.' ) // Reserved IP block
			&& ( $substr6 !== '128.0.' ) // Reserved IP block
			&& ( $substr8 !== '169.254.' ) // Reserved IP block
			&& ( ( $IpAsLong < js_ip2long( '172.16.0.0' )  ) // Private networks
				|| $IpAsLong > js_ip2long( '172.31.255.255'  ) )
			&& ( $substr8 !== '191.255.' ) // Reserved IP block
			&& ( $substr8 !== '192.0.0.' ) // Reserved IP block
			&& ( $substr8 !== '192.0.2.' ) // Reserved IP block
			&& ( $substr10 !== '192.88.99.' ) // Reserved IP block
			&& ( $substr8 !== '192.168.' ) // Reserved IP block
			&& ( ( $IpAsLong < js_ip2long( '198.18.0.0' ) ) // Multicast addresses
				|| ( $IpAsLong > js_ip2long( '198.19.255.255' ) ) )
			&& ( $substr12 !== '223.255.255.' ) // Reserved IP block
			&& ( ( $IpAsLong < js_ip2long( '224.0.0.0' ) ) // Multicast addresses
				|| ( $IpAsLong > js_ip2long( '239.255.255.255'  ) ) )
			&& ( ( $IpAsLong < js_ip2long( '240.0.0.0' ) ) // Reserved IP blocks
				|| ( $IpAsLong > js_ip2long( '255.255.255.255' ) ) )
		);
	}

   /**
    * @static Because of PHP 4.0 this is not explicit defined as static
    *
    * Executes a WHOIS query
    *
    * Appended By DVW
    *
    * @param string $server
    * @param string $query
    * @return array
    *
    * @since 2.3.x: added maximum time to fsockopen from server config
    * @todo mic 20081013: maybe moving ths function into a own class AND into backend?
    */
    function &executeWhois( $server, $query ) {

        $resultList = array();

        // mic 20081013: get maximum time for fsockopen - AT: NO, NO, NO!!!! We are on front page!
        //$timeout = ini_get( 'max_execution_time' );
		$timeout = 1;//value 0.1 is better but I am not sure if it is allowed

		js_echoJSDebugInfo('server', $server);
		js_echoJSDebugInfo('query', $query);

        if( ( $socket = fsockopen( gethostbyname( $server ), 43, $errno, $errstr, $timeout ) ) != false ) {
                // send the query string to the socket
                fputs( $socket, $query, strlen( $query ) );

                $result		= array();
                $appended	= false;
                while( !feof( $socket ) ) {
                    $contents = fgets( $socket, 4096 );
                    $contents = trim( $contents );
                    if( empty( $contents ) ) {
                        continue;
                    }

                    $first = $contents[0];

                    if( $first == '%' || $first == '<' || $first == '#' ) {
                        continue;
                    }

                    $comment = strstr( $contents, '//' );

                    if( $comment ) {
                        continue;
                    }

                    $seperatorIndex = strpos($contents, ':');

                    if( $seperatorIndex <= 0 ) {
                        continue;
                    }

                    $key	= trim( substr( $contents, 0, $seperatorIndex ) );
                    $value	= trim( substr( $contents, $seperatorIndex + 1 ) );
                    // Make sure we just have single spaces
                    $value	= preg_replace( '/\s+/', ' ', $value );

                    if( $key == 'inetnum') {
                        $appended	= false;
                        $result		= array();
                    }elseif( $key == 'source' ) {
                        if( !$appended ) {
                            $resultList[] = $result;
                        }
                        $appended = true;
                    }
                    if( array_key_exists( $key, $result ) ) {
                        $entry = $result[$key];
                        if( $entry ) {
                            $value = $entry . "\n" . $value;
                        }
                    }
                    $result[$key] = $value;
                }

                fclose( $socket );
        }

        //filter Results - We are only interested in first result using status ASSIGNED
        //Some results do not have "status", but this is could be our "ASSIGNED" result
        $returnList = array();

        foreach ( $resultList as $result ) {
            if( array_key_exists( 'status', $result ) ) {
                $status = $result['status'];
                //@at stripos() function is not supported by PHP 4.0 //@todo could we here use strpos(); function?
                // mic 20081013: re-added it, because stripos is a function in base.classes.php since 2.3.x
                $pos = stripos( $status, 'SSIGNED' );
                //$pos = strpos( strtolower( $status ), strtolower( 'SSIGNED' ) );

                if( $pos == false ) {
                    continue;
                }else{
                   return array( $result );
                }
            }
            $returnList[] = $result;
        }

        return $returnList;
    }


    /**
    *   @static Because of PHP 4.0 this is not explicit defined as static
    */
    //function queryWhois( $server, $query, &$ipFrom = "0.0.0.0", &$ipTo  = "255.255.255.255", &$result ) {//problem in PHP 4.0 (probably with defalut argument value)
    function &queryWhois( $ip_to_check, $server, $query, &$ipFrom, &$ipTo, &$result ) {
        $countryCode	= '';
        $resultList		= IPInfoHelper::executeWhois( $server, $query );

        if( !empty( $resultList ) ) {
            //$line	    = '';
            $prevline   = '';
            $getCountry = false;
            $getRange   = false;
            $result		= null;

            foreach ( $resultList as $whois) {
                // process the result of the Whois lookup
                if( empty( $whois ) ) {
                	continue;
                }

                if( array_key_exists( 'inetnum', $whois ) ) {
                    $inetnum = $whois['inetnum'];
                    // get IP range and see if it's narrower than the current range
                    // note: ip2long gives signed results, so we convert to unsigned using sprintf

					$getCountry = false;

					$values = null;

					if (substr_count($inetnum, ' - ') > 0)	// Netblock notation
					{
						$values = explode(' - ', $inetnum);
					}
					else if (substr_count($inetnum, '/') > 0)	// CIDR block notation
					{
						/* - Begin CIDR notation parser, heavily based on code from Leo Jokinen <legetz81@yahoo.com> - */

						$values = explode('/', $inetnum);

						if (is_array($values))
						{
							if (count($values) == 2)
							{
								$values[0] = trim($values[0]);
								$values[1] = trim($values[1]);
								if (strlen($values[0])>0 && strlen($values[1])>0)
								{
									$bin = '';
									for ($i = 1; $i <= 32; $i++)
										$bin .= $values[1] >= $i ? '1' : '0';
									for ($i = substr_count($values[0], "."); $i < 3; $i++)
										$values[0] .= ".0";

									$nm = ip2long(bindec($bin)); //@todo is this supposed to be signed Long? We need unsigned for the DB!!
									$v0 = ip2long($values[0]);
									if (is_int($nm) && is_int($v0))
									{
										$nw = ($v0 & $nm);
										$bc = $nw | (~$nm);

										$values[0] = long2ip($nw);
										$values[1] = long2ip($bc);
									}
								}
							}
						}

						/* - End CIDR notation parser ---------------------------------------------------------------- */
					}

					if (is_array($values))
					{
						if (count($values) == 2)
						{
							$values[0] = trim($values[0]);
							$values[1] = trim($values[1]);
							if (strlen($values[0])>0 && strlen($values[1])>0)
							{
								if (js_ip2long($values[0]) >= js_ip2long($ipFrom) &&
								    js_ip2long($values[1]) <= js_ip2long($ipTo))
								{
									$ipFrom = $values[0];
									$ipTo = $values[1];

									$getCountry = true;
								}
							}
						}
					}
                }

                if( array_key_exists( 'netname', $whois ) && $getCountry ) {
                    $netname = $whois['netname'];
                    // filter some of the generic networks

                    $ipA	= explode( '.', $ip_to_check );
                    $ipNet	= 'NET' . $ipA[0];

                    if( substr( $netname, 0, 6 )	=== 'LACNIC'
                    || substr( $netname, 0, 7 )		=== 'AFRINIC'
                    || substr( $netname, 0, 9 )		=== 'RIPE-CIDR'
                    || substr( $netname, 0, 9 )		=== 'ARIN-CIDR'
                    || substr( $netname, 0, 10 )	=== 'IANA-BLOCK'
                    || substr( $netname, 0, 13 )	=== 'IANA-NETBLOCK'
                    || substr( $netname, 0, 12 )	=== 'ERX-NETBLOCK'
                    || substr( $netname, 0, strlen( $ipNet ) ) === $ipNet ) {
                        $getCountry = false;
                    }

                    if( $server === 'whois.ripe.net' ) {
                        if( substr( $netname, 0, 6 ) === 'LACNIC' ) {
                        	$countryCode = 'LACNIC';
                        }
                        if( ( substr( $netname, 0, 7 ) === 'AFRINIC') || ( $ipA[0] === '41' ) ) {
                        	$countryCode = 'AfriNIC';
                        }
                    }
                }
                if( array_key_exists( 'OrgName', $whois ) ) {
                    // LACNIC Joint Whois entry, get country and IP range now
                    $result		= $whois;
                    $getCountry	= true;
                    $getRange	= true;
                }
                if( array_key_exists( 'role', $whois )  && $result == null ) {
                    // LACNIC Joint Whois entry, get country and IP range now
                    $result = $whois;
                }
                if( array_key_exists( 'NetRange', $whois ) && $getRange ) {
                    $NetRange = $whois['NetRange'];
                    // get IP range from LACNIC Joint Whois entry

                    $values = explode( ' - ', $NetRange );

                    if( js_ip2long( $values[0] ) >= js_ip2long( $ipFrom )
                    && js_ip2long( $values[1] ) <= js_ip2long( $ipTo ) ) {
                        $ipFrom	= $values[0];
                        $ipTo	= $values[1];
                    }

                    $getRange = false;
                }
                if( array_key_exists( 'country', $whois ) && $getCountry ) {
                    $country = $whois['country'];
                    // the last ip range was narrower than the ones before and we found
                    // a country entry; now extract the country entry

                    $countryCode = $country;

                    if( $countryCode !== 'AP') {
                    	$getCountry = false;
                    }
                }
                if( array_key_exists( 'nserver', $whois ) && $getCountry ) {
                    $nserver = $whois['nserver'];
                    // if there is no country entry, try to get the TLD from the name
                    // server entry (e.g. registro.br does not include a country code)

                    $pos = strrpos( $nserver, '.' ) + 1;

                    if( $pos > 1) {
                        $xt = trim( substr( $nserver, $pos ) );

                        if( ereg( '([a-zA-Z])', $xt ) ) {
                            $countryCode = $xt;
                        }
                    }
                }
                //RB: question for mic: why did you remove the .br part?
                // mic 20081013: because they use now a capture code for accessing
                /*
                else if ( array_key_exists("nserver", $whois) strstr($line, "registro.br") !== false && $getCountry)
                {
                	registro.br does neither include a country code nor a name
                    server entry for some entries, so find these entries here

                  	$countryCode = "br";
                }
                */
            }
            $result = $resultList[0];
        }

        return $countryCode;
    }

    /**
	 * @static Because of PHP 4.0 this is not explicit defined as static
     *
     * @param string  $tld_str  eg.: "localhost"; "us", "de"
	 *
	 * @return object of class js_Tld or null when fail
	 */
	function getTldFromTld( $tld_str ) {

        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
        $db = $JSDatabaseAccess->db;

		$query = ''
		. ' SELECT'
		. '   t.code       ,'
		. '   t.country'
		. ' FROM'
		. '   #__ip2nationCountries t'
		. ' WHERE'
		. '   t.code=\''.$db->getEscaped($tld_str).'\''
		;
		$db->setQuery( $query );
		$obj = $db->loadObject();
		if ($db->getErrorNum() > 0)
			return null;

		if (!$obj)
			return null;

		$Tld = new js_Tld();
		//$Tld->tld_id = $obj->tld_id;
		$Tld->tld = $obj->code;
		$Tld->tld_name = $obj->country;
		$Tld->tld_img = $obj->code;

		return $Tld;
	}
    /**
     * @static Because of PHP 4.0 this is not explicit defined as static
     *
     * @param array $rows
     * @return void
     */
	function CheckIPAddresses(&$rows, $forceCheck = false)
    {
        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
        if(empty($rows))
        {
            return ;
          /*$db = $JSDatabaseAccess->db;

		  $query  = 'SELECT * FROM #__jstats_ipaddresses AS a WHERE a.code IS NULL or a.nslookup IS NULL';
		  $db->setQuery( $query );
		  $myrows = $db->loadObjectList();
          if(count($myrows) > 0 )
          {
            IPInfoHelper::CheckIPAddresses($myrows, true);
          }            */
        }
        else
        {
            $index = -1;
            foreach($rows as $row)
            {
               $index = $index +1;
               if($forceCheck || ( empty($row->code) && $row->ip != 2130706433) )
               {
                   js_echoJSDebugInfo('Before update ', $row);  
                  $Location = new js_Location();
                  $Location->init($row);

                  $success = IPInfoHelper::performLocationCheck( $Location, $row->ip , true);

                  if( $success )
                  {
                      $row->tld = $Location->Tld->tld;
                      $row->nslookup = $Location->nslookup;
                      $row->city = $Location->city;
                      $row->code = $Location->code;
                      if(empty($Location->nslookup))
                      {
                          $Location->nslookup = long2ip($Location->ip);
                      }
                      $rows[$index] = $row;
                      IPInfoHelper::updateLocation($Location);
                      js_echoJSDebugInfo('Location updated ', $row);  
                  }
               }
               if(!$JSDatabaseAccess->executionTimeAvailable() )
               {
                  break; //We avoid further whois-checks, if we run out of time
               }
            }
        }
    }

    /**
	 * @static Because of PHP 4.0 this is not explicit defined as static
     *
     * Update Visitor to #__jstats_ipaddresses table
	 *
	 * //@todo: Is it a mistake that we need to update prevoiusly entered entry? (I am unsure but maybe this this is mistake in logic)
	 *
	 * @param object $Visitor - object of class $js_Visitor
	 * @return bool - true on success
	 */
	function updateLocation( &$address ) {
        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
        $db = $JSDatabaseAccess->db;

        if($address && !empty($address->ip))
        {
            js_echoJSDebugInfo('Update IP-Address Entry '.$address->ip, '');

            $query = 'UPDATE #__jstats_ipaddresses'
            . ' SET ip = \'' . $address->ip . '\'';

            //@todo city not yet fully implemented
                /*
                if( !empty($Visitor->city) )
                {
                  $query .= ', city = \'' . $Visitor->city . '\'';
                }*/
            if( !is_null($address->nslookup))
            {
                //Do not store the nslookup, if it is just the IP address
                if( $address->nslookup == long2ip($address->ip) )
                {
                    $query .= ', nslookup = \'\'';
                }
                else
                {
                    $query .= ', nslookup = \'' . $db->getEscaped($address->nslookup) . '\'';
                }
            }
            if( !is_null($address->ip_type) )
            {
              $query .= ', ip_type = \'' . $address->ip_type . '\'';
            }
            if( !is_null($address->ip_exclude) )
            {
              $query .= ', ip_exclude = \'' . $address->ip_exclude . '\'';
            }
            if( !empty($address->code) )
            {
              $query .= ', code = \'' . $db->getEscaped($address->code) . '\'';
            }
            $query .= ' WHERE ip = \''. $address->ip . '\'';

            $db->setQuery( $query );
            $db->query();
            if ($db->getErrorNum() > 0)
            {
                $msg = $JSDatabaseAccess->db->getErrorMsg();
                JError::raiseNotice( 0, $msg );
                return false;
            }
        }

		return true;
	}

    /**
	 * @static Because of PHP 4.0 this is not explicit defined as static
     *
     * Update Client to #__jstats_clients table (Visitor)
	 *
	 * @param object $Visitor - object of class $js_Visitor
	 * @return bool - true on success
	 */
	function &updateClient( &$client, $byUseragentStr = false ) {
        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
        $db = $JSDatabaseAccess->db;

        if(!empty($client->client_id))
        {
            js_echoJSDebugInfo('Update Client '.$client->client_id, '');
            $query = 'UPDATE #__jstats_clients AS c';

            if($byUseragentStr)
            {
                if( empty($client->useragent) )
                {
                    $query .= ", #__jstats_clients AS c2 ";
                    $query .= ' SET c.useragent = c2.useragent ';
                }
                else
                {
                    $query .= ' SET c.useragent = c2 \'' . $db->getEscaped($client->useragent) . '\'';
                }
            }
            else
            {
                $query .= ' SET c.client_id = \'' . $client->client_id . '\'';
                if( !empty($client->useragent) )
                {
                  $query .= ', c.useragent = \'' . $db->getEscaped($client->useragent) . '\'';
                }
            }
            if( $client->OS->os_id != null )
            {
              $query .= ', c.os_id = ' . $client->OS->os_id . '';
            }
            if( $client->Browser->browser_id != null )
            {
              $query .= ', c.browser_id = ' . $client->Browser->browser_id . '';
            }
            if( !empty($client->client_type) )
            {
              $query .= ', c.client_type = ' . $client->client_type . '';
            }
            if( !empty($client->client_exclude) )
            {
              $query .= ', c.client_exclude = \'' . $client->client_exclude . '\'';
            }
            if( !empty($client->visitor_id) )
            {
              $query .= ', c.visitor_id = ' . $client->visitor_id . '';
            }
            if( !empty($client->browser_version) )
            {
                if($client->browser_version == 'NULL')
                {
                    $query .= ', c.browser_version = NULL';
                }
                else
                {
                    $query .= ', c.browser_version = \'' . $db->getEscaped($client->browser_version) . '\'';
                }
            }
            if($byUseragentStr)
            {
                if( empty($client->useragent) )
                {
                    $query .= ' WHERE c.useragent = c2.useragent AND c2.client_id = \''. $client->client_id . '\'';
                }
                else
                {
                    $query .= ' WHERE c.useragent LIKE \''. $db->getEscaped($client->visitor_useragent) . '\'';
                }
            }
            else
            {
                $query .= ' WHERE c.client_id = \''. $client->client_id . '\'';
            }
            
            $db->setQuery( $query );
            $db->query();
            if ($db->getErrorNum() > 0)
            {
                $msg = $JSDatabaseAccess->db->getErrorMsg();
                JError::raiseNotice( 0, $msg );
                return false;
            }
        }
		return true;
	}

    	/**
	 *
	 *  @param $VisitorIp - valid only when true is returned
	 *  @return true on success
	 */
	function getVisitorIp()
	{
		$Ip_tmp = null;
		// get usefull vars:
		$client_ip       = isset($_SERVER['HTTP_CLIENT_IP'])       ? $_SERVER['HTTP_CLIENT_IP']	      : NULL;
		$x_forwarded_for = isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : NULL;
		$remote_addr     = isset($_SERVER['REMOTE_ADDR'])          ? $_SERVER['REMOTE_ADDR']	      : NULL;

        //is IPv6
        if(strrpos($remote_addr, ':') >= 0)
        {
            if($remote_addr == "::1")
            {
               $remote_addr =  2130706433;
            }
        }

		// then the script itself
		if (!empty($x_forwarded_for) && strrpos($x_forwarded_for, '.') > 0)
		{
			$arr = explode(',', $x_forwarded_for);
			$Ip_tmp = trim(end($arr));
		}

		if (!IPInfoHelper::isIpAddressValidRfc3330($Ip_tmp) && !empty($client_ip))
		{
			$ip_expl = explode('.', $client_ip);
			$referer = explode('.', $remote_addr);

			if ($referer[0] != $ip_expl[0])
			{
				$Ip_tmp = trim(implode('.', array_reverse($ip_expl)));
			}
			else
			{
				$arr = explode(',', $client_ip);
				$Ip_tmp = trim(end($arr));
			}
		}

		if (!IPInfoHelper::isIpAddressValidRfc3330($Ip_tmp) && !empty($remote_addr))
		{
			$arr = explode(',', $remote_addr);
			$Ip_tmp = trim(end($arr));
		}

		unset($client_ip, $x_forwarded_for, $remote_addr, $ip_expl, $referer);

		$VisitorIp = $Ip_tmp;

        if($VisitorIp == 1)
        {
           $VisitorIp = "0.0.0.0";
        }

		return $VisitorIp;
	}
}
