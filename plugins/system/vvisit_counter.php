<?php
/**
* @version		$Id: vvisit_counter.php 2009-12-05 vinaora $
* @package		VINAORA VISITORS COUNTER
* @copyright	Copyright (C) 2007 - 2010 VINAORA. All rights reserved.
* @license		GNU/GPL
* @website		http://vinaora.com
* @email		admin@vinaora.com
*
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<?php

jimport( 'joomla.plugin.plugin');

/**
* Vinaora Visitors Counter Plugin
*
* @package 		VINAORA VISITORS COUNTER
* @subpackage	System
*/
class plgSystemVVisit_Counter extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param	object		$subject The object to observe
	 * @param 	array  		$config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function plgSystemVVisit_Counter( &$subject, $config )  {
		parent::__construct( $subject, $config );
		
		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'System', 'vvisit_counter' );
		//$this->_params = new JParameter( $this->_plugin->params );
		
		//load the translation
		$this->loadLanguage( );

	}
	function onAfterInitialise(){
		
		// Get Lifetime / Session Time from Global Configuration of Joomla!
		$config			=	&JFactory::getConfig();
		$lifetime		=	(int) $config->getValue('config.lifetime');
		$offset			=	(float) $config->getValue('config.offset');
		
		// Get duration to insert new log. Default = 15 minutes / 900 seconds
		$vlifetime		=	$this->params->get('session_time', 15);
		$vlifetime		=	min( $vlifetime,$lifetime );
		$vlifetime		*=	60;
		
		// Determine Date/Time Now
		$now		=	mktime();
		$hour_start	=	$now - ( $now % 3600 );
				
		$vvisit_path	=	JPATH_ADMINISTRATOR.DS."components".DS."com_vvisit_counter".DS."helpers";
		$vvisit_exists	=	file_exists( $vvisit_path.DS."vinaora_visitors_counter.php" );

		if ( $vvisit_exists ) {
			require_once ( $vvisit_path.DS."vinaora_visitors_counter.php" );
		}
		else{
			return;
		}
		
		// Get Last Time in the table #__vvcounter_logs
		$ltime	=	extVinaoraVisitorsCounter::lastTimeLog();
		
		if ( !$ltime ){
			$visits	=	extVinaoraVisitorsCounter::getVisitsOnline( $vlifetime );
			extVinaoraVisitorsCounter::insertLog( $now, $visits );
			
			// Update $ltime
			$ltime	=	$now;
		}
		else{
			if ( $ltime<$hour_start ){
			
				// Get visits from Session and insert new Log
				$visits	=	extVinaoraVisitorsCounter::getVisitsFromSession( $ltime+1, $hour_start );
				extVinaoraVisitorsCounter::insertLog( $hour_start, $visits );
				$ltime	=	$hour_start;
			}
			
			// Update $now
			$now	=	mktime();
			
			if ( $now>=$ltime+$vlifetime ){
				$visits	=	extVinaoraVisitorsCounter::getVisitsFromSession( $ltime+1 );
				extVinaoraVisitorsCounter::insertLog( $now, $visits );
				$ltime	=	$now;
			}
		}
	}
}
?>