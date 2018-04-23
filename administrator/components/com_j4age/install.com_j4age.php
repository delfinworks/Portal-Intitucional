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

require_once( dirname(__FILE__) .DS. 'libraries' .DS. 'base.classes.php' );
require_once( dirname(__FILE__) .DS. 'libraries' .DS. 'util.classes.php' );
require_once( dirname(__FILE__) .DS. 'database' .DS. 'access.php' );
require_once( dirname(__FILE__) .DS. 'controllers' .DS. 'installer.php' );


class JSInstall
{
	var $errors					= array();//removing this is much more complicated than it seams! (notification must be fixed)


	/**
	 * basic function for installing JoomlaStats
	 *
	 */
	function com_install()
    {
        $mainframe = JFactory::getApplication();

		$errorMsg	= array();
		$warningMsg	= array();
		$infoMsg	= array();

		$JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
		$JSUtil = new js_JSUtil();
        $installationErrorMsg = '';

        $query = "SHOW TABLES LIKE '%_components'";
        $isJoomla1_6 = false;

        $JSDatabaseAccess->db->setQuery( $query );
        $tables = $JSDatabaseAccess->db->LoadObjectList();
        if ($JSDatabaseAccess->db->getErrorNum() > 0)
        {
            $installationErrorMsg .= JTEXT::_( 'Some errors occured during the j4age installation process' ) . 'Error: '.$JSDatabaseAccess->db->getErrorMsg();
        }
        if(empty($tables))
        {
            $isJoomla1_6 = true;
        }

        $queryModules = null;
        $queryComponents = null;

        if( $isJoomla1_6 )
        {
            $queryModules = "SELECT extension_id as id FROM #__extensions AS c  WHERE `type` LIKE 'module' AND c.element LIKE '%_jstats_%'";
            $queryComponents = "SELECT extension_id as id FROM #__extensions AS c  WHERE `type` LIKE 'component' AND c.element LIKE '%_joomlastats%'";
        }
        else
        {
            $queryModules = "SELECT * FROM `#__modules` WHERE `module` LIKE '%_jstats_%'";
            $queryComponents = "SELECT * FROM `#__components` WHERE `option` LIKE '%_joomlastats%'";
        }


        $JSModuleRows = array();

        {
          $JSDatabaseAccess->db->setQuery( $queryModules );
          $JSModuleRows = $JSDatabaseAccess->db->LoadObjectList();
          if ($JSDatabaseAccess->db->getErrorNum() > 0)
              $installationErrorMsg .= JTEXT::_( 'Some errors occured during the j4age installation process' ) . 'Error: '.$JSDatabaseAccess->db->getErrorMsg();
        }

        $JSComponents = array();

        {
          $JSDatabaseAccess->db->setQuery( $queryComponents );
          $JSComponents = $JSDatabaseAccess->db->LoadObjectList();
          if ($JSDatabaseAccess->db->getErrorNum() > 0)
              $installationErrorMsg .= JTEXT::_( 'Some errors occured during the j4age installation process' ) . 'Error: '.$JSDatabaseAccess->db->getErrorMsg();
        }


		$isThisUpgrade		= false;
		$oldJSVersionNumber = '';

		{// detect if this is upgrade or install and get old version number
			$query = 'SHOW TABLE STATUS FROM `' . $mainframe->getCfg( 'db' ) . '`'
			. ' LIKE \'' . $mainframe->getCfg( 'dbprefix' ) .'jstats_configuration\''
			;
			$JSDatabaseAccess->db->setQuery( $query );
			$rows = $JSDatabaseAccess->db->LoadObjectList();
			if ($JSDatabaseAccess->db->getErrorNum() > 0)
				$installationErrorMsg .= JTEXT::_( 'Some errors occured during the j4age installation process' ) . 'Error number: #1';

			if ($rows) {
				if (count($rows) == 1) {
					$query = 'SELECT * FROM'
					. ' #__jstats_configuration'
					;
					$JSDatabaseAccess->db->setQuery( $query );
					$rows = $JSDatabaseAccess->db->loadAssocList();
					if ($JSDatabaseAccess->db->getErrorNum() > 0) {
						$installationErrorMsg .= JTEXT::_( 'Some errors occured during the j4age installation process' ) . 'Error number: #4';
					} else {
						if ( (!$rows) || (count($rows) == 0) ) {
							$installationErrorMsg .= JTEXT::_( 'Some errors occured during the j4age installation process' ) . 'Error number: #5';
				        } else {
							$isThisUpgrade = true;
				
							foreach( $rows as $row ) {
								if( $row['description'] == 'version' )
									$oldJSVersionNumber = $row['value'];
							}
						}
					}
				} else {
					$installationErrorMsg .= JTEXT::_( 'Some errors occured during the j4age installation process' ) . 'Error number: #2';
				}
			}
		}


		$setJSVersion = false;


		if( $isThisUpgrade == true) {

            /**
             * The update is performed within our own installer process
             * @author Andreas Halbig
             */
		} else {

            /**
             * The DB JS structure is initial created, but excluding any data
             */
			include_once( dirname( __FILE__ ) .DS. 'install' .DS. 'all.tables.j4age.inc.php' );
	        $setJSVersion = true;
			// populate queries
			$JSDatabaseAccess->populateSQL( $quer, true );
			$quer = null;

		}
        $JSConf = js_JSConf::getInstance();
        if($setJSVersion)
        {
            $JSConf->JSVersion = $JSConf->BuildVersion;
        }
        {
            /**
             * We set an flag for the installer, so that he knows that we have to update or initial fill the static data to the DB
             */
            $JSConf->update_static_data = 1;
            $error_msg = '';
            $JSConf->storeConfigurationToDatabase($error_msg);
        }

		{//here update/modify things outside JS
		
		    // Modify the admin icon because j1.0.x cannot do that through the xml
			/*$query = 'UPDATE #__components'
			. ' SET admin_menu_img = \'../administrator/components/com_j4age/images/logo-icon.png\''
			. ' WHERE link = \'option=com_j4age\''
			;
			$JSDatabaseAccess->db->setQuery( $query );
			$JSDatabaseAccess->db->query(); */
		}
		
		if ($installationErrorMsg != '')
			$errorMsg[] = array( 'name' => JTEXT::_( 'Installation' ), 'description' => $installationErrorMsg );

        if(count($JSComponents) > 0)
        {
            $errorMsg[] = array( 'name' => JTEXT::_( 'Installation' ), 'description' => JTEXT::_( 'JoomlaStats is no more working, please uninstall' ) );
        }
	    // collect warning/recommendation/info messages
		if( count( $this->errors ) == 0 ) {
			//everything is OK
			if( $isThisUpgrade == true ) {
                $infoMsg[] = array( 'name' => JTEXT::_( 'Upgrade' ), 'description' => JTEXT::sprintf( 'j4age code has been successfully upgraded from version [%s] to version [%s]<br />Previously collected statistics are retained!', $JSConf->JSVersion, $JSConf->BuildVersion ) );
                $infoMsg[] = array( 'name' => JTEXT::_( 'Migration' ), 'description' => JTEXT::sprintf( 'The Datebase needs to be migrated. Please open j4age to start and finish the post-installation process' ) );
			} else {
				$infoMsg[] = array( 'name' => JTEXT::_( 'Installation' ), 'description' => JTEXT::_( 'New installation of j4age' ) );
				$infoMsg[] = array( 'name' => JTEXT::_( 'Installation' ), 'description' => JTEXT::_( 'j4age code has been succesfully installed!' ) );
                $infoMsg[] = array( 'name' => JTEXT::_( 'Static Data' ), 'description' => JTEXT::sprintf( 'All static data are getting refreshed. Please open j4age to start and finish the post-installation process' ) );
			}
		}else{
			//something is wrong
			if ( $isThisUpgrade == true ) {
				$installationErrorMsg .= JTEXT::SPRINTF( 'Some errors occured during j4age upgrade process when trying to upgrade from version [%s] to version [%s]', $JSConf->JSVersion, $JSConf->BuildVersion );
			}else{
				$installationErrorMsg .= JTEXT::_( 'Some errors occured during the j4age installation process' ) . 'Error number: #3';
			}

			if( $this->errors ) {
				$installationErrorMsg .= '<br/><ul>';
				foreach ( $this->errors as $err ) {
					$installationErrorMsg .= '<li>' . print_r( $err, true ) . '</li>';
				}
				$installationErrorMsg .= '</ul><br/>';
			}

			$errorMsg[] = array( 'name' => JTEXT::_( 'Installation' ), 'description' => $installationErrorMsg );
		}

        $dbversion = $JSDatabaseAccess->db->getVersion();
        if( js_JSUtil::JSVersionCompare( $dbversion, '4.0.0', '<'))
        {
            $warningMsg[] = array( 'name' => JTEXT::_( 'Datebase Version' ).' '.$dbversion, 'description' => "Not officially Supported." );
        }

        if( js_JSUtil::JSVersionCompare( phpversion(), '5.0.0', '<'))
        {
            $warningMsg[] = array( 'name' => JTEXT::_( 'PHP Version' ).' '.phpversion(), 'description' => "Not officially Supported." );
        }
        /*Error Message - Show error message if j4age modules are installed*/
        if($JSModuleRows && count($JSModuleRows)>0)
        {
            foreach($JSModuleRows as $JSModuleRow)
            {
                if($JSModuleRow->published == '0')
                {
                    $warningMsg[] = array( 'name' => JTEXT::_( 'Module Conflict' ).' '.$JSModuleRow->module, 'description' => "This module won't work anymore. Please do not enable it at any time or uninstall completely" );
                }
                else
                {
                    $errorMsg[] = array( 'name' => JTEXT::_( 'Module Conflict' ).' '.$JSModuleRow->module, 'description' => "This module won't work anymore. Please uninstall to prevent fatal exceptions" );
                }
            }
        }

		JSInstall::viewJSStatusForInstallProcess( $errorMsg, $warningMsg, $infoMsg );
	}

    	/**
	 * shows messages just after installation process
	 *
	 * @param string $errorMsg
	 * @param string $warningMsg
	 * @param string $infoMsg
	 */
	function viewJSStatusForInstallProcess( $errorMsg, $warningMsg, $infoMsg ) {
		$isInstallPage = true;

		$StatusTData = new stdClass();//new js_JSStatusTData();
        $StatusTData->errorMsg = array();
        $StatusTData->warningMsg = array();
        $StatusTData->infoMsg = array();
		//messages from install process are more important, add them at begining
		foreach( $errorMsg as $msg )
        {
//            echo "<p>$msg</p>";
			$StatusTData->errorMsg[] = $msg;
		}

		foreach( $warningMsg as $msg ) {
//            echo "<p>$msg</p>";
			$StatusTData->warningMsg[] = $msg;
		}

		foreach( $infoMsg as $msg ) {
//            echo "<p>$msg</p>";
			$StatusTData->infoMsg[] = $msg;
		}
//        $JSStatus = new js_JSStatus();

//		$JSStatus->viewJSStatusPageBase( $isInstallPage, $StatusTData );

		include( dirname(__FILE__) .DS. 'install.com_j4age.html.php' );
	}
}

$jsInstall = new JSInstall();
$jsInstall->com_install();

// mic: dummy function do NOT delete it!
function com_install() { }