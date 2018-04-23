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


require_once( dirname(__FILE__) .DS.'..'.DS. 'libraries'.DS. 'base.classes.php' );
require_once( dirname(__FILE__) .DS.'..'.DS. 'libraries'.DS. 'template.html.php' );



/**
 * Object of hold templates to 'tools options'
 */
class js_JSToolsTpl
{

	/**
	 * This template is used when unistallation process fail
	 * or when unistallation was OK but user must manualy uninstall JS in standard Joomla uninstaller
	 * (2008-09-07 Curently it works in this way)
	 *
	 * @param string $errorMsg
	 * @param string $noErrorMsgText
	 * @param string $warningMsg
	 * @param string $noWarningMsgText
	 * @param string $recommendationMsg
	 * @param string $noRecommendationMsgText
	 */
	function doJSUninstallFailTpl( $errorMsg, $noErrorMsgText, $warningMsg, $noWarningMsgText, $recommendationMsg, $noRecommendationMsgText ) {

		$JSTemplate = new js_JSTemplate();

		echo '<div style="text-align: left;"><!-- needed by j1.0.15 -->';

		if ( $noErrorMsgText != '' ) {
			echo $JSTemplate->generateMsgColorInfoFrame( 'error', $errorMsg, $noErrorMsgText );
		}

		if( $noWarningMsgText != '' ) {
			echo $JSTemplate->generateMsgColorInfoFrame( 'warning', $warningMsg, $noWarningMsgText );
		}

		if( $noRecommendationMsgText != '' ) {
			echo $JSTemplate->generateMsgColorInfoFrame( 'recommend', $recommendationMsg, $noRecommendationMsgText );
		}

		echo $JSTemplate->generateAdminForm();
		echo '</div><!-- needed by j1.0.15 -->';
	}

}