<?php
/**
* @version		$Id: upgrade.php 2009-12-05 vinaora $
* @package		VINAORA VISITORS COUNTER
* @copyright	Copyright (C) 2007 - 2010 VINAORA. All rights reserved.
* @license		GNU/GPL
* @website		http://vinaora.com
* @email		admin@vinaora.com
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<?php
class extVisitCounterUpgrade
{
	/*
	** Get Number of Visits from Old data (#__vvisitcounter table)
	*/
	/* ------------------------------------------------------------------------------------------------ */
	function &getVisits( $timestart = 0, $timestop = 0 ){
	
		$db			=	&JFactory::getDBO();
		
		$timestart	=	(int) $timestart;
		$timestop	=	(int) $timestop;
		$visits		=	0;

		if ( $timestart < BIRTH_DAY_JOOMLA ) $timestart = 0;
		if ( $timestop < BIRTH_DAY_JOOMLA ) $timestop = 0;
		
		$query		=	' SELECT COUNT(*) FROM #__vvisitcounter ';
		
		if ( !$timestart ){
			if ( $timestop ) {
				$query	.=	" WHERE tm < $timestop ";
			}
		}
		else{
			if ( !$timestop ){
				$query		.= " WHERE tm >= $timestart ";
			}
			else{
				if ( $timestop == $timestart ){
					return 0;
				}
				else{
					$query		.= " WHERE tm >= $timestart ";
					$query		.= " AND tm < $timestop ";						
				}
			}
		}
		
		$db->setQuery($query);
		$visits	=	$db->loadResult();
		
		if ( $db->getErrorNum() ) {
			JError::raiseWarning( 500, $db->stderr() );
		}
		
		return $visits;
	}
	/* ------------------------------------------------------------------------------------------------ */
}