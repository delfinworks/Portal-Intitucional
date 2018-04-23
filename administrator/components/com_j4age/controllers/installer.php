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

jimport('joomla.application.component.controller');

//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'XmapPluginInstaller.php');
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_installer'.DS.'models'.DS.'install.php');
require_once( dirname( __FILE__ ) .DS. 'main.php' );
require_once( dirname(__FILE__) .DS. '..' .DS. 'libraries' .DS. 'template.html.php' );
require_once( dirname(__FILE__) .DS.'..'.DS.'api'.DS. 'tools.php' );


class j4ageControllerInstaller extends j4ageControllerMain
{
    var $steps = array();
    var $steps_per_Versions = null;
    var $versionToBeUpgrade = null;
    var $versionToBeNextUpgrade = null;
    var $countOfStepsForUpgradeToNextVersion = null;
    var $currentStepToNextVersion = null;

    function __construct(  )
    {
        parent::__construct();
        global $js_installer_instance;
        if($js_installer_instance == null)
        {
            $js_installer_instance = $this;
        }
    }

    function douninstall()
    {
        JRequest::setVar('view', 'uninstall');

        $JSTools = new js_JSTools();
        $JSTools->doJSUninstall();
        //$this->display(false);
    }

    function uninstall()
    {
        js_echoJSDebugInfo('called method uninstall<br/>', '');
        JRequest::setVar('view', 'uninstall');
        $this->display();
    }

    function getInstallerInstance()
    {
        global $js_installer_instance;
        if($js_installer_instance == null)
        {
            $js_installer_instance = new j4ageControllerInstaller();
        }
        return $js_installer_instance;
    }
    /**
     * Method to display the view
     *
     * @access    public
     */
    function display($surroundings = false, $printform = null, $toolbar = false)
    {
        $this->JoomlaStatsEngine->FilterTimePeriod->hide = true;
        $result = parent::display($surroundings, $printform, $toolbar);
        JToolBarHelper::title( 'j4age'.': <small><small>[ ' . JTEXT::_( 'Post-Installation' ) . ' ]</small></small>', 'js_js-logo.png' ); // this generate demand for css style 'icon-48-js_js-logo'
        return $result;
    }
    
    function installfromdir() {
	    return $this->uploadPlugin();
    }

    function uploadPlugin( )
    {
        $lang =& JFactory::getLanguage();
        $lang->load('com_installer',JPATH_ADMINISTRATOR);

        $installerModel = new InstallerModelInstall();

            // Get an installer instance
        $installer =& JInstaller::getInstance();

        $jsInstaller = new js_PluginInstaller($installer);

            /* Fix for a small bug on Joomla on PHP 4 */
        if (version_compare(PHP_VERSION, '5.0.0', '<'))
        {
            // We use eval to avoid PHP warnings on PHP>=5 versions
            eval("\$installer->setAdapter('js_ext',\$jsInstaller);");
        }else
        {
            $installer->setAdapter('js_ext',$jsInstaller);
        }
        $jsInstaller->parent = &$installer;

        if( JS_IS_JOOMLA16)
        {
            $installer->setAdapter('js_ext', $jsInstaller);
        }
        else{
            $installer->_adapters['js_ext'] = &$jsInstaller;
        }
            /* End of the fix for PHP <= 4 */

        if ($installerModel->install())
        {
            //show installation msg
            $this->setRedirect( 'index.php?option=com_j4age&task=default&view=tools&controller=maintenance' );
        } else
        {
            //failed msg
            JRequest::setVar('view', 'tools');
            $this->display(false);
        }
    }

    function executeStep()
    {
      $this->handleStepExecution(true, false);
    }

    function executeAll()
    {
        $this->handleStepExecution(false, true);
    }

    /**
     * Migrates a old DB structure to the new scheme
     *
     * @return void
     */
    function handleStepExecution($stepByStep = false, $isredirect = false)
    {
        /**
         * There is a limit for redirects otherwise we would get an HTTP error, if we exceed 15 redirects
         */
        $redirectCounter = JRequest::getVar('rCnt', 0);
        $redirectCounter = $redirectCounter + 1;

        if($redirectCounter > 15)
        {
          $stepByStep = true;
        }

        if($this->steps_per_Versions == null)
        {
           $this->loadMigrationSteps();
        }

        /**
         * Get the config object as we store there also any migration / upgrade informations
         */
        $JSConf =& js_JSConf::getInstance();

        /**
         * Get the last performed step
         */
        $lastPerformedStep = $JSConf->current_step;
        $currentVersion    = $JSConf->JSVersion;
        $currentSteps      = current($this->steps_per_Versions);
        $nextStep          = null;
        if($lastPerformedStep < $this->countOfStepsForUpgradeToNextVersion)
        {
            /**
             * We are in progress of doing the next step of few to migrate to the next version
             */
           $nextStep = $currentSteps[$lastPerformedStep];
           $JSConf->current_step = $JSConf->current_step +1;
        }
        else
        {
            /**
             * All steps are performed, so we are updating the version string in the DB to be able to process the next steps
             */
            $JSConf->current_step = 0;
            $JSConf->JSVersion = $this->versionToBeNextUpgrade;
            $finished = false;
            /**
             * We have reached the end of the update once there is one entry left in the array
             */
            if(count($this->steps_per_Versions) <= 1)
            {
                $JSConf->update_static_data = 0; 
                $JSConf->JSVersion = $JSConf->BuildVersion;
                $finished = true;
                /**
                 * this section ensures that pre-intergrated plugins are fully configured
                 */
                $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
                $plg = new js_Plugin($JSDatabaseAccess->db);
                if( $plg->load('ext_ip2nation') )
                {

                }
                else
                {
                    $plg->description = "ext_ip2nation";
                    $plg->value = 1;
                    $plg->params = "active=1";
                    $plg->store();
                }
            }

            $err_msg = '';
            /**
             * Update Version in the DB and perform redirect to the next step
             */
            $JSConf->storeConfigurationToDatabase($err_msg);
            
            if( !empty($err_msg) )
            {
                js_JSTemplate::startBlock();
                echo $err_msg;
                js_JSTemplate::endBlock();

            }
            else
            {
                $msg		= JTEXT::_( 'Version successfully changed to ' ).$this->versionToBeNextUpgrade;
                //it is good to make optimalization (database structure can change a little), but it is not neccessary

                //js_JSUtil::optimizeAllJSTables();
                if($finished)
                {
                    /**
                     * Last step was performed
                     */
                    $task = $JSConf->startoption;
                    JRequest::setVar( 'task', $task);
                    JRequest::setVar( 'controller', 'main');
                    /**
                     * Set the component flag to j4age, so that no further Joomlastats -> J4Age conversion is required
                     */
                    $JSConf->component = 'j4age';
                    //return $this->display();
                        $this->setRedirect( 'index.php?option=com_j4age&task=default&view=install&controller=installer&layout=info&rCnt='.$redirectCounter, $msg, 'message' );//third argument: 'message', 'notice', 'error'
                        // $this->setRedirect( 'index.php?option=com_j4age&task='.$task.'&controller=main', $msg, 'message' );//third argument: 'message', 'notice', 'error'
                    return;
                }
                else
                {
                    if($stepByStep)
                    {
                        $this->setRedirect( 'index.php?option=com_j4age&task=default&view=install&controller=installer&layout=steps&rCnt='.$redirectCounter.($isredirect?'&rGo=1':''), $msg, 'message' );//third argument: 'message', 'notice', 'error'
                        //return $this->display();
                    }
                    else
                    {
                        $this->setRedirect( 'index.php?option=com_j4age&task=executeAll&view=install&controller=installer&layout=steps&rCnt='.$redirectCounter.($isredirect?'&rGo=1':''), $msg, 'message' );//third argument: 'message', 'notice', 'error'
                    }
                }
            }
            return;
        }
        if(!$stepByStep && $nextStep->isEmpty())
        {
            $error_msg = '';
            $JSConf->storeConfigurationToDatabase($error_msg);
            $this->handleStepExecution($stepByStep, $isredirect);
            return;
        }
        js_JSTemplate::startBlock();
        echo "<strong>Step Execution:</strong><br/><br/>";
        if( $nextStep->execute() )
        {
            //echo "Migration Step success";
            $error_msg = '';
            $JSConf->storeConfigurationToDatabase($error_msg);
            if(!empty($error_msg))
            {
                //JError::raise(E_NOTICE, 0, "Upgrade of version <strong>$currentVersion</strong> to <strong>".$this->versionToBeNextUpgrade."</strong> - Step ".$this->currentStepToNextVersion." of ".$this->countOfStepsForUpgradeToNextVersion."");
                echo $error_msg;
               //JError raise 
            }
            else
            {
                if( ($lastPerformedStep +1) >= $this->countOfStepsForUpgradeToNextVersion)
                {
                    $this->handleStepExecution($stepByStep, $isredirect);
                    return;
                }
                else
                {
                    $msg = null;//JTEXT::_( 'Successfully performed step' ).' '.($lastPerformedStep+1);
                    if($stepByStep)
                    {
                        $this->setRedirect( 'index.php?option=com_j4age&task=default&view=install&controller=installer&layout=steps&rCnt='.$redirectCounter.($isredirect?'&rGo=1':''), $msg, 'message' );//third argument: 'message', 'notice', 'error'
                        //return $this->display();
                        return;
                    }
                    else
                    {
                        $this->setRedirect( 'index.php?option=com_j4age&task=executeAll&view=install&controller=installer&layout=steps&rCnt='.$redirectCounter.($isredirect?'&rGo=1':''), $msg, 'message' );//third argument: 'message', 'notice', 'error'
                        return;
                    }
                }
            }
        }
        else
        {
            $errors = $nextStep->getErrors();
            $errorMsg = implode( "<br/>",$errors);
            echo '<strong>'.JTEXT::_( 'Failed to perform step' ).' '.($lastPerformedStep+1).'</strong><br/><br/>';
            echo '<font color="red">'.$errorMsg."</font>";
        }

        js_JSTemplate::endBlock();
        $this->display();
    }

    /**
     * Migrates a old DB structure to the new scheme
     *
     * @return void
     */
    function loadMigrationSteps()
    {
        if($this->steps_per_Versions != null)
        {
            return;
        }
        $this->steps_per_Versions = array();
        require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'install'.DS.'update.db.j4age.inc.php');
        $defaultConfig = new js_JSConfDef();
        $config =& js_JSConf::getInstance();
        
        $currentVersion = $config->JSVersion;
        //$currentVersion = '1.1.0.600 dev'; //Just for tests, delete it
        js_UpdateJSDatabaseOnInstall($this, $currentVersion, $defaultConfig);

        /**
         * Nothing to do
         */
        if( $config->update_static_data )
        {
            /**
             * Queue all non transactional data to post-progress installation steps
             *
             * We refresh data. We delete existing data and load new one (that contains more rows) - this way exist from version 2.2.0
             * this is not the best way but... eg. data does not exist in 2.2.0 nor 2.3.0.84, next we will make fix (add new data) in in 2.2.1, and in the same moment in 2.3.0.104 - every thing will work all right
            */
            require_once( JPATH_COMPONENT_ADMINISTRATOR .DS. 'install' .DS. 'all.data.j4age.inc.php' );

            js_appendSystemDataSteps($this, $currentVersion, $config->BuildVersion);
        }

        js_PluginManager::fireEventUsingSource('onPostInstallation', $this);

        /**
         * Nothing to do
         */
        if(empty($this->steps))
        {
            return;
        }

        /**
         * We have to sort all steps by the version to avoid any conflicts
         */
        usort($this->steps, 'js_version_compareStep');

        $this->totalAmountOfAllSteps = count($this->steps);

        /**
         * All Steps are now sorted, but there might be several steps to upgrade to a specific version.
         *
         * Now we bundle all steps for an specific version together
         *
         * Assumption: If we recall the method again, we would get the same count of steps per version
         * in the same order, so that we can process every step individual with in a single HTTP request
         */
        foreach($this->steps as $step)
        {
            /**
             * Ignore steps for future releases, which do not match the current Build Version
             */
            if(js_JSUtil::JSVersionCompare( $step->version, $config->BuildVersion, '>'))
            {
                $this->totalAmountOfAllSteps = $this->totalAmountOfAllSteps -1;
                continue;
            }

            $steps = &$this->steps_per_Versions[$step->version];
            if($steps)
            {
                $steps[] = $step;
            }
            else
            {
                $steps = array();
                $steps[] = $step;
                $this->steps_per_Versions[$step->version] =& $steps;
            }
            //echo $step->version ."<br/>";
        }
        /**
         * @todo This has to move partially to the view & migration method
         */
        $versions = array_keys($this->steps_per_Versions);
        $versions_steps = array_values($this->steps_per_Versions);

        $this->versionToBeUpgrade = $versions[count($versions)-1];
        $this->versionToBeNextUpgrade = $versions[0];
        $this->countOfStepsForUpgradeToNextVersion = count($versions_steps[0]);
        
        $this->currentStepToNextVersion = $config->current_step +1;
        
        if(count($this->steps_per_Versions) <= 1)
        {
            $this->versionToBeNextUpgrade = $config->BuildVersion;
        }
        $this->versionToBeUpgrade = $config->BuildVersion;
    }

    function appendStep(&$step)
    {
       $step->number = count($this->steps);
       $this->steps[] = $step;
    }

    function appendSQLStep($version, $query, $description = null)
    {
        $step = new js_InstallStep($version, $query, $description);
        $this->appendStep( $step );
    }

    function uninstallPlugin( )
    {
        $plugin_name = JRequest::getVar('plugin',null);
        $lang =& JFactory::getLanguage();
        $lang->load('com_installer',JPATH_ADMINISTRATOR);

            // Get an installer instance
        $installer =& JInstaller::getInstance();

        $jsInstaller = new js_PluginInstaller($installer);
        $installer->setAdapter('js_ext',$jsInstaller);

        if ($installer->uninstall('js_ext', $plugin_name ))
        {
            JError::raiseNotice(0, JText::_('Plugin').' '.JText::_('Uninstall').':'.JText::_('Success'));
        } else
        {
            JError::raiseWarning(100, JText::_('Plugin').' '.JText::_('Uninstall').': '.$installer->getError());
        }

        JRequest::setVar('view', 'tools');
        $this->display(false);
    }

    function showPluginSettings ()
    {
        $JSDatabaseAccess =& js_JSDatabaseAccess::getInstance();

        $plugin_name = JRequest::getVar('plugin',JRequest::getVar('id',''));
        $plugin = new js_Plugin($JSDatabaseAccess->db);
        if (empty($plugin_name) || !$plugin->load($plugin_name))
        {
            die('Cannot load plugin');
        }

		$xmlfile = $plugin->getXmlPath();

		$params = new JParameter( $plugin->params, $xmlfile, 'com_j4age' );
        ?>

        <form name="frmSettings" id="frmSettings<?php echo $plugin->description; ?>">
            <input type="hidden" name="controller" value="installer" />
            <input type="hidden" name="task" value="savePluginConfig" />
            <input type="hidden" name="option" value="com_j4age" />
            <input type="hidden" name="plugin" value="<?php echo $plugin->description; ?>" />
            <input type="hidden" name="tmpl" value="component"/>
            <?php echo $params->render(); ?>
            <div style="text-align: center;padding: 5px;">
               <input type="button" name="cancel" onclick="window.parent.document.getElementById('sbox-window').close();" value="<?php echo JText::_('Cancel'); ?>" />&nbsp;&nbsp;&nbsp;
               <input type="button" name="save" onclick="document.frmSettings.submit();window.top.setTimeout('window.parent.document.getElementById(\'sbox-window\').close()', 700);" value="<?php echo JText::_("Save"); ?>" />
            </div>
        </form>
        <?php
	}
    
    function changePluginState()
    {
        $JSDatabaseAccess =& js_JSDatabaseAccess::getInstance();

        $plugin_name = JRequest::getVar('plugin',JRequest::getVar('id',''));

        $plugin = new js_Plugin($JSDatabaseAccess->db);
        if (empty($plugin_name) || !$plugin->load($plugin_name))
        {
            js_echoJSDebugInfo('Cannot load plugin');
            return;
        }
        /**
         * @todo retrieve a plugin type from the plugin settings to differentiate between plugins for modules or component
         */
        $plugin->value = ($plugin->value == '0' ? '1' : '0');
        if ($plugin->store())
        {
        } else {
            JError::raiseWarning(100, JText::_('Plugin').' '.JText::_('Change').': '.$database->getErrorMsg());
        }
        JRequest::setVar('view', 'tools');
        $this->display(false);        
    }

    function savePluginConfig()
    {
        js_echoJSDebugInfo('Start save plugin');
        $JSDatabaseAccess =& js_JSDatabaseAccess::getInstance();

        $plugin_name = JRequest::getVar('plugin',JRequest::getVar('id',''));

        $plugin = new js_Plugin($JSDatabaseAccess->db);
        if (empty($plugin_name) || !$plugin->load($plugin_name))
        {
            js_echoJSDebugInfo('Cannot load plugin');
            return;
        }

        if ( $plugin->description )
        {
            $params = JRequest::getVar('params');
            if (is_array( $params ))
            {
                $plugin->parseParams();
                $txt = array();
                foreach ($params as $k=>$v) {
                    $txt[] = "$k=" . str_replace( "\n", '<br />', $v );
                }

                $params = implode("\n",$txt);
                $plugin->setParams($params);
                if ( $plugin->store() )
                {
                    JError::raiseNotice(0, JText::_('Plugin').' '.JText::_('Save').':'.JText::_('Success'));
                } else {
                    js_echoJSDebugInfo($database->getErrorMsg());
                }
            }
            else
            {
               js_echoJSDebugInfo('No params passed');
            }
        } else {
            js_echoJSDebugInfo('Invalid plugin');
        }
         js_echoJSDebugInfo('End save plugin');
        $this->showPluginSettings();
    }
}

class js_InstallStep extends JObject
{
    var $version;
    var $description;
    var $query; //Can be a string as SQL query or array of SQL queries
    var $number = null;

    /** constructor do nothing. Only for PHP4.0 */
    function __construct($version, $query, $description = '') {
        $this->version = $version;
        $this->query = $query;
        $this->description = $description;
    }

    function isEmpty()
    {
        if(empty($this->query))
        {
            return true;
        }
        return false;
    }

    function execute()
    {
        $JSDatabaseAccess =& js_JSDatabaseAccess::getInstance();
        if(is_array($this->query))
        {
            foreach($this->query as $query)
            {
                $JSDatabaseAccess->db->setQuery( $query );
                if (!$JSDatabaseAccess->db->query())
                {
                    if ($JSDatabaseAccess->db->getErrorNum() > 0)
                    {
                        //js_echoJSDebugInfo("".$this->db->getErrorMsg(), '');
                        $this->setError($JSDatabaseAccess->db->getErrorMsg());
                    }
                    return false;
                }
            }
        }
        else if(!empty($this->query))
        {
            $JSDatabaseAccess->db->setQuery( $this->query );
            if (!$JSDatabaseAccess->db->query())
            {
                if ($JSDatabaseAccess->db->getErrorNum() > 0)
                {
                    //js_echoJSDebugInfo("".$this->db->getErrorMsg(), '');
                    $this->setError($JSDatabaseAccess->db->getErrorMsg());
                }
                return false;
            }
        }
        return true;
    }
}

/**
 * Extension installer
 *
 */
class js_PluginInstaller extends JObject
{
    /**
     * Constructor
     *
     * @access    protected
     * @param    object    $parent    Parent object [JInstaller instance]
     * @return    void
     * @since    1.5
     */
    function __construct(&$parent)
    {
        $this->parent =& $parent;
    }

    /**
     * Custom install method
     *
     * @access    public
     * @return    boolean    True on success
     * @since    1.5
     */
    function install()
    {
        // Get a database connector object
        //$db =& $this->parent->getDBO();
        $JSDatabaseAccess =& js_JSDatabaseAccess::getInstance();
        $db = $JSDatabaseAccess->db;
        // Get the extension manifest object
        $manifest =& $this->parent->getManifest();
        $this->parent->setOverwrite(true);
        $this->manifest =& $manifest->document;

        /**
         * ---------------------------------------------------------------------------------------------
         * Manifest Document Setup Section
         * ---------------------------------------------------------------------------------------------
         */

        $mymanifest  =& $this->manifest;

        $name = null;
        $description = null;
        $type = null;
        $manifestChildrens = array();
        if($mymanifest instanceof JSimpleXMLElement)
        {
            $xmlvalue =& $mymanifest->getElementByPath('name');
            $xmlvalue = $xmlvalue->data();
            $name = $xmlvalue;

            $xmlvalue =& $mymanifest->getElementByPath('description');
            $xmlvalue = $xmlvalue->data();
            $description = $xmlvalue;

            $manifestChildrens = $this->manifest->children();
            /*
            * Backward Compatability
            * @todo Deprecate in future version
            */
            $type = $this->manifest->attributes('type');  //this should be js_ext

        }
        else
        {
            $result = $manifest->xpath('child::name');
            $xmlvalue = $result[0]->__toString();
            $name = $xmlvalue;

            $result = $manifest->xpath('child::description');
            $xmlvalue = $result[0]->__toString();
            $description = $xmlvalue;

            $manifestChildrens = $manifest->xpath('child::*');

            $type = $manifest->xpath('@type');
            $type = $type[0]->__toString();
        }
        // Set the extensions name
        $name = JFilterInput::clean($name, 'cmd');
        $this->set('name', $name);
        $this->parent->set('message', $description );
        $pname = null;
        foreach($manifestChildrens as $manifestChildren)
        {
            $elementName = $manifestChildren->name();
            if($elementName == 'files')
            {
                $files =& $manifestChildren->children();

                foreach ($files as $file)
                {
                    $attr = null;
                    if(method_exists($file,'getAttribute'))
                    {
                      $attr = $file->getAttribute($type);
                    }
                    else{
                        $attr = $file->attributes($type);
                    }


                    if ($attr)
                    {
                        $pname = $attr;
                        break;
                    }
                }
            }
        }

        // Set the installation path
 //       $element =& $this->manifest->getElementByPath('files');


        /**
         * ---------------------------------------------------------------------------------------------
         * Database Processing Section
         * ---------------------------------------------------------------------------------------------
         */

        // Check to see if a extension by the same name is already installed
        $query = 'SELECT `value`, description FROM `#__jstats_configuration` WHERE description = '.$db->Quote($pname);
        $db->setQuery($query);
        if (!$db->Query()) {
            // Install failed, roll back changes
            $this->parent->abort('Extension Install: '.$db->stderr(true));
            return false;
        }
        $result = $db->loadObject();


            // Was there a module already installed with the same name?
        if (!empty($result)) {

            if (!$this->parent->getOverwrite())
            {
                // Install failed, roll back changes
                $this->parent->abort('Extension Install: Extension "' . $pname . '" already exists!' );
                js_echoJSDebugInfo('Plugin already installed - overwrite on');
                return false;
            }
            else
            {
                js_echoJSDebugInfo('Plugin already installed - overwrite off');
            }

        } else {
            js_echoJSDebugInfo($pname.' Plugin not yet installed');
            // Check to see if there is a backup for this extension
            $query = 'SELECT `value` FROM `#__jstats_configuration` WHERE description = '.$db->Quote($pname. '.bak');
            $db->setQuery($query);
            if (!$db->query()) {
                // Install failed, roll back changes
                $this->parent->abort('Extension Install: '.$db->stderr(true));
                return false;
            }
            $published = $db->loadResult();

            $row = new js_Plugin($db,$pname);
            //$row->extension = $pname;
            $row->value = 1;
            if (!$published ) {
                $row->setParams($this->parent->getParams(),'-1');
            }

            if (!$row->store()) {
                // Install failed, roll back changes
                $this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.$db->stderr(true));
                return false;
            }

                // Since we have created a extension item, we add it to the installation step stack
                // so that if we have to rollback the changes we can undo it.
            $this->parent->pushStep(array ('type' => 'js_ext', 'id' => $row->description));
        }

        /**
         * ---------------------------------------------------------------------------------------------
         * Copy Files - do not perform this step before any SQL enquires - you could run into an DB timeout
         * ---------------------------------------------------------------------------------------------
         */
        foreach($manifestChildrens as $manifestChildren)
        {
            $elementName = $manifestChildren->name();
            if($elementName == 'files')
            {
                if (!$this->copyFiles($manifestChildren, $pname, $type))
                {
                    // Install failed, roll back changes
                    $this->parent->abort();
                    return false;
                }
            }
        }


        /**
         * The manifest file should be within the extension folder
         */
        $this->parent->setPath('extension_root', JPATH_ROOT.DS.'/administrator/components/com_j4age/extensions');

            // Lastly, we will copy the manifest file to its appropriate place.
        if (!$this->parent->copyManifest(-1)) {
            // Install failed, rollback changes
            $this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('Could not copy setup file'));
            return false;
        }
        return true;
    }

    function copyFiles(&$element, &$pname, $type)
    {
        $folderType = 'admin';

        $attr = null;
        if(method_exists($element,'getAttribute'))
        {
            $attr = $element->getAttribute('folder');
        }
        else{
            $attr = $element->attributes('folder');
        }


        if ($attr) {
            $folderType = $attr;
        }

        $folderType = strtolower(trim($folderType));
        $files =& $element->children();
        if (count($files))  //if (is_a($element, 'JSimpleXMLElement') && count($childrens))
        {
            foreach ($files as $file)
            {
                if(method_exists($file,'getAttribute'))
                {
                    $attr = $file->getAttribute($type);
                }
                else{
                    $attr = $file->attributes($type);
                }
                if ($attr) {
                    $pname = $attr;
                    break;
                }
            }
        }

        if ( !empty ($pname) ) {
            $this->parent->setPath('extension_root', JPATH_ROOT.DS.'/administrator/components/com_j4age/extensions');
        } else {
            $this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('No extension file specified'));
            return false;
        }

        $pname = trim($pname);

        if(strpos($pname, 'ext_' ) != 0)
        {
            $this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('Wrong plugin name. Needs to be in the format of "ext_<name>"'));
            return false;
        }

        /**
         * ---------------------------------------------------------------------------------------------
         * Filesystem Processing Section
         * ---------------------------------------------------------------------------------------------
         */

        // If the extension directory does not exist, lets create it
        $created = false;
        if (!file_exists($this->parent->getPath('extension_root'))) {
            if (!$created = JFolder::create($this->parent->getPath('extension_root'))) {
                $this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('Failed to create directory').': "'.$this->parent->getPath('extension_root').'"');
                return false;
            }
        }

        /*
        * If we created the extension directory and will want to remove it if we
        * have to roll back the installation, lets add it to the installation
        * step stack
        */
        if ($created) {
            $this->parent->pushStep(array ('type' => 'folder', 'path' => $this->parent->getPath('extension_root')));
        }

        /**
         * Everything should be relative to the component root folder
         */
        if($folderType == 'site')
        {
            $this->parent->setPath('extension_root', JPATH_ROOT.DS.'/components/com_j4age/');
        }
        else
        {
            $this->parent->setPath('extension_root', JPATH_ROOT.DS.'/administrator/components/com_j4age/');
        }

        // Copy all necessary files
        if ($this->parent->parseFiles($element, -1) === false) {
            return false;
        }
        return true;
    }

    /**
     * Custom uninstall method
     *
     * @access    public
     * @param    int        $cid    The id of the extension to uninstall
     * @return    boolean    True on success
     * @since    1.5
     */
    function uninstall($id )
    {
        // Initialize variables
        $row    = null;
        $retval = true;
        $db        =& $this->parent->getDBO();
            // First order of business will be to load the module object table from the database.
            // This should give us the necessary information to proceed.
        $row = new js_Plugin($db, $id);
        $extension_name = $id;//$row->description;
        echo $id;
            // Set the extension root path
        $this->parent->setPath('extension_root', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j4age');

            // Because extensions don't have their own folders we cannot use the standard method of finding an installation manifest
        $manifestFile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j4age'.DS.'extensions'.DS.$extension_name.'.xml';
        if (file_exists($manifestFile))
        {
            $xml =& JFactory::getXMLParser('Simple');

                // If we cannot load the xml file return null
            if (!$xml->loadFile($manifestFile)) {
                JError::raiseWarning(100, JText::_('Plugin').' '.JText::_('Uninstall').': '.JText::_('Could not load manifest file'));
                return false;
            }

            /*
             * Check for a valid XML root tag.
             * @todo: Remove backwards compatability in a future version
             * Should be 'install', but for backward compatability we will accept 'mosinstall'.
             */
            $root =& $xml->document;
            if ($root->name() != 'install' && $root->name() != 'mosinstall') {
                JError::raiseWarning(100, JText::_('Plugin').' '.JText::_('Uninstall').': '.JText::_('Invalid manifest file'));
                return false;
            }

                // Remove the extension files
            $this->parent->removeFiles($root->getElementByPath('images'), -1);

            //$this->parent->removeFiles($root->getElementByPath('files'), -1);

                // Remove all media and languages as well
            $this->parent->removeFiles($root->getElementByPath('media'));
            $this->parent->removeFiles($root->getElementByPath('languages'), 1);

            $manifestChildrens = $root->children();

            foreach($manifestChildrens as $manifestChildren)
            {
                if($manifestChildren->name() == 'files')
                {
                    $folderType = 'admin';

                    if ($manifestChildren->attributes('folder')) {
                        $folderType = $manifestChildren->attributes('folder');
                    }

                    $folderType = strtolower(trim($folderType));
                    if($folderType == 'site')
                    {
                        $this->parent->setPath('extension_root', JPATH_ROOT.DS.'/components/com_j4age/');
                    }
                    else
                    {
                        $this->parent->setPath('extension_root', JPATH_ROOT.DS.'/administrator/components/com_j4age/');
                    }
                    $this->parent->removeFiles($manifestChildren, -1);
                }
            }
            JFile::delete($manifestFile);           
        } else {
            JError::raiseWarning(100, 'Plugin Uninstall: Manifest File invalid or not found: '.$manifestFile);
            return false;
        }

            // Now we will no longer need the extension object, so lets delete it
        //$row->extension = $row->extension . '.bak';
        $row->delete();

        return $retval;
    }

    /**
     * Custom rollback method
     *     - Roll back the extension item
     *
     * @access    public
     * @param    array    $arg    Installation step to rollback
     * @return    boolean    True on success
     * @since    1.5
     */
    function _rollback_extension($arg)
    {
        // Get database connector object
        $db =& $this->parent->getDBO();

            // Remove the entry from the #__extensions table
        $query = 'DELETE' .
                ' FROM `#__jstats_configuration`' .
                ' WHERE description='.(int)$arg['id'];
        $db->setQuery($query);
        return ($db->query() !== false);
    }

}

/** Wraps all configuration functions for Xmap */
class js_Plugin extends JTable {
	var $description= '';
	//var $extension 	= '';
    /**
     * 0 = Not active
     * 1 = Plugin for Component
     * 2 = Plugin for Modules
     *
     */
	var $value	= 1;
	var $params		= '';
	var $_params    = '';
    var $_exists = false;
    var $_instance = null;
    var $_id = null;

	function js_Plugin(&$_db,$id=NULL) {
		parent::__construct( '#__jstats_configuration', 'description', $_db );
        $this->_id = $id;
		if ($id) {
			$this->_exists = $this->load($id);
            $this->description = $id;
		}
 	}

    function load($id)
    {
        $this->_id = $id;
        $this->description = $id;
        $this->_exists = parent::load($id);
        if($this->_exists)
        {
            $this->_instance = js_PluginManager::getPlugin($id);
    
            if($this->_instance)
            {
              $this->value = $this->_instance->getPluginType();
            }
        }
        return $this->_exists;
    }

	function &getParams($Itemid='-1',$asTXT = false)
    {
		if (!is_array($this->_params)) {
			$this->parseParams();
		}
		if (!empty($this->_params[$Itemid])) {
			$params = $this->_params[$Itemid];
		} else {
			$params = $this->_params[-1];
		}
		if ($asTXT) {
			return $params['__TXT__'];
		}

		return $params;
        //return array();
	}

	function parseParams() {
        
		$this->_params = array('-1'=>array());
		if ($this->params) {
			preg_match_all('/(.?[0-9]+){([^}]+)}/',$this->params,$paramsList);
			$count = count($paramsList[1]);
			for ($i=0; $i < $count; $i++) {
				$this->_params[$paramsList[1][$i]] = $this->paramsToArray($paramsList[2][$i]);
			}
		}
	}

	function &loadDefaultsParams ($asText)
    {
        global $mosConfig_absolute_path;
		$path = $this->getXmlPath();
        $xmlDoc = new DOMIT_Lite_Document();
        $xmlDoc->resolveErrors( true );

		$params=null;
        if ($xmlDoc->loadXML( $path, false, true )) {
                $root =& $xmlDoc->documentElement;

                $tagName = $root->getTagName();
                $isParamsFile = ($tagName == 'mosinstall' || $tagName == 'install' || $tagName == 'mosparams');
                if ($isParamsFile && $root->getAttribute( 'type' ) == 'xmap_ext') {
                        $params = &$root->getElementsByPath( 'params', 1 );
                }
        }

		$result = ($asText)? '' : array();

        if (is_object( $params )) {
        foreach ($params->childNodes as $param) {
            $name = $param->getAttribute( 'name' );
            $label = $param->getAttribute( 'label' );

            $key = $name ? $name : $label;
            if ( $label != '@spacer' && $name != '@spacer') {
                $value = str_replace("\n",'\n',$param->getAttribute( 'default' ));
                if ($asText) {
                    $result.="$key=$value\n";
                } else {
                    $result[$key]=$value;
                }
            }
        }
		}
		return $result;
	}

        /** convert a menuitem's params field to an array */
	function paramsToArray( &$menuparams ) {
		$tmp = explode("\n", $menuparams);
		$res = array();
		foreach($tmp AS $a) {
			@list($key, $val) = explode('=', $a, 2);
			$res[$key] = str_replace('\n',"\n",$val);
		}
		$res['__TXT__'] = $menuparams;
		return $res;
        }

	function setParams($params,$itemid = '-1') {
		$this->_params[$itemid] = $params;
	}

	function getXmlPath () {
		return JPATH_ADMINISTRATOR.DS.'components'.DS.'com_j4age'.DS.'extensions'.DS.$this->description.'.xml';
	}

    	/**
	 * Inserts a new row if id is zero or updates an existing row in the database table
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @access public
	 * @param boolean If false, null object variables are not updated
	 * @return null|string null if successful otherwise returns and error message
	 */
	function store( $updateNulls=false, $updateParams = true )
	{
        if($updateParams)
        {
            if (is_array($this->_params)) {
                $this->params='';
                foreach ($this->_params as $itemid => $params) {
                    if ($params) {
//                        $this->params .= $itemid . '{' . $params . '}';
                        $this->params .=  $params.' ';
                    }
                }
            }
        }
		if( $this->_exists)
		{
			$ret = $this->_db->updateObject( $this->_tbl, $this, $this->_tbl_key, $updateNulls );
		}
		else
		{
			$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
		}
		if( !$ret )
		{
			$this->setError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());
			return false;
		}
		else
		{
			return true;
		}
	}

	function restore() {
		$database = & JFactory::getDBO();
		$query = "select * from #__jstats_configuration where description='".$this->description.".bak'";
		$database->setQuery($query);
		if ($row = $database->loadObject()) {
			$this->params=$row->params;
			$this->store(false, false);
		}
	}

}

function js_version_compareStep($a, $b)
{
    $result =  version_compare($a->version, $b->version);
    if($result == 0)
    {
       //Very important to keep the logical order of the entries
       return $a->number - $b->number;
    }
    return $result;
}



