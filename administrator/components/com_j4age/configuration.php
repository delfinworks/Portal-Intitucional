<?php

              
/**
 * @package j4age
 * @copyright Copyright (C) 2009-@THISYEAR@ j4age Team. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * Thank you to the project j4age and it's team, on which roots this project is build on.
 */
             
          

/**
 * THis file is not mixed with others as it contains dynamic elements, which would prevent to just drag and drop fixes 
 */
if( !defined( '_JS_STAND_ALONE' ) && !defined( '_JEXEC' ) ) {
	die( 'JS: No Direct Access to '.__FILE__ );
}

/**
 * 'Joomla Stats' class that contain DEFAULT 'Joomla Stats' configuration
 *
 * All members are READ ONLY!
 */
class js_JSConfDef
{
	/** constructor do nothing. Only for PHP4.0 */
	function __construct() {
        //$this->JSVersion = $this->BuildVersion;
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
	function js_JSConfDef()
	{
		$args = func_get_args();
		call_user_func_array(array(&$this, '__construct'), $args);
        //$this->JSVersion = $this->BuildVersion;
	}


	/**
	 *	Members initialization values are system default values!
	 */

	/**
	 * this constant was hold by define('_JoomlaStats_V','2.3.0_dev2008-08-12'); in previous releases version of script
	 * this member is not stored to database by function storeConfigurationToDatabase() (security)
	 * version x.y.w.z  z - is SVN version
	 *
	 * NOTICE:
	 *   - Always should be 4 nuber sections!!! - see method JSVersionCompare(...)
	 *   - space is separation character to. Space differ development and release versions!!!
	 *
	 * eg.: '2.3.0.151 dev' - for development snapshot
	 * eg.: '2.3.0.194'     - for release
	 *
	 */
    var $BuildVersion = '4.0.2.1 RC';// Keept it always one step higher as it is listed in the Migration Script!!

	var $JSVersion = null;// eg '2.3.0.151 dev'

	/** time online in [minutes] before regular visitor is counted within a new visit */
	var $onlinetime = 240;

    /** time online in [minutes] before bot visit is counted as new visit */
    var $onlinetime_bots = 480;

	/** option for starting statistics */
	var $startoption = 'visitors';

	/** option for selecting 1 day or whole month at JoomlaStats start */
	var $startdayormonth = 'd';

	/** show statistics including summarized/purged data */
	var $include_summarized = true;

	/** show statistics with summarized/purged data in brackets [23244] //$show_summarized HAVE TO be set to false if $include_summarized = false */
	var $show_summarized = true;

	/** enable Whois queries */
	var $enable_whois = true;

	/** enable Joom!Fish i18n support */
	var $enable_i18n = true;

    /** This is an indicator for the installer controller to proceed with the concurrent installation process */
    var $current_step = 0;

    var $update_static_data = 1;

    var $show_charts_within_reports = true;

    var $component = 'joomlastats';
    var $show_icons = false;
    var $language = null;
}