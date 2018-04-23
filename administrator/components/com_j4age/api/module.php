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

require_once( dirname(__FILE__) .DS. '..' .DS. 'libraries' .DS. 'util.classes.php' );
require_once( dirname(__FILE__) .DS. '..' .DS. 'database' .DS. 'db.constants.php');


/**
 *  This class contain API (application programming interface) to JoomlaStats specially for modules.
 *
 *  Eg. of including JoomlaStats API
 *       require_once( dirname(__FILE__) .DIRECTORY_SEPARATOR. 'joomla' .DIRECTORY_SEPARATOR. 'administrator' .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_j4age' .DIRECTORY_SEPARATOR. 'api' .DIRECTORY_SEPARATOR. 'module.php' );
 *
 *
 *  All methods are static
 */
class js_JSApiModule
    {
    /**
     *  This function convert time period string from *.xml to SQL WHERE condition (in optimized way)
     *
     *  NOTICE:
     *    Function is complicated because SQL query is optimized!!
     *
     */
    function getConditionStringFromXmlTimePeriodList( $list_time_period ) {

        if ($list_time_period == 'total')
            return '1=1';
            //total

        $sql_constr_time = '1=1';
            //total and wrong value

        $timestamp_to = null;
        $timestamp_from = null;
        js_JSUtil::resolvePeriodIndicator($list_time_period, $timestamp_from, $timestamp_to, $d = null, $m = null, $y = null, $step = null);

        $sql_constr_time = '(v.changed_at >= '.$timestamp_from.' AND v.changed_at <= '.$timestamp_to.')';

        return $sql_constr_time;
    }


    /**
     *  This function transform user provided translation string to associative array
     *    that can be easy used to translate
     *
     *
     *  Example of using:
     *        <param name="tld_translation_tool"      type="textarea"   default=""                     label="Country translation tool" description="Enter translation here for 'country names'. Country names will be replaced by texts defined here.&lt;br/>&lt;br/>You can also use 'Translation tool' to provide shortcuted country name: 'United States of America' => 'USA'&lt;br/>&lt;br/>eg.&lt;br/>&lt;b>de=Deutschland; nl=Niederlande; us=Vereinigte Staaten&lt;/b>&lt;br/>'Germany' will be replaced by 'Deutschland', 'Netherlands' by 'Niederlande' and 'United States' by 'Vereinigte Staaten'" rows="3" cols="35" />
     *
     *        $tld_translation_arr     = create_translation_arr($tld_translation_tool);
     *        $tld_name = $Visitor->Tld->tld_name; //oryginal name
     *        if ( isset($tld_translation_arr[$Visitor->Tld->tld]) )
     *            $tld_name = $tld_translation_arr[$Visitor->Tld->tld]; //replace by translated name
     *
     *
     *  @param string in  $translation_tool_str     eg.: "de=Deutschland; nl=Niederlande; us=Vereinigte Staaten" (semicolon is separator (not space))
     *  @return array                                eg.: array( 'de'=>'Deutschland', 'nl'=>'Niederlande', 'us'='Vereinigte Staaten')
     *
     *  @since: v3.0.1.446
     */
    function create_translation_arr($translation_tool_str) {
        if (strlen($translation_tool_str) == 0)
            return array();

        $translation_arr = array();
        $trans_arr = explode(';', $translation_tool_str);
        foreach ($trans_arr as $trans) {
            $var_val_arr = explode('=', $trans);
            if ( !isset($var_val_arr[0]) || !isset($var_val_arr[1]) )
                continue;
            $var = trim($var_val_arr[0]);
            $val = trim($var_val_arr[1]);
            if ( (strlen($var_val_arr[0]) == 0) || (strlen($var_val_arr[1]) == 0) )
                continue;
            $translation_arr[$var] = $val;
        }

        return $translation_arr;
    }


    //this is copy of function from base.classes.php file
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
            $mainframe = JFactory::getApplication();

            $TZOffset = $mainframe->getCfg( 'offset' );

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
            require_once( dirname(__FILE__) .DIRECTORY_SEPARATOR. 'database' .DIRECTORY_SEPARATOR. 'stand.alone.configuration.php' );
            $JSStandAloneConfiguration = new js_JSStandAloneConfiguration();
            $TZOffset = $JSStandAloneConfiguration->JConfigArr['offset'];
        }

        return $TZOffset;
    }

    //this is copy of function from base.classes.php file
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


    //this is copy of function from base.classes.php file
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

}

/**
 * This class is supposed to be used by any kind of JS module, to give us a central control for all modules
 *
 * This class represents the interface between the JS component and any kind of module.
 * Whenever we require something, that depends on the JS component, we should try to find a way to pass it thru that module
 */
class js_Module extends js_JSApiModule
{
    var $name = null;
    var $params = null;

    function __construct( $module_name, $params = array()  ) {
        $this->name = $module_name;
        $this->params = $params;
    }

    /**
     * Do not overwrite!!
     *
     * @return void
     */
    function execute()
    {
       js_profilerMarker('Execute Module '.$this->name. ' - Start');
       $this->init();
       $this->load();
       js_profilerMarker('Execute Module '.$this->name. ' - End');
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
    function load()
    {
       $this->display();
    }

    function display($tmpl = 'default')
    {
       $this->render($tmpl);
    }

    /**
     * Called by your logic to render
     *
     * @param string $template
     * @return void
     */
    function render($template = 'default')
    {
        $layout = JModuleHelper::getLayoutPath($this->name,$template);
        require( $layout );
    }
}
