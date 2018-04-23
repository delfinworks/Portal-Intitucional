<?php
/**
* @version		$Id: controller.php 2009-12-05 vinaora $
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

jimport('joomla.application.component.controller');
 
/**
 * Vinaora Visitors Counter Component Controller
 *
 * @package    Vinaora.Visitors.Counter
 * @subpackage Components
 */
class VVisitCounterController extends JController
{
    /**
     * Method to display the view
     *
     * @access    public
     */
    function display()
    {
        parent::display();
    }
 
}
?>