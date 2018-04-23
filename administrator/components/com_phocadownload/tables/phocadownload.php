<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include library dependencies
jimport('joomla.filter.input');

class TablePhocaDownload extends JTable
{

	var $id 				= null;
	var $catid 				= null;
	var $owner_id 			= null;
	var $sectionid 			= null;
	var $sid 				= null;
	var $title 				= null;
	var $alias 				= null;
	var $filename 			= null;
	var $filesize 			= 0;
	var $filename_play 		= null;
	var $filename_preview 	= null;
	var $version	 		= null;
	var $directlink			= null;
	var $author 			= null;
	var $author_url 		= null;
	var $author_email 		= null;
	var $license 			= null;
	var $license_url 		= null;
	var $image_filename 	= null;
	var $image_filename_spec1 = null;
	var $image_filename_spec2 = null;
	var $image_download 	= null;
	var $link_external 		= null;
	var $description 		= null;
	var $textonly			= 0;
	var $date 				= null;
	var $publish_up 		= null;
	var $publish_down 		= null;
	var $hits 				= null;
	var $published 			= null;
	var $approved 			= null;
	var $checked_out 		= 0;
	var $checked_out_time 	= 0;
	var $ordering 			= null;
	var $access				= null;
	var $confirm_license	= null;
	var $unaccessible_file	= null;
	var $params 			= null;
	var $metakey			= null;
	var $metadesc			= null;

	function __construct(& $db) {
		parent::__construct('#__phocadownload', 'id', $db);
	}
	
	function check()
	{
		// check for valid name
		if (trim( $this->filename ) == '') {
			$this->setError( JText::_( 'FILE MUST HAVE A FILENAME') );
			return false;
		}
		
		if(empty($this->title)) {
			$this->title = PhocaDownloadHelper::getTitleFromFilenameWithoutExt($this->filename);
		}

		if(empty($this->alias)) {
			$this->alias = $this->title;
		}
		
		$this->alias = PhocaDownloadHelper::getAliasName($this->alias);

		return true;
	}
}
?>