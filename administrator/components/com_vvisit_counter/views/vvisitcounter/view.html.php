<?php
/**
* @version		$Id: view.html.php 2009-12-05 vinaora $
* @package		VINAORA VISITORS COUNTER
* @copyright	Copyright (C) 2007 - 2010 VINAORA. All rights reserved.
* @license		GNU/GPL
* @website		http://vinaora.com
* @email		admin@vinaora.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access'); 
?>
<?php

jimport( 'joomla.application.component.view');
 
/**
 * HTML View class for the VVisitCounter Component
 *
 * @package    Vinaora.Visitors.Counter
 */
 
class VVisitCounterViewVVisitCounter extends JView
{
    /**
     * VVisitCounter view display method
     * @return void
     **/
    function display($tpl = null)
    {
        JToolBarHelper::title( JText::_( 'Vinaora Visitors Counter' ), 'generic.png' );
        parent::display($tpl);
    }
}

?>