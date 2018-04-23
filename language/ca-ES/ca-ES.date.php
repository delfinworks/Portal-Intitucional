<?php
/**
* @version	$Id: ca-ES.date.php 2008-11-11 dverger $
* @package	Catalan Language Pack
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * JDateca_ES overrides JDate class
 *
 * @author	Damià Verger <dverger@joomla.cat>
 *
 * @package	Catalan Language Pack
 * @since	1.5.8
 */

class JDateca_ES extends JDate
{
	/**
	 * Gets the date in a specific format
	 *
	 * @access protected
	 * @param string $format The date format specification string (see {@link PHP_MANUAL#strftime})
	 * @param int $time Unix timestamp
	 * @return string a date in a specific format
	 */
	function _strftime($format, $time) 
	{
		if (date('n',$time) == 4 || date('n',$time) == 8 || date('n',$time) == 10) // abril, agost o octubre
		{
			if(strpos($format, 'de %B') !== false)
				$format = str_replace('de %B','d\'%B',$format);
			if(strpos($format, 'de %b') !== false)
				$format = str_replace('de %b','d\'%b',$format);
		}

		if(strpos($format, '%a') !== false)
			$format = str_replace('%a', $this->_dayToString(date('w', $time), true), $format);
		if(strpos($format, '%A') !== false) 
			$format = str_replace('%A', $this->_dayToString(date('w', $time)), $format);
		if(strpos($format, '%b') !== false)
			$format = str_replace('%b', $this->_monthToString(date('n', $time), true), $format);
		if(strpos($format, '%B') !== false)
			$format = str_replace('%B', $this->_monthToString(date('n', $time)), $format);

		if(strpos($format, '%e') !== false)
			$format = str_replace('%e', date('j', $time), $format);
	
		$date = strftime($format, $time);
		return $date;
	}
}
