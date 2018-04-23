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
require_once( dirname(__FILE__) .DS.'..'.DS. 'libraries'.DS. 'filters.php' );
require_once( dirname(__FILE__) .DS.'..'.DS. 'libraries'.DS. 'util.classes.php' );
require_once( dirname(__FILE__) .DS.'..'.DS. 'api' .DS. 'tools.html.php' );
require_once( dirname(__FILE__) .DS.'..'.DS. 'database' .DS. 'select.one.value.php' );


/**
 *  Object of this group all functionality of JS Maintenance tab
 *
 *  NOTICE: This class should contain only set of static, argument less functions that are called by task/action
 */
class js_JSTools
{
	/** function from j1.5.6 from file 'j1.5.6\libraries\joomla\application\application.php' from class 'JApplication' */
	/** @access private */
	/** this function send token by SENT method */
	/** @bug - this function not working!! //@At need internet access to check HTML specification how to do it :( */
	function _redirect( $url, $params, $msg='',  $msgType='message' )
	{
        global $js_controller;
        $token = JUtility::getToken();
        //header( "POSTDATA: $token=1" );

        ?>

        <form name="redirectFirm" id="redirectFirm" method="post" action="index.php" style="display: inline; margin: 0px; padding: 0px;" onsubmit="return true;">
               <?php
                 echo JHTML::_( 'form.token' );
                 foreach($params as $key=>$value)
                 {
                     echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
                 }
                ;?>
        </form>
          <script>
                 document.redirectFirm.submit();
          </script>
        <?php

        //$js_controller->setRedirect( $url, $msg, $msgType );
        // check for relative internal links
		/*if (preg_match( '#^index[2]?.php#', $url )) {
			$url = JURI::base() . $url;
		}

		// Strip out any line breaks
		$url = preg_split("/[\r\n]/", $url);
		$url = $url[0];

        //
		// If the headers have been sent, then we cannot send an additional location header
		// so we will output a javascript redirect statement.
		//
		if (headers_sent()) {
			echo "<script>document.location.href='$url';</script>\n";
		} else {
			//@ob_end_clean(); // clear output buffer
			header( 'HTTP/1.1 301 Moved Permanently' );
			header( 'Location: ' . $url );
		}
		$this->close();*/
	}

	/**
	* Exit the application.
	* borrought from application.php J.1.5.x
	*
	* @access	public
	* @param	int	Exit code
	*/
	function close( $code = 0 ) {
		exit( $code );
	}

	/** @access private */
	function _returnBytes($val)
	{
		$val = trim($val);
		$last = strtolower($val{strlen($val)-1});

		switch($last)
		{
			// The 'G' modifier is available since PHP 5.1.0
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}
		return $val;
	}


	/**
	 * Uninstall JoomlaStats Database (only)
     *
     * @todo Move logic to controller "maintenance"
	 *
	 * @return bool
	 */
	function doJSUninstall() {
		global $database;

		require_once( dirname(__FILE__) .DS. '..'.DS.'database' .DS. 'access.php' );
		$JSDatabaseAccess = js_JSDatabaseAccess::getInstance();

		$errors = array();

        js_PluginManager::fireEvent('doUninstall');

		//remove all JS tables from database
		$JSSystemConst = new js_JSSystemConst();

        $query = "SHOW TABLES LIKE '%_jstats_%'";
        $JSDatabaseAccess->db->setQuery( $query );
        $columns = $JSDatabaseAccess->db->loadRowList();
        if ($JSDatabaseAccess->db->getErrorNum() > 0)
        {
            js_echoJSDebugInfo("".$JSDatabaseAccess->db->getErrorMsg());
            return false;
        }
        foreach ($columns as $column)
        {
            $JSDatabaseAccess->db->setQuery("DROP TABLES $column[0]");
            $JSDatabaseAccess->db->query();
            if ($JSDatabaseAccess->db->getErrorNum() > 0)
                $errors[] = $JSDatabaseAccess->db->getErrorMsg();
        }

		foreach( $JSSystemConst->allJSDatabaseTables as $db_table_name) {
			$JSDatabaseAccess->db->setQuery('DROP TABLE IF EXISTS `'.$db_table_name.'`');
			$JSDatabaseAccess->db->query();
			if ($JSDatabaseAccess->db->getErrorNum() > 0)
				$errors[] = $JSDatabaseAccess->db->getErrorMsg();
		}

		//common text for 2 cases
		$recommendationTextFinishUninstallationArr = array(
			'name'			=> JTEXT::_( 'Finish Uninstallation' ),
			'description'	=> JTEXT::_( 'To finish the uninstallation process use the standard CMS uninstalling method' )
		);

		if( count($errors) == count($JSSystemConst->allJSDatabaseTables) ) { //probalby user already uninstall database
			$noErrorMsgText				= ''; //this hide ColorInfoFrame
			$noWarningMsgText			= 'js_text_23432'; //this show ColorInfoFrame //this text will newer appear
			$noRecommendationMsgText	= 'js_text_23432'; //this show ColorInfoFrame //this text will newer appear

			$errorMsg					= array();
			$warningMsg					= array();
			$recommendationMsg			= array();

			$warningMsg[] = array(
				'name'			=> JTEXT::_( 'Probably JoomlaStats database already removed' ),
				'description'	=> JTEXT::_( 'It seems that you have already uninstalled the JoomlaStats database' )
			);
			$recommendationMsg[] = $recommendationTextFinishUninstallationArr;

			$JSToolsTpl = new js_JSToolsTpl();
			$JSToolsTpl->doJSUninstallFailTpl( $errorMsg, $noErrorMsgText, $warningMsg, $noWarningMsgText, $recommendationMsg, $noRecommendationMsgText );

			return false;
		}

		if ( count( $errors ) > 0 ) {
			$noErrorMsgText				= 'js_text_23432'; //this show ColorInfoFrame //this text will newer appear
			$noWarningMsgText			= ''; //this hide ColorInfoFrame
			$noRecommendationMsgText	= 'js_text_23432'; //this show ColorInfoFrame //this text will newer appear

			$db_errors_html  = JTEXT::_( 'List of errors:' ) . '<br/>';
			$db_errors_html .= implode( '<br/>', $errors );

			$errorMsg = array();
			$warningMsg = array();
			$recommendationMsg = array();

			$errorMsg[] = array(
				'name'			=> JTEXT::_( 'JoomlaStats database uninstall failed!' ),
				'description'	=> $db_errors_html
			);
			$recommendationMsg[] = array(
				'name'			=> JTEXT::_( 'Database Checkup' ),
				'description'	=> JTEXT::_( 'Serious errors occured' )
			);
			$recommendationMsg[] = array(
				'name'			=> JTEXT::_( 'Report' ),
				'description' 	=> JTEXT::_( 'Please report errors to JoomlaStats project website!' )
			);

			$JSToolsTpl = new js_JSToolsTpl();
			$JSToolsTpl->doJSUninstallFailTpl( $errorMsg, $noErrorMsgText, $warningMsg, $noWarningMsgText, $recommendationMsg, $noRecommendationMsgText );

			return false;
		}

		$bug_manualUninstal	= false; //this variable should be removed (first fix $this->_redirect() method)
		$JSComponentId		= -1;
        $params = array();
        $params['option'] = 'com_installer';
        $params['task'] = 'remove';
        $params['boxchecked'] = '1';

		//get JS component Id
		// @At something is wrong with word 'option' I must add 'c.' alias
         jimport('joomla.version');
         $version = new JVersion();
         $query = null;
         if( $version->isCompatible("1.6.0") )
         {
             $query = "SELECT extension_id as id FROM #__extensions AS c  WHERE `type` LIKE 'component' AND c.element = 'com_j4age'";
             $params['type'] = 'extensions';
         }
         else
         {
            $query = 'SELECT id FROM #__components AS c WHERE c.option = \'com_j4age\' and c.parent=0';
            $params['type'] = 'components';
         }

		;
		$JSDatabaseAccess->db->setQuery( $query );
		$rowList = $JSDatabaseAccess->db->loadAssocList();

		//if 2 or 0 components than error
		if( ( !$JSDatabaseAccess->db->query() ) || ( count( $rowList ) != 1 ) || $bug_manualUninstal ) {
			// this show ColorInfoFrame
			$noErrorMsgText				= JTEXT::_( 'No errors occured during JoomlaStats database uninstallation process.' );
			$noWarningMsgText			= ''; //this hide ColorInfoFrame
			$noRecommendationMsgText	= 'js_text_23432'; //this show ColorInfoFrame //this text will newer appear

			$errorMsg					= array();
			$warningMsg					= array();
			$recommendationMsg			= array();

			$recommendationMsg[]		= $recommendationTextFinishUninstallationArr;

			$JSToolsTpl = new js_JSToolsTpl();
			$JSToolsTpl->doJSUninstallFailTpl( $errorMsg, $noErrorMsgText, $warningMsg, $noWarningMsgText, $recommendationMsg, $noRecommendationMsgText );

			return false;
		}
		$JSComponentId = $rowList[0]['id'];

        $params['eid'] = $JSComponentId;

		//prepare link to uninstall component from joomla
		$urlToUninstallJS = '';
		//$token = JHTML::_( 'form.token' );
		//$urlToUninstallJS = 'index2.php?option=com_installer&type=components&task=remove&eid='.$JSComponentId.'&'.$token.'=1&boxchecked=1';//token not working when it is in GET method
		//index.php?option=com_installer&type=components&task=manage

        $params = array();
        $params['option'] = 'com_installer';
        $params['type'] = 'components';
        $params['task'] = 'remove';
        $params['eid'] = $JSComponentId;
        $params['boxchecked'] = '1';

		//after uninstall database go to joomla uninstall component menu
		// http://127.0.0.1/j156/administrator/index.php?option=com_installer&type=components&task=manage
		//$mainframe->redirect( 'index.php?option=com_installer&type=components&task=manage' );//third argument: 'message', 'notice', 'error'


		//try to uninstall from joomla
		//@at we can not do so simply :( (error 'Invalid Token' is returnded when try to execute below statement
		//Joomla checks is token was send by _POST method
		//$mainframe->redirect( 'index.php?option=com_installer&type=components&task=remove&eid='.$JSComponentId.'&'.$token.'=1&boxchecked=1' );//third argument: 'message', 'notice', 'error'

		$this->_redirect( $urlToUninstallJS,$params, 'j4age tables dropped' );

		return true;
	}
}