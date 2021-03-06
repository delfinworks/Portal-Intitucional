<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevuser.php 1399 2009-03-30 08:31:52Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class JElementJevdate extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'JEVDate';

	function fetchElement($name, $value, &$node, $control_name)
	{

		// Must load admin language files
		$lang =& JFactory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);
		$lang->load("com_jevents", JPATH_SITE);

		JLoader::register('JEVHelper',JPATH_SITE."/components/com_jevents/libraries/helper.php");
		$option = "com_jevents"; 
		$params =& JComponentHelper::getParams( $option );
		$minyear = $params->get("com_earliestyear",1970);
		$maxyear = $params->get("com_latestyear",2150);
		ob_start();
		JEVHelper::loadCalendar11($control_name.'['.$name.']', $control_name.$name, $value,$minyear, $maxyear, '',"", 'Y-m-d');
		return ob_get_clean();
	}
}