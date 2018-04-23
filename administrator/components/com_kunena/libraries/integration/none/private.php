<?php
/**
 * @version $Id: private.php 4336 2011-01-31 06:05:12Z severdia $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
//
// Dont allow direct linking
defined( '_JEXEC' ) or die('');

class KunenaPrivateNone extends KunenaPrivate
{
	public function __construct() {
		$this->priority = 5;
	}

	public function getOnClick($userid) {}

	public function getURL($userid) {}
}
