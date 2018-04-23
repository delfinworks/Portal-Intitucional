<?php

              
/**
 * @package j4age
 * @copyright Copyright (C) 2009-@THISYEAR@ j4age Team. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * Thank you to the project j4age and it's team, on which roots this project is build on.
 */
             
          

defined('_JEXEC') or die ('JS: Direct access to this location is not allowed.');

global $j4age_performed;

if($j4age_performed == true)
{
   return;
}

$js_PathToJoomlaStatsCountClasses = JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_j4age' .DS. 'libraries' .DS. 'count.classes.php';
if ( !is_readable($js_PathToJoomlaStatsCountClasses) || !include_once($js_PathToJoomlaStatsCountClasses) ) {
	echo "<strong>j4age</strong> component required, but not installed";
	return false;
}

$JSCountVisitor = new js_JSCountVisitor();
$js_visit_id = $JSCountVisitor->countVisitor( );

$isJ4ageActivated = JRequest::getBool('isJ4ageActivated', false);

if($isJ4ageActivated)
{
    $content .= '<!-- j4ageActivated -->';
}

$j4age_performed = true;