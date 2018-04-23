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
global $js_start;
if(empty($js_start))
{
    $js_start = time();
}

jimport('joomla.version');
$version = new JVersion();

if(!defined("JS_IS_JOOMLA16"))
{
    define("JS_IS_JOOMLA16", $version->isCompatible("1.6.0") );
}

$mainframe = JFactory::getApplication();

require_once( dirname( __FILE__ ) .DS. 'api'.DS. 'toolbar.j4age.php' );
require_once( dirname( __FILE__ ) .DS. 'libraries' .DS. 'base.classes.php' );
require_once( dirname( __FILE__ ) .DS. 'libraries' .DS. 'util.classes.php' );
require_once(JApplicationHelper::getPath('admin_html'));
//$mydate = JFactory::getDate();
//echo time().' => '.gmdate('Y-m-d H:i:s P e').' => '.date('Y-m-d H:i:s P e').$mydate->toFormat('%Y-%m-%d %H:%M:%S');
//echo '<br/>'.JURI::getInstance()->toString();

js_PluginManager::fireEvent('beforeLoad');

//set a default view (just a fallback)
JRequest::setVar('view', JRequest::getVar('view', 'visitors'));
{//check user autorization (code from 'com_banners' from j1.5.6)
	// Make sure the user is authorized to view this page
	$user = & JFactory::getUser();
	//if( !$user->authorize( 'com_config', 'manage' ) ) {//if we use this line only 'super administrators' will be able to view JoomlaStats. Mic suggest to use that way - it is most restricted access
	//if (!$user->authorize( 'com_j4age', 'manage' )) { //this line is wrong!!! ACL has not got JoomlaStats registered! This line always fail
	if (!$user->authorize( 'com_components', 'manage' )) { //this line allow all (that have permission to login to joomla back-end) to view JoomlaStats
		$mainframe->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
        return;
	}
}

$document =& JFactory::getDocument();
$document->addStyleSheet('components/com_j4age/assets/icon.css');

$user  =&  JFactory::getUser();
$JSConf =& js_JSConf::getInstance();
$JSUtil = new js_JSUtil();


// Require specific controller if requested
$controller = JRequest::getVar('controller', 'main');
if(empty($controller))
{
    $controller = 'main';
}

if( $JSConf->updateRequired() || $JSConf->showInstaller() )
{
    /**
     * The newest files are stored, but there are still some outstanding post-installation steps to perform
     *
     * We are going to force to redirect all request to the installer process - nothing is allowed until we are done here
     */
    if($controller != 'installer')
    {
        $view = 'install';
        $task = 'default';
        JRequest::setVar( 'task', $task);
        JRequest::setVar( 'view', $view);
    }
    $controller = 'installer';
    JRequest::setVar( 'controller', $controller);
}

require_once (dirname(__FILE__).DS.'controllers'.DS.$controller.'.php');

// Create the controller
$classname	= 'j4ageController'.$controller;

$config = array();

$controller = new $classname( $config );

/**
 * We need the controller as global, to be able to access the object at any position
 */
global $js_controller;
$js_controller = $controller;

// Perform the Request task

$task = JRequest::getVar( 'task');
$view = JRequest::getVar( 'view');
if(empty($task))
{
    //todo "task" is mostly wrong used and actually should be called "view". However, until the refactoring is in process, we are supporting "view" and "task" 
    //$task = trim(JRequest::getVar( 'view', 'js_view_statistics_default'));
}
// 'js_view_statistics_default' means that we should display task that user select as 'default start page' in configuration (user selection)
if( $task == 'js_view_statistics_default' ) {
	$task = $JSConf->startoption;
	JRequest::setVar( 'task', $task );
}

$cid = JRequest::getVar( 'cid', array(0));

if (!is_array($cid))
	$cid = array (0);

js_echoJSDebugInfo("task: '$task' view: $view<br/>", '');

js_profilerMarker('Start');
js_PluginManager::fireEventUsingSource( 'onExecute', $controller);
$controller->execute( $task );
js_PluginManager::fireEvent('afterLoad');
js_profilerMarker('Stop');

//Below the system check to make sure, that the user has everything installed correctly.
//todo refactor code below into a outsourced helper class, which is going to handle the version differences
{
  jimport('joomla.version');
  $version = new JVersion();
  $query = null;
  if( JS_IS_JOOMLA16 )
  {
      $query = "SELECT *, enabled as published FROM `#__extensions` WHERE `type` LIKE 'module' AND `element` LIKE '%_jstats_%'";
  }
  else
  {
      $query = "SELECT * FROM `#__modules` WHERE `module` LIKE '%_jstats_%'";
  }

  $JSDatabaseAccess = js_JSDatabaseAccess::getInstance();
  $JSDatabaseAccess->db->setQuery( $query );
  $JSModuleRows = $JSDatabaseAccess->db->LoadObjectList();
  if (!empty($JSModuleRows) > 0)
  {
      foreach($JSModuleRows as $JSModuleRow)
      JError::raiseWarning(100, JText::_('Module').' '.$JSModuleRow->module.' '.JText::_('must be uninstalled or disabled'));
  }

  if( JS_IS_JOOMLA16 )
  {
      $query = "SELECT *, enabled as published FROM `#__extensions` WHERE `type` LIKE 'module' AND `element` LIKE 'mod_j4age_activate'";
  }
  else
  {
      $query = "SELECT * FROM `#__modules` WHERE `module` LIKE 'mod_j4age_activate'";
  }

  $JSDatabaseAccess->db->setQuery( $query );
  $JSModuleRow = $JSDatabaseAccess->db->loadObject();
  if(!empty($JSModuleRow))
  {
      if ( $JSModuleRow->published == 1)
      {
          //Activation Module running
      }
      else
      {
          //Module installed, but not enabled. lets check for the plugin
          if( JS_IS_JOOMLA16 )
          {
              $query = "SELECT *, enabled as published FROM `#__extensions` WHERE `type` LIKE 'plugin' AND `element` LIKE 'j4age'";
          }
          else
          {
              $query = "SELECT * FROM `#__plugins` WHERE `element` LIKE 'j4age'";
          }


          $JSDatabaseAccess->db->setQuery( $query );
          $JSPluginRow = $JSDatabaseAccess->db->loadObject();

          if(!empty($JSPluginRow))
          {
              if( $JSPluginRow->published == 1)
              {
                  //Plugin Running
              }
              else
              {
                  //Remind to enable the plugin
                  JError::raiseWarning(100, JText::_('Module').' mod_j4age_activate or '.JText::_('Plugin').' plg_j4age_activate '.JText::_('needs to be enabled to collect statistics'));
              }
          }

          else
          {
              //Remind to enable the module
              JError::raiseWarning(100, JText::_('Module').' mod_j4age_activate '.JText::_('needs to be installed and enabled to collect statistics'));
          }
      }
  }
  else
  {
          //Module  not installed. lets check for the plugin
          if( JS_IS_JOOMLA16 )
          {
              $query = "SELECT *, enabled as published FROM `#__extensions` WHERE `type` LIKE 'plugin' AND `element` LIKE 'j4age'";
          }
          else
          {
              $query = "SELECT * FROM `#__plugins` WHERE `element` LIKE 'j4age'";
          }

          $JSDatabaseAccess->db->setQuery( $query );
          $JSPluginRow = $JSDatabaseAccess->db->loadObject();

          if(!empty($JSPluginRow))
          {
              if( $JSPluginRow->published == 1)
              {
                  //Plugin Running
              }
              else
              {
                  JError::raiseWarning(100, JText::_('Plugin').' plg_j4age_activate '.JText::_('needs to be enabled to collect statistics'));
              }
          }

          else
          {
              //Remind to install the module or plugin
              JError::raiseWarning(100, JText::_('Module').' mod_j4age_activate or '.JText::_('Plugin').' plg_j4age_activate '.JText::_('needs to be installed and enabled to collect statistics'));
          }
  }


}


// Redirect if set by the controller
$controller->redirect();

?>
<div style="text-align:center; color:#9F9F9F; font-size:0.9em"><P>
    &copy;2009-<?php echo js_gmdate( 'Y' ); ?> <a href="http://www.ecomize.com" target="_blank" title="Visit our Homepage">ecomize AG</a> - All rights reserved.<br />
    <a href="http://www.ecomize.com" target="_blank" title="Visit our Homepage">j4age</a>
    is released under the GNU/GPL License.<br /></P>
</div>