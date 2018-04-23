<?php

              
/**
 * @package j4age
 * @copyright Copyright (C) 2009-@THISYEAR@ j4age Team. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * Thank you to the project j4age and it's team, on which roots this project is build on.
 */
             
          

// Check to ensure this file is only called within the Joomla! application and never directly
if( !defined( '_JEXEC' ) ) {
	die( 'JS: No Direct Access to '.__FILE__ );
}


jimport( 'joomla.application.component.view' );

require_once( dirname(__FILE__) .DS. '..'.DS. '..'.DS. 'controllers' .DS.'installer.php' );

/**
 * Hello View
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class j4ageViewInstall extends JView
{                    
	/**
	 * display method of Hello view
	 * @return void
	 **/
	function display($tpl = null)
	{
	/*
		$model	  =& $this->getModel();
		$text = '';
		JToolBarHelper::title(   JText::_( 'Technical Expertise' ).': <small><small>[ ' . $text.' ]</small></small>', 'dvw' );
		JToolBarHelper::save('saveEntry');
		if ($isNew)  {
			JToolBarHelper::cancel('cancelEntry');
		} else {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancelEntry', 'Close' );
		}
        */
		//show the sub content

        $JSConf = js_JSConf::getInstance();

        // first check if we are able to upgrade from this particular old version
        // Yes, we should do this. We should not allow to update from too old versions - too many queries and datbase operations could exide PHP time limit (30s) and we get broken database!!!
        if (js_JSUtil::JSVersionCompare( $JSConf->JSVersion, '2.2.0', '<') == true && js_JSUtil::JSVersionCompare( $JSConf->JSVersion, '0.0.0', '>') == true) {
            echo "<br/><br/>";
            echo "You try to update JoomlaStats from version ".$JSConf->JSVersion." to version ".$JSConf->BuildVersion."!!!<br/><br/>";
            echo "Unfortunately, we do not support an upgrade from versions older than 2.2.3n<br/><br/>";
            echo "Please completely uninstall JoomlaStats and all correspoding DB tables!!<br/><br/>";
            echo "<br/><br/>";
            return false;
        }

        //js_JSToolBarMenu::jsButton('publish', JTEXT::_( 'Execute Block' ) , 'executeAll', 'install', 'installer');
        //js_JSToolBarMenu::jsButton('publish', JTEXT::_( 'Execute Next' ) , 'executeStep', 'install', 'installer');
        
        $currentVersion  = $JSConf->JSVersion;
        $currentStep  = $JSConf->current_step;
        $js_installer = j4ageControllerInstaller::getInstallerInstance();
        $js_installer->loadMigrationSteps();

        $this->assignRef("steps_per_Versions", $js_installer->steps_per_Versions);
        $this->assignRef("nextVersion", $js_installer->versionToBeNextUpgrade);
        $this->assignRef("nextStep", $js_installer->currentStepToNextVersion);
        $this->assignRef("versionToBeUpgraded", $js_installer->versionToBeUpgrade);

        $document =& JFactory::getDocument();
        $document->addStyleSheet('components/com_j4age/assets/installer/css/installer.css');
        $this->assignRef("document", $document);
        $this->assignRef("buildVersion", $JSConf->BuildVersion);
        $this->assignRef("currentVersion", $currentVersion);
        $this->assignRef("nextVersion", $js_installer->versionToBeNextUpgrade);
        $this->assignRef("currentStep", $currentStep);
        $this->assignRef("nextStep", $js_installer->currentStepToNextVersion);
        $this->assignRef("nextVersionStepAmount", $js_installer->countOfStepsForUpgradeToNextVersion);
        $this->assignRef("totalAmountOfSteps", $js_installer->totalAmountOfAllSteps);
        $totalAmountOfStepsLeft = ($js_installer->totalAmountOfAllSteps - $js_installer->currentStepToNextVersion);
        $this->assignRef("totalAmountOfStepsLeft", $totalAmountOfStepsLeft);


        $options = array();

        $optionEntry = new stdClass();
        $optionEntry->label = JText::sprintf('PHP Version > %s', '5.0.0');
        $optionEntry->notice = (js_JSUtil::JSVersionCompare( phpversion(), '5.0.0', '<') ? JText::sprintf('%s Not officially Supported', ''.phpversion()) : sprintf('%s', ''.phpversion()) );
        $optionEntry->state = js_JSUtil::JSVersionCompare( phpversion(), '5.0.0', '>=');
        $options[] = $optionEntry;

        $optionEntry = new stdClass();
        $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
        $dbversion = $JSDatabaseAccess->db->getVersion();
        $optionEntry->label = JText::sprintf('DB Version > %s', '4.0.0');
        $optionEntry->notice = (js_JSUtil::JSVersionCompare( $dbversion, '4.0.0', '<') ? JText::sprintf('%s Not officially Supported', ''.$dbversion) : sprintf('%s', ''.$dbversion) );
        $optionEntry->state = js_JSUtil::JSVersionCompare( $dbversion, '4.0.0', '>=');
        $options[] = $optionEntry;

        $JSModuleRows = array();
        {
          $query = "SELECT * FROM `#__modules` WHERE `module` LIKE '%_jstats_%'";
          $JSDatabaseAccess->db->setQuery( $query );
          $JSModuleRows = $JSDatabaseAccess->db->LoadObjectList();
          if ($JSDatabaseAccess->db->getErrorNum() > 0)
              $installationErrorMsg .= JTEXT::_( 'Some errors occured during the j4age installation process' ) . 'Error: '.$JSDatabaseAccess->db->getErrorMsg();
        }

                /*Error Message - Show error message if j4age modules are installed*/
        if($JSModuleRows && count($JSModuleRows)>0)
        {
            foreach($JSModuleRows as $JSModuleRow)
            {
                $optionEntry = new stdClass();
                $dbversion = $JSDatabaseAccess->db->getVersion();
                $optionEntry->label = JText::sprintf('Disabled Module %s', $JSModuleRow->module);
                $optionEntry->notice = (($JSModuleRow->published == '0') ? JText::_('Can be uninstalled') : JText::_('Do disable or uninstall') );
                $optionEntry->state = $JSModuleRow->published == '0';
                $options[] = $optionEntry;
            }
        }


        $settings = array();

        $settingEntry = new stdClass();
        $settingEntry->label = JText::sprintf('PHP max_execution_time >= %ss', '30');
        $settingEntry->notice = ini_get('max_execution_time') >= 30 || ini_get('max_execution_time') <= 0 ? '' : JText::sprintf('%ss might be insufficent', ''.ini_get('max_execution_time'));
        $settingEntry->recommended = true;
        $settingEntry->state = ini_get('max_execution_time') >= 30 || ini_get('max_execution_time') <= 0;
        $settings[] = $settingEntry;

        $settingEntry = new stdClass();
        $settingEntry->label = JText::sprintf('MySQL mysql.connect_timeout >= %ss', '30');
        $settingEntry->notice = ini_get('mysql.connect_timeout') >= 30 || ini_get('mysql.connect_timeout') <= 0 ? '' : JText::sprintf('%s might be insufficent', ''.ini_get('mysql.connect_timeout'));
        $settingEntry->recommended = true;
        $settingEntry->state = ini_get('mysql.connect_timeout') >= 30 || ini_get('mysql.connect_timeout') <= 0;
        $settings[] = $settingEntry;

        $settingEntry = new stdClass();
        $settingEntry->label = JText::sprintf('PHP max_input_time >= %ss', '30');
        $settingEntry->notice = ini_get('max_input_time') >= 30 || ini_get('max_input_time') <= 0 ? '' : JText::sprintf('%ss might be insufficent', ''.ini_get('max_input_time'));
        $settingEntry->recommended = true;
        $settingEntry->state = ini_get('max_input_time') >= 30 || ini_get('max_input_time') <= 0;
        $settings[] = $settingEntry;

        $settingEntry = new stdClass();
        $settingEntry->label = JText::sprintf('PHP memory_limit >= %sMB', '10');
        $settingEntry->notice = ini_get('memory_limit') >= 10 || ini_get('memory_limit') <= 0 ? ini_get('memory_limit') : JText::sprintf('%s might be insufficent', ''.ini_get('memory_limit'));
        $settingEntry->recommended = true;
        $settingEntry->state = ini_get('memory_limit') >= 10 || ini_get('memory_limit') <= 0;
        $settings[] = $settingEntry;

        $settingEntry = new stdClass();
        $settingEntry->label = JText::sprintf('PHP interactive_timeout >= %ss', '30');
        $settingEntry->notice = ini_get('interactive_timeout') >= 30 || ini_get('interactive_timeout') <= 0 ? '' : JText::sprintf('%ss might be insufficent', ''.ini_get('interactive_timeout'));
        $settingEntry->recommended = true;
        $settingEntry->state = ini_get('interactive_timeout') >= 30 || ini_get('interactive_timeout') <= 0;
        $settings[] = $settingEntry;

        $settingEntry = new stdClass();
        $settingEntry->label = JText::sprintf('PHP connect_timeout >= %ss', '30');
        $settingEntry->notice = ini_get('connect_timeout') >= 30 || ini_get('connect_timeout') <= 0 ? '' : JText::sprintf('%ss might be insufficent', ''.ini_get('connect_timeout'));
        $settingEntry->recommended = true;
        $settingEntry->state = ini_get('connect_timeout') >= 30 || ini_get('connect_timeout') <= 0;
        $settings[] = $settingEntry;



        $settingEntry = new stdClass();
        $settingEntry->label = JText::sprintf('PHP connect_timeout >= %ss', '30');
        $settingEntry->notice = ini_get('connect_timeout') >= 30 || ini_get('connect_timeout') <= 0 ? '' : JText::sprintf('%ss might be insufficent', ''.ini_get('connect_timeout'));
        $settingEntry->recommended = true;
        $settingEntry->state = ini_get('connect_timeout') >= 30 || ini_get('connect_timeout') <= 0;
        $settings[] = $settingEntry;

        /*
echo "PHP Version : ".phpversion().' '.( js_JSUtil::JSVersionCompare( phpversion(), '5.0.0', '<') ? ("<span  style='color:red'>(Not officially Supported)</span>") : '' )."<br/>";
echo "DB Version : ".$dbversion.' '.( js_JSUtil::JSVersionCompare( $dbversion, '4.0.0', '<') ? ("<span  style='color:red'>(Not officially Supported)</span>") : '' )."<br/>";

echo "PHP Max. Execution Time : ".ini_get('max_execution_time')."s<br/>";
echo "PHP Max. SQL Connection Time : ".ini_get('mysql.connect_timeout')."s<br/>";
echo "PHP Max. Input Time : ".ini_get('max_input_time')."s<br/>";
echo "PHP Memory Limit : ".ini_get('memory_limit')."<br/>";
echo "PHP Interative Timeout : ".ini_get('interactive_timeout')."<br/>";
echo "PHP Connection Timeout : ".ini_get('connect_timeout')."<br/>";
echo "PHP Max Connections : ".ini_get('max_connections')."<br/>";  */

        $this->assignRef("options", $options);
        $this->assignRef("settings", $settings);

		parent::display($tpl);
	}	
}
