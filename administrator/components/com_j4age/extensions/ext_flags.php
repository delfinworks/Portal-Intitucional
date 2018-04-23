<?php
if( !defined( '_JS_STAND_ALONE' ) && !defined( '_JEXEC' ) ) {
	die( 'JS: No Direct Access to '.__FILE__ );
}

class extComponentflags extends ComponentExtension
{
    var $BuildVersion = "1.0.0";

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
        return array('beforeLoad');
    }

    function init()
    {
    }

    function beforeload()
    {
        $JSConf =& js_JSConf::getInstance();
        $JSConf->show_icons = true;
    }

}