<?php
if( !defined( '_JS_STAND_ALONE' ) && !defined( '_JEXEC' ) ) {
	die( 'JS: No Direct Access to '.__FILE__ );
}



require_once( dirname(__FILE__) .DS.'..'.DS.'libraries'.DS.'util.classes.php' );

class extComponentip2nation extends ComponentExtension
{
    var $BuildVersion = "1.0.3";

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
        return array('onPostInstallation', 'resolveLocation');
    }

    function isProtected()
    {
        return true;
    }

    function init()
    {
       $JSConf =& js_JSConf::getInstance();

       if($this->config->active == '1')
       {
           $db_installed = $JSConf->getParam('ip2nation_db_installed');
           if(empty($db_installed) || (js_JSUtil::JSVersionCompare( $db_installed, $this->BuildVersion, '<') == true))
           {
              $JSConf->show_installer = true;
           }
       }
    }

    function resolveLocation(&$location, $option = array())
    {
        if($this->config->active == '1')
        {     
           $country_code = null;
           if( $this->getCountryCodeFromJSDatabase($location->ip, $country_code) )
           {
               $location->code = $country_code;
               $location->Tld = $this->getCountryFromCode( $country_code );
           }

           $IpAddressStr = long2ip($location->ip);

           /**
            * This is the slowest part to retrieve the nslookup string!!!
            *
            * It takes up to 5 seconds!!!
            */
           $visitor_nslookup = js_gethostbyaddr( $IpAddressStr );

           if($visitor_nslookup != $IpAddressStr)
           {
              $location->nslookup = $visitor_nslookup;
           }
           if($location->Tld == null)
           {
             $location->Tld = js_Tld::getDefault();;
           }
       }
        else
        {
            js_echoJSDebugInfo("ip2nation extension switched off", $this);
        }
    }

    function onPostInstallation(&$installer, $option = array())
    {
        $JSConf =& js_JSConf::getInstance();

        $db_installed = $JSConf->getParam('ip2nation_db_installed');
        if(empty($db_installed) || (js_JSUtil::JSVersionCompare( $db_installed, $this->BuildVersion, '<') == true))
        {
            require_once( dirname(__FILE__)  .DS. 'ip2nation' .DS. 'data.ip2nation.php' );
            js_retrieveIP2NationInstallSteps($installer, $this, $JSConf->getParam('ip2nation_db_installed'));
        }
    }



        /**
	 * @static Because of PHP 4.0 this is not explicit defined as static
     *
     * @param string  $tld_str  eg.: "localhost"; "us", "de"
	 *
	 * @return object of class js_Tld or null when fail
	 */
	function getCountryFromCode( $code_str ) {

        if(empty($code_str))
        {
           return null;
        }

        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
        $db = $JSDatabaseAccess->db;

		$query = ''
		. ' SELECT'
		. '   t.code       ,'
		. '   t.country'
		. ' FROM'
		. '   #__ip2nationCountries t'
		. ' WHERE'
		. '   t.code=\''.$code_str.'\''
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

    function getCountryDetails( $code, &$countryDetails )
    {
        if($this->config->active == '1')
        {
            global $js_country_details;

            if($js_country_details == null)
            {
               $js_country_details = array();
            }

            $countryDetails = $js_country_details[$code];

            if(!empty($countryDetails))
            {
                return $countryDetails;
            }

            js_echoJSDebugInfo("getCountryDetails start");

            $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
            $db = $JSDatabaseAccess->db;

            $query = "SELECT code, code as tld, country as fullname FROM
                        #__ip2nationCountries AS i2nc
                      WHERE
                        i2nc.code like $code
                     ";

            $db->setQuery( $query );
            $countryDetails = $db->loadObject();

            js_echoJSDebugInfo("getCountryDetails stop");

            if ($db->getErrorNum() > 0)
                return $countryDetails;

            $js_country_details[$code] = $countryDetails;
            return $countryDetails;
        }
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
    function getCountryCodeFromJSDatabase( $visitor_ip, &$country_code ) {


        if(empty($visitor_ip))
        {
           $country_code = null;
           return true;
        }

        /**
         * 127.0.0.1 = 2130706433
         * => not handled within ip2nation
         */
        if($visitor_ip == 2130706433)
        {
           $country_code = null;
           return true;
        }
        js_echoJSDebugInfo("getCountryCodeFromJSDatabase start");

        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
        $db = $JSDatabaseAccess->db;

        $query = "SELECT  i2n.country FROM
	                #__ip2nation i2n
	              WHERE
                    i2n.ip < $visitor_ip
                  ORDER BY
                    i2n.ip DESC
                  LIMIT 0,1";
                
        $db->setQuery( $query );
        $tmp_country_code = $db->loadResult();

        js_echoJSDebugInfo("getCountryCodeFromJSDatabase stop");

        if ($db->getErrorNum() > 0)
            return false;

        if( $tmp_country_code ) {
            $country_code = $tmp_country_code;
            return true;
        }

        return false;
    }
}