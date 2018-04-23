<?php

              
/**
 * @package j4age
 * @copyright Copyright (C) 2009-@THISYEAR@ j4age Team. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * Thank you to the project j4age and it's team, on which roots this project is build on.
 */
             
          

if( !defined( '_JEXEC' ) ) {
	die( 'JS: No Direct Access to '.__FILE__ );
}

$JSTemplate = new js_JSTemplate();
$dbaccess =& js_JSDatabaseAccess::getInstance();

?>

<div style="text-align: left;">
	<!--div style="width:95%; border: 1px solid #EFEFEF; margin:5px auto 5px auto; padding:5px;"-->
	    <div style="text-align:center">
	      <h3>Credits</h3>
	      <p><strong>j4age</strong> is an almost rewritten component based on the hard work of the JoomlaStats Team.</p>
	      <h3>Special Credits</h3>
	      <p>Special thanks to Robert Borlet, Head of JoomlaStats, to which this component is dedicated!</p>
	    </div>
		<table style="width: 100%; padding: 0px; border-width: 0px; border-collapse: collapse; /*not working in IE 6.0, 7.0 use cellspacing=0 */ border-spacing: 0px; /* no difference */" cellspacing="0">
		<tr>
			<td style="padding: 0px; text-align: left;"></td>
			<td style="padding: 0px; text-align: right; vertical-align: top; font-weight: bold;">
				j4age version:&nbsp;
				<!-- JoomlaStats build version: '<?php echo $this->JSConf->BuildVersion; ?>' -->
				<?php
					if (strpos($this->JSConf->JSVersion, ' ') === false) {
						//for release, cut the build number (last digits) from version number
						$pos = strrpos($this->JSConf->JSVersion, '.');
						if ($pos === false) {
							//somethings goes wrong, echo all
							echo $this->JSConf->JSVersion;
						} else {
							echo substr($this->JSConf->JSVersion, 0, $pos);
						}
					} else {
						echo $this->JSConf->JSVersion;
					}
				?>
			</td>
		</tr>
		</table>
		<?php $JSTemplate->startBlock();?>
			<br/>
			<table width="550" align="center" style="border: 1px solid #CCCCCC; background-color: #F5F5F5;">
			<tr>
				<td colspan="4" style="font-weight:bold; text-align:center;"><?php echo JTEXT::_( 'Database Summary' );?><hr /></td>
            </tr>
			<tr>
				<td width="220"><?php echo JTEXT::_( 'Spider/Bots' );?></td>
				<td width="150" align="left"><?php echo $this->StatusTData->totalbots;?></td>
				<td width="220" align="left"><?php echo JTEXT::_( 'Visited pages' );?></td>
				<td width="150" align="left"><?php echo $this->StatusTData->totalpages;?></td>
			</tr>
			<tr>
				<td><?php echo JTEXT::_( 'Search engines' );?></td>
				<td align="left"><?php echo $this->StatusTData->totalse;?></td>
				<td align="left"><?php echo JTEXT::_( 'Referrer' );?></td>
				<td align="left"><?php echo $this->StatusTData->totalpagereferrer;?></td>
			</tr>
			<tr>
				<td><?php echo JTEXT::_( 'Visitor OS' );?></td>
				<td align="left"><?php echo $this->StatusTData->totalsys;?></td>
				<td align="left"><?php echo JTEXT::_( 'Visitor' );?></td>
				<td align="left"><?php echo $this->StatusTData->totalvisits;?></td>
			</tr>
			<tr>
				<td><?php echo JTEXT::_( 'Countries' );?></td>
				<td align="left"><?php echo $this->StatusTData->totaltld;?></td>
                <td align="left"><?php echo JTEXT::_( 'Total Size' );?></td>
                <td align="left"><?php echo round( ( ( $this->StatusTData->dbsize / 1024 ) / 1024 ), 2 );?>MB</td>
			</tr>
            <tr>
                <td colspan="4" style="font-weight:bold; text-align:center;"><?php echo JTEXT::_( 'Other Details' );?><hr /></td>
            </tr>
            <tr>
                <td><?php echo JTEXT::_( 'GMT Date (J4age)' );?></td>
                <td align="left"><?php echo js_gmdate("j.n.Y H:i:s");?></td>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td><?php echo JTEXT::_( 'GMT Date' );?></td>
                <td align="left"><?php echo gmdate("j.n.Y H:i:s");?></td>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td><?php echo JTEXT::_( 'System Date' );?></td>
                <td align="left"><?php echo date("j.n.Y H:i:s");?></td>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="4" style="font-weight:bold; text-align:center;"><?php echo JTEXT::_( 'DB Table Collection Info' );?><hr /></td>
            </tr>
            <?php
             $columnsOfTable = $dbaccess->js_getTableColumns("#__jstats_keywords" );
             ;?>
            <?php $columnCollectionUTF = $dbaccess->js_getColumnCollation( $columnsOfTable, 'keywords' ) ; $isUTFType = ( strpos(strtolower($columnCollectionUTF), 'utf8') !== false);?>
            <tr>
                <td><?php echo JTEXT::_( '#__jstats_keywords->keywords' );?></td>
                <td align="left"><?php echo $columnCollectionUTF;?></td>
                <td colspan="2"><?php echo (( $isUTFType != true ) ? ("<span  style='color:red'>(UTF-8 recommended)</span>") : '' );?></td>
            </tr>

            <tr>
                <td colspan="4" style="font-weight:bold; text-align:center;"><?php echo JTEXT::_( 'System Info' );?><hr /></td>
            </tr>
            <tr>
                <td><?php echo JTEXT::_( 'PHP Version' );?></td>
                <td align="left"><?php echo phpversion();?></td>
                <td colspan="2"><?php echo ( js_JSUtil::JSVersionCompare( phpversion(), '5.0.0', '<') ? ("<span  style='color:red'>(Not officially Supported)</span>") : '' );?></td>
            </tr>
            <?php

            $dbversion = $dbaccess->db->getVersion();
            ;?>
                
                <tr>
                    <td><?php echo JTEXT::_( 'PHP Version' );?></td>
                    <td align="left"><?php echo phpversion();?></td>
                    <td colspan="2"><?php echo ( js_JSUtil::JSVersionCompare( phpversion(), '5.0.0', '<') ? ("<span  style='color:red'>(Not officially Supported)</span>") : '' );?></td>
                </tr>
                <tr>
                    <td><?php echo JTEXT::_( 'DB Version' );?></td>
                    <td align="left"><?php echo $dbversion;?></td>
                    <td colspan="2"><?php echo ( js_JSUtil::JSVersionCompare( $dbversion, '4.0.0', '<') ? ("<span  style='color:red'>(Not officially Supported)</span>") : '' );?></td>
                </tr>
                <tr>
                    <td><?php echo JTEXT::_( 'PHP Max. Execution Time' );?></td>
                    <td align="left"><?php echo ini_get('max_execution_time');?>s</td>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td><?php echo JTEXT::_( 'PHP Max. SQL Connection Time' );?></td>
                    <td align="left"><?php echo ini_get('mysql.connect_timeout');?>s</td>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td><?php echo JTEXT::_( 'PHP Max. Input Time' );?></td>
                    <td align="left"><?php echo ini_get('max_input_time');?>s</td>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td><?php echo JTEXT::_( 'PHP Memory Limit' );?></td>
                    <td align="left"><?php echo ini_get('memory_limit');?></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td><?php echo JTEXT::_( 'MySQLi Interative Timeout' );?></td>
                    <td align="left"><?php echo (defined('MYSQLI_CLIENT_INTERACTIVE')?MYSQLI_CLIENT_INTERACTIVE:'-');?>s</td>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td><?php echo JTEXT::_( 'MySQLi Connection Connections' );?></td>
                    <td align="left"><?php echo (defined('MYSQLI_OPT_CONNECT_TIMEOUT')?MYSQLI_OPT_CONNECT_TIMEOUT:'-');?>s</td>
                    <td colspan="2">&nbsp;</td>
                </tr>

			</table>
			<br />
		<?php $JSTemplate->endBlock();?>
	<!--/div-->

	<div style="clear:both; margin-top:15px"></div>

</div>


