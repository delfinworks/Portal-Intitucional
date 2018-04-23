<?php
/**
 * @package JoomlaStats
 * @copyright Copyright (C) 2004-2009 JoomlaStats Team. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

//ensure this file is being included by a parent file
defined('_JEXEC') or die ('JS: Direct Access to this location is not allowed.');

require_once($mainframe->getPath('front_html', 'com_j4age'));

$task = JRequest::getVar( 'task', '' );

switch ($task)
{
	default:
        $mainframe = JFactory::getApplication();
		$mainframe->SetPageTitle( "Not yet implemented" ); // Dynamic Page Title
		HTML_joomlastats::defaultmessage();
		break;
}


