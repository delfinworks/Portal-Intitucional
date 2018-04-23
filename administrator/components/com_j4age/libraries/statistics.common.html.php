<?php

              
/**
 * @package j4age
 * @copyright Copyright (C) 2009-@THISYEAR@ j4age Team. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * Thank you to the project j4age and it's team, on which roots this project is build on.
 */
             
          

if( !defined( '_JS_STAND_ALONE' ) && !defined( '_JEXEC' ) )
{
	die( 'JS: No Direct Access to '.__FILE__ );
}


require_once( dirname(__FILE__) .DS. 'template.html.php' );

/**
 *  This file contain HTML templates that are common for statistics pages
 */

/**
 *  This class hold HTML templates that are used by statistics pages
 *
 *  NOTICE: methods from class JoomlaStats_Engine will be moved here
 */
class js_JSStatisticsCommonTpl
{
	var $task; //@todo remove this member!!!
	//var _JSAdminImagePath - use getUrlPathToJSAdminImages() function to get path to admin images



	/** constructor */
	function __construct() {
	}
	
	/**
	 * A hack to support __construct() on PHP 4
	 *
	 * Hint: descendant classes have no PHP4 class_name() constructors,
	 * so this constructor gets called first and calls the top-layer __construct()
	 * which (if present) should call parent::__construct()
	 *
	 * code from Joomla CMS 1.5.10 (thanks!)
	 *
	 * @access	public
	 * @return	Object
	 * @since	1.5
	 */
	function js_JSStatisticsCommonTpl()
	{
		$args = func_get_args();
		call_user_func_array(array(&$this, '__construct'), $args);
	}	
	
	
	/**
	 * Retrurn url that to directory with JS admin images
	 *
	 * @return string		eg.: "http://127.0.0.1/joomla/administrator/components/com_j4age/images/"
	 */
	function getUrlPathToJSAdminImages() {
		return JURI::base(true) . '/components/com_j4age/images/';
	}

	// style 4 detail view
	function getStyleForDetailView( $aaaa ) {
		return '<span style="font-weight:normal; font-style:italic; color:#007BBD">'.$aaaa.'</span>';
	}
	
	//@depracated - use addSummStyleLine
	function getStyleForSummarizedNumber( $SummarizedNumber ) {
		return '&nbsp;<span style="font-weight:normal; font-style:italic;">[ '.$SummarizedNumber.' ]</span>';
	}

	/**
	 *  Add style to show summarized data 
	 *
	 *  @param bool $show_summarized       - true or false; Could be get from JSConf->show_summarized
	 *  @param any  $data                  - if $show_summarized == true; $data should contain number including summarized data;
	 *  @param any  $data_only_summarized  - if $show_summarized == false; this parameter is not considered; if $show_summarized == true; $data should contain only number of summarized data
	 *
	 *  eg.:
	 *     total 83, summarized 21, current 62
	 *        addSummStyleLine(true, 83, 21)  ->  "83 [21]"
	 *        addSummStyleLine(false, 83, 21)  ->  "83"
	 */
	function addSummStyleLine( $show_summarized, $data, $data_only_summarized ) {
		return $data . ( ($show_summarized==true) ? ('&nbsp;['.$data_only_summarized.']') : '' );
	}
		
	/**
	 *  Add style to show summarized data 
	 *
	 *  @param bool $show_summarized       - true or false; Could be get from JSConf->show_summarized
	 *  @param any  $data                  - if $show_summarized == true; $data should contain number including summarized data;
	 *  @param any  $data_only_summarized  - if $show_summarized == false; this parameter is not considered; if $show_summarized == true; $data should contain only number of summarized data
	 *
	 *  eg.:
	 *     total 83, summarized 21, current 62
	 *        addSummStyleLine(true, 83, 21)  ->  "83 [21]"
	 *        addSummStyleLine(false, 83, 21)  ->  "83"
	 */
	function addSummStyleTable( $show_summarized, $data, $data_only_summarized ) {
		if ( $show_summarized == false )
			return $data;
		
		$html = ''
		. '<table class="adminlist" cellspacing="0" width="100%"><tr>'
		. '<td style="width: 50%; text-align: right;">'.$data.'</td>'
		. '<td style="width: 50%; text-align: left;">['.$data_only_summarized.']</td>'
		. '</tr></table>'
		;
		return $html;
	}
	
	/**
	 * Displays a percentage bar
	 *
	 * @param integer $percent
	 * @param integer $maxpercent
	 * @return string
	 */
	function PercentBar( $percent, $maxpercent )
    {
		$barmaxlength	= 180;
		$barlength		= $maxpercent == 0 ? 0 : (int) ( $percent / $maxpercent * $barmaxlength );
		if ($barlength == 0)
			$barlength = 1;//draw at least 1px bar-on
		$barrest		= ( $barmaxlength - $barlength );

		// draw the filled bar
		$retvar = '<img border="0" src="' . $this->getUrlPathToJSAdminImages() . 'bar-on.gif' . '" width="' . $barlength . '" height="7" alt="" />';
		
		// if there is non-filled bar to draw do so...
		if( $barrest > 0 ) {
			$retvar .= '<img border="0" src="' . $this->getUrlPathToJSAdminImages() . 'bar-off.gif' . '" width="' . $barrest . '" height="7" alt="" />';
		}

		return $retvar;
	}


	/** this function format percentages from double to string
	 *
	 *  Examples:
	 *     getFormatedPercentages(0.543054) -> '54.30' (not '54.3')
	 *
	 *  @param $percent like 0.4350363
	 *  return formated string '43.50' (not '43.5')
	 */
	function getFormatedPercentages($percent) {
		//$per_cent_format_token = '%01.0f';
		$per_cent_format_token = '%01.1f';
		//$per_cent_format_token = '%01.2f';
												
		return sprintf($per_cent_format_token, $percent * 100);
	}
		
	/**
	 * Displays a percentage bar
	 *
	 * @param integer $percent
	 * @param integer $maxpercent
	 * @return string
	 */
	function getPercentBarWithPercentNbr( $value, $max_value, $sum_all_values ) {
		
		//$percent = round( ( ( $row->os_visits / $sum_all_system_visits ) *100 ), 2 );
		//$totalmaxpercent	= round( ( ( $max_system_visits / $sum_all_system_visits ) *100 ), 2 );
		
		$PercentBar = $this->PercentBar( $value, $max_value );
        
		$PercentNbr = ($sum_all_values == 0) ? 0 : $this->getFormatedPercentages( $value / $sum_all_values );
		
		/** in IE 6.0, 7.0 style not working so use html tag cellspacing="0" */
		$retvar = '
			<table style="width: 100%; border-width: 0px; border-collapse: collapse; border-spacing: 0px;" cellspacing="0">
			<tr>
				<td style="height: auto; padding: 0px; border-width: 0px; text-align: left; white-space: nowrap;">'.$PercentBar.'</td>
				<td style="height: auto; padding: 0px; padding-left: 7px; border-width: 0px; text-align: right;">'.$PercentNbr.'%</td>
			</tr>
			</table>
		';
		
		return $retvar;
	}		

	/**
	 * This function return Statistics Page Header HTML
	 *
	 * @param unknown_type $FilterSearch
	 * @param unknown_type $FilterDate
	 * @param unknown_type $vid
	 * @param unknown_type $moreinfo
	 * @param unknown_type $FilterDomain
	 * @param unknown_type $task				mic: removed: switched to this->task
	 * @param unknown_type $ReportTitle
	 * @param unknown_type $StatisticsMenu
	 * @return string
	 *
	 */
	function renderFilters( &$FilterSearch, &$FilterDate, $vid, $moreinfo, &$FilterDomain, $show_typefilter = false, $show_timefilter = null ) {
        require_once( dirname( __FILE__ ) .DS. 'template.html.php' );

        if(!is_null($show_timefilter))
        {
            $FilterDate->hide = !$show_timefilter;
        }
        $afilter = trim(JRequest::getVar('afilter', ''));
        echo js_JSTemplate::startBlock();
        ?>

		<!-- hidden value for display stats -->
		<input type="hidden" name="vid" value="<?php echo $vid;?>" />
        <input type="hidden" name="moreinfo" value="<?php echo $moreinfo;?>" />
		<?php echo $FilterSearch->getHtmlSearchFilterHiddenCode() ;?>
		<?php echo $FilterDomain->getHtmlDomainFilterHiddenCode();?>

        <?php if(!$FilterDate->hide || $FilterSearch->show_search_filter || $FilterDomain->show_domain_filter) {?>
                    <table border="0" cellspacing="0" width="100%">
                        <!-- 1st row: Logo + date selection -->
                        <tr>
                            <td width="100%" class="sectionname" style="vertical-align: middle;">
                                <?php
                                    if(!empty($afilter))
                                    {
                                      ?>
                                        <input type="button" name="skipFilter" id="skipFilter" onClick="document.adminForm.afilter.value='';document.adminForm.method='GET';document.adminForm.submit()" value="<?php echo JTEXT::_('Drop filter');?>" />
                                      <?php
                                    }
                                ?>
                                <?php
                                    if($show_typefilter)
                                    {
                                        $objtype = JRequest::getInt('objtype', -1);
                                      ?>
                                         <input type="radio" name="objtype" value="-1" id="typeall" onchange="document.adminForm.method='GET';document.adminForm.submit()" <?php echo $objtype == -1 ? 'checked="checked"':'';?>/><label for="typeall"><?php echo JTEXT::_('All');?></label>
                                         <input type="radio" name="objtype" value="1"  id="typeregular" onchange="document.adminForm.method='GET';document.adminForm.submit()" <?php echo $objtype == 1 ? 'checked="checked"':'';?>/><label for="typeregular"><?php echo JTEXT::_('Regular');?></label>
                                         <input type="radio" name="objtype" value="2"  id="typebots" onchange="document.adminForm.method='GET';document.adminForm.submit()" <?php echo $objtype == 2 ? 'checked="checked"':'';?>/><label for="typebots"><?php echo JTEXT::_('Bots');?></label>
                                         <input type="radio" name="objtype" value="3"  id="typeubots" onchange="document.adminForm.method='GET';document.adminForm.submit()" <?php echo $objtype == 3 ? 'checked="checked"':'';?>/><label for="typeubots"><?php echo JTEXT::_('Unknown Bots');?></label>
                                         <input type="radio" name="objtype" value="0"  id="typeunknown" onchange="document.adminForm.method='GET';document.adminForm.submit()" <?php echo $objtype == 0 ? 'checked="checked"':'';?>/><label for="typeunknown"><?php echo JTEXT::_('Unknown');?></label>
                                       <?php
                                    }
                                ?>
                            </td>
                            <td nowrap="nowrap">
                            <?php
                                    echo $FilterSearch->getHtmlSearchFilterVisibleCode();
                                    echo $FilterDomain->getHtmlDomainFilterVisibleCode();
                            ?>
                            <?php echo JTEXT::_( 'Filter' );?>
                            <?php $FilterDate->render();?>
                            </td>
                        </tr>
                    </table>
            <?php } ;?>
		<?php
        echo js_JSTemplate::endBlock();
	}
}
