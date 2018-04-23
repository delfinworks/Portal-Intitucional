<?php
/**
 * @package JoomlaStats
 * @copyright Copyright (C) 2004-2009 JoomlaStats Team. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// no direct access
if( !defined( '_JEXEC' ) && !defined( '_JS_STAND_ALONE' ) ) {
	die( 'JS: No Direct Access to '.__FILE__ );
}

require_once(JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_j4age' .DS. 'libraries'.DS. 'count.classes.php');

$JSCountVisitor = new js_JSCountVisitor();
$js_visit_id = $JSCountVisitor->countVisitor( );
echo '<!-- j4ageActivated -->';



