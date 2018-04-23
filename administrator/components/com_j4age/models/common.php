<?php

              
/**
 * @package j4age
 * @copyright Copyright (C) 2009-@THISYEAR@ j4age Team. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * Thank you to the project j4age and it's team, on which roots this project is build on.
 */
             
          

// Check to ensure this file is included in Joomla!
if( !defined( '_JS_STAND_ALONE' ) && !defined( '_JEXEC' ) )
{
	die( 'JS: No Direct Access to '.__FILE__ );
}


jimport('joomla.application.component.model');

/**
 * Weblinks Component Weblink Model
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class j4ageModelCommon extends JModel
{
    var $rows = array();

    function getJoomlaConfig()
    {
        global $jconfig;
        if(!$jconfig)
        {
           $jconfig = new JConfig;;
        }
        return $jconfig;
    }

    function getConfiguration($options = array())
	{
        global $configs;
        $mainframe = JFactory::getApplication();

        if(!$configs)
        {
           $configs = array();
        }
	    $component = $mainframe->scope;
        if(isset($configs[$component]))
        {
            $config =& $configs[$component];
            if($config != null)
            {
               return $config;
            }
        }
        //Ensure that we do not get a loop
        $configs[$component] = new JParameter('');

        $table =& JTable::getInstance('component');
        $table->loadByOption( $component );

        // work out file path
        if ($path = JRequest::getString( 'path' )) {
            $path = JPath::clean( JPATH_SITE.DS.$path );
            JPath::check( $path );
        } else {
            $option	= preg_replace( '#\W#', '', $table->option );
            $path	= JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'config.xml';
        }

        if (file_exists( $path )) {
            $instance = new JParameter( $table->params, $path );
        } else {
            $instance = new JParameter($table->params );
        }
        //Ensure default value
        $definition = DBHelperClass::renderToArray($instance);
        if($definition)
        {
            foreach($definition as $k => $v)
            {                  
               $instance->def($k,$v);
            }
        }
        $configs[$component] =& $instance;

		return $instance;
	}

    function setRows($rows = array())
    {
       $this->rows = $rows;
    }

    function getRows()
    {
       return $this->rows;
    }
}