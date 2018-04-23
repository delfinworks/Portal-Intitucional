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



/**
 *
 *  This file include all files with JoomlaStats API (application programming interface).
 *
 *
 *  Below is example of including JoomlaStats API (just copy below code to Your module)


	{//include JoomlaStats API
		$PathToJoomlaStatsApi = dirname(__FILE__) .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'administrator' .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_j4age' .DIRECTORY_SEPARATOR. 'api' .DIRECTORY_SEPARATOR. 'api.include.php';
		if (defined( '_JEXEC' )) //in joomla CMS v1.5.x all modules have own direcotory - path need to be a little longer
			$PathToJoomlaStatsApi = dirname(__FILE__) .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'administrator' .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_j4age' .DIRECTORY_SEPARATOR. 'api' .DIRECTORY_SEPARATOR. 'api.include.php';
			
			
		if ( file_exists($PathToJoomlaStatsApi) ) {
			require_once($PathToJoomlaStatsApi);
		} else {
			echo 'File with JoomlaStats API not found. Did You install JoomlaStats engine (eg.: com_j4age.3.0.zip)?';
		}
	}	


 */


require_once( dirname(__FILE__) .DIRECTORY_SEPARATOR. 'general.php' );



