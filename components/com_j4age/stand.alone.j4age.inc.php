<?php
/**
 * @package JoomlaStats
 * @copyright Copyright (C) 2004-2009 JoomlaStats Team. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */


/** 
 *    HOW TO RUN JOOMLASTATS FOR NON-JOOMLA PAGES
 *
 * Make a) or b):
 *
 * a) Use JoomlaStats API
 *
 * b) Include this file to non-joomla CMS *.php files
 *      (paste below line to pages that You want to count visitors and fix path to this file)
 *      include(dirname(__FILE__) .DIRECTORY_SEPARATOR. 'joomla' .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_j4age' .DIRECTORY_SEPARATOR. 'stand.alone.j4age.inc.php');
 *
 * Testing:
 *	  step 1)  Open activation file in web browser, eg:
 *           http://my.domain.com/joomla/components/com_j4age/stand.alone.j4age.inc.php
 *    step 2) If something goes wrong You should see error (if Your PHP settings are set in that way)
 *    step 3) If You see blank page probalby every thing is OK. Go to Joomla administration panel
 *           to JoomlaStats statistics page and chek if You were counted.
 *
 *
 * NOTICE:
 *   If You activate JoomlaStats by using 'Stand Alone' method, some JoomlaStats features will be 
 *   unavailable (like determine if user was logged to Joomla CMS or not)
 */
 

 
//this file must have direct access!! - It is stand alone version!
//defined('_JEXEC') or die ('Direct Access to this location is not allowed.');


/** _JS_STAND_ALONE define tell Us that is stand alone version */
define('_JS_STAND_ALONE', true);
//define('_JEXEC', true);
define('JPATH_BASE', true);

 if (!defined('DIRECTORY_SEPARATOR')) {
	define('DIRECTORY_SEPARATOR', '/');
}
if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
define('JPATH_COMPONENT_ADMINISTRATOR', dirname(__FILE__) .DS. '..' .DS. '..' .DS. 'administrator' .DS. 'components' .DS. 'com_j4age');
define('JPATH_SITE', dirname(__FILE__) .DS. '..' .DS. '..' );



//require_once(JPATH_SITE .DS. 'libraries' .DS. 'joomla' .DS.'base' .DS.'object.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR .DS. 'database' .DS. 'res_joomla' .DS.'object.php');
require_once(JPATH_SITE .DS. 'libraries' .DS. 'joomla' .DS.'environment' .DS.'request.php');
require_once(JPATH_SITE .DS. 'libraries' .DS. 'joomla' .DS.'filter' .DS.'filterinput.php');
require_once(JPATH_SITE .DS. 'libraries' .DS.'loader.php');

//include JoomlaStats count classes
require_once(JPATH_COMPONENT_ADMINISTRATOR .DS. 'libraries' .DS. 'count.classes.php');

//perform count action
$JSCountVisitor = new js_JSCountVisitor();
$js_visit_id = $JSCountVisitor->countVisitor( );
echo '<!-- j4ageActivated -->';

	
