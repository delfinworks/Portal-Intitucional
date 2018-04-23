<?php defined('_JEXEC') or die('JS: No Direct Access');
$JSTemplate = new js_JSTemplate();
		?>
		<div style="font-size: 1px;">&nbsp;</div>
		<?php /*<!-- This div is needed to show content of tab correctly in 'IE 7.0' in 'j1.5.6 Legacy'. Tested in: FF, IE, j1.0.15, j1.5.6 and works OK --> */ ?>
		<table class="adminform" width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td nowrap="nowrap"><?php echo JTEXT::_( 'Onlinetime' ); ?> <?php echo JTEXT::_( 'Visitors' ); ?></td>
			<td>
				<select name="onlinetime">
				<?php
				echo '<option value=  "10"'. ($this->JSConf->onlinetime ==   10 ? ' selected="selected"' : '') .'>10 '. JTEXT::_( 'Min' ) . '</option>' . "\n";
				echo '<option value=  "15"'. ($this->JSConf->onlinetime ==   15 ? ' selected="selected"' : '') .'>15 '. JTEXT::_( 'Min' ) . '</option>' . "\n";
				echo '<option value=  "20"'. ($this->JSConf->onlinetime ==   20 ? ' selected="selected"' : '') .'>20 '. JTEXT::_( 'Min' ) . '</option>' . "\n";
				echo '<option value=  "25"'. ($this->JSConf->onlinetime ==   25 ? ' selected="selected"' : '') .'>25 '. JTEXT::_( 'Min' ) . '</option>' . "\n";
				echo '<option value=  "30"'. ($this->JSConf->onlinetime ==   30 ? ' selected="selected"' : '') .'>30 '. JTEXT::_( 'Min' ) . '</option>' . "\n";
				echo '<option value=  "60"'. ($this->JSConf->onlinetime ==   60 ? ' selected="selected"' : '') .'>60 '. JTEXT::_( 'Min' ) . '</option>' . "\n";
				echo '<option value=  "90"'. ($this->JSConf->onlinetime ==   90 ? ' selected="selected"' : '') .'>90 '. JTEXT::_( 'Min' ) . '</option>' . "\n";
				echo '<option value= "120"'. ($this->JSConf->onlinetime ==  120 ? ' selected="selected"' : '') .'>2 '. JTEXT::_( 'Hrs' ) . '</option>' . "\n";
				echo '<option value= "240"'. ($this->JSConf->onlinetime ==  240 ? ' selected="selected"' : '') .'>4 '. JTEXT::_( 'Hrs' ) . '</option>' . "\n";
				echo '<option value= "480"'. ($this->JSConf->onlinetime ==  480 ? ' selected="selected"' : '') .'>8 '. JTEXT::_( 'Hrs' ) . '</option>' . "\n";
				echo '<option value= "720"'. ($this->JSConf->onlinetime ==  720 ? ' selected="selected"' : '') .'>12 '. JTEXT::_( 'Hrs' ) . '</option>' . "\n";
                echo '<option value="1440"'. ($this->JSConf->onlinetime == 1440 ? ' selected="selected"' : '') .'>24 '. JTEXT::_( 'Hrs' ) . '</option>' . "\n";
                echo '<option value="0"'. ($this->JSConf->onlinetime == 0 ? ' selected="selected"' : '') .'>'. JTEXT::_( 'Infinity' ) . '</option>' . "\n";
                ?>
				</select>
			</td>
			<td width="100%">
				<?php
				echo $JSTemplate->jsToolTip( JTEXT::_( 'Timeout in minutes' ) ); ?>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap"><?php echo JTEXT::_( 'Onlinetime' ); ?> <?php echo JTEXT::_( 'Bots' ); ?></td>
			<td>
				<select name="onlinetime_bots">
				<?php
				echo '<option value=  "10"'. ($this->JSConf->onlinetime_bots ==   10 ? ' selected="selected"' : '') .'>10 '. JTEXT::_( 'Min' ) . '</option>' . "\n";
				echo '<option value=  "15"'. ($this->JSConf->onlinetime_bots ==   15 ? ' selected="selected"' : '') .'>15 '. JTEXT::_( 'Min' ) . '</option>' . "\n";
				echo '<option value=  "20"'. ($this->JSConf->onlinetime_bots ==   20 ? ' selected="selected"' : '') .'>20 '. JTEXT::_( 'Min' ) . '</option>' . "\n";
				echo '<option value=  "25"'. ($this->JSConf->onlinetime_bots ==   25 ? ' selected="selected"' : '') .'>25 '. JTEXT::_( 'Min' ) . '</option>' . "\n";
				echo '<option value=  "30"'. ($this->JSConf->onlinetime_bots ==   30 ? ' selected="selected"' : '') .'>30 '. JTEXT::_( 'Min' ) . '</option>' . "\n";
				echo '<option value=  "60"'. ($this->JSConf->onlinetime_bots ==   60 ? ' selected="selected"' : '') .'>60 '. JTEXT::_( 'Min' ) . '</option>' . "\n";
				echo '<option value=  "90"'. ($this->JSConf->onlinetime_bots ==   90 ? ' selected="selected"' : '') .'>90 '. JTEXT::_( 'Min' ) . '</option>' . "\n";
				echo '<option value= "120"'. ($this->JSConf->onlinetime_bots ==  120 ? ' selected="selected"' : '') .'>2 '. JTEXT::_( 'Hrs' ) . '</option>' . "\n";
				echo '<option value= "240"'. ($this->JSConf->onlinetime_bots ==  240 ? ' selected="selected"' : '') .'>4 '. JTEXT::_( 'Hrs' ) . '</option>' . "\n";
				echo '<option value= "480"'. ($this->JSConf->onlinetime_bots ==  480 ? ' selected="selected"' : '') .'>8 '. JTEXT::_( 'Hrs' ) . '</option>' . "\n";
				echo '<option value= "720"'. ($this->JSConf->onlinetime_bots ==  720 ? ' selected="selected"' : '') .'>12 '. JTEXT::_( 'Hrs' ) . '</option>' . "\n";
                echo '<option value="1440"'. ($this->JSConf->onlinetime_bots == 1440 ? ' selected="selected"' : '') .'>24 '. JTEXT::_( 'Hrs' ) . '</option>' . "\n";
                echo '<option value="0"'. ($this->JSConf->onlinetime_bots == 0 ? ' selected="selected"' : '') .'>'. JTEXT::_( 'Infinity' ) . '</option>' . "\n";
                ?>
				</select>
			</td>
			<td width="100%">
				<?php
				echo $JSTemplate->jsToolTip( JTEXT::_( 'Timeout in minutes' ) ); ?>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap"><?php echo JTEXT::_( 'Startoption' ); ?></td>
			<td>
				<select name="startoption">
					<?php

					$select_html = '';

					$MenuArrIdAndText = $this->statisticsCommon->MenuArrIdAndText;
					foreach( $MenuArrIdAndText as $id => $menu_config ) {
						$select_html .= '<option value="'.$id.'"'. ( $this->JSConf->startoption == $id ? ' selected="selected"' : '' ) . '>'
						. $menu_config['label'] . '</option>' . "\n";
					}

					echo $select_html;
					?>
				</select>
			</td>
			<td>
				<?php
				echo $JSTemplate->jsToolTip( JTEXT::_( 'Startoption for j4age' ) ); ?>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap"><?php echo JTEXT::_( 'Start by day or month' ); ?></td>
			<td>
				<select name="startdayormonth">
					<?php
					echo "<option value='d'". ( $this->JSConf->startdayormonth == 'd' ? ' selected="selected"' : '' ) .'>'
					. JTEXT::_( 'Day' ) .'</option>' ."\n";
					echo "<option value='m'". ( $this->JSConf->startdayormonth == 'm' ? ' selected="selected"' : '' ) .'>'
					. JTEXT::_( 'Month' ) .'</option>' ."\n";
					?>
				</select>
			</td>
			<td>
				<?php
				echo $JSTemplate->jsToolTip( JTEXT::_( 'Select first view' ) ); ?>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap"><label for="enable_whois"><?php echo JTEXT::_( 'WHOIS Support' ); ?></label></td>
			<td>
				<input type="checkbox" name="enable_whois" id="enable_whois" <?php echo ( $this->JSConf->enable_whois ? ' checked="checked"' : '' ); ?> />
			</td>
			<td>
				<?php
				echo $JSTemplate->jsToolTip( JTEXT::_( 'Do a WHOIS query' ) ); ?>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap"><label for="enable_i18n"><?php echo JTEXT::_( 'I18n Support' ); ?></label></td>
			<td>
				<input type="checkbox" name="enable_i18n" id="enable_i18n" <?php echo ( $this->JSConf->enable_i18n ? ' checked="checked"' : '' ); ?> /></td>
			<td>
				<?php
				echo $JSTemplate->jsToolTip( JTEXT::_( 'Multiple translations as one' ) ); ?>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap"><label for="show_charts_within_reports"><?php echo JTEXT::_( 'Integrate Charts within Reports' ); ?></label></td>
			<td>
				<input type="checkbox" name="show_charts_within_reports" id="show_charts_within_reports" <?php echo ( $this->JSConf->show_charts_within_reports ? ' checked="checked"' : '' ); ?> /></td>
			<td>
				<?php
				echo $JSTemplate->jsToolTip( JTEXT::_( 'If switched on, some charts are shown within specific reports. Please switch off if you experience performance issues as this might cause additional SQL enquires!' ) ); ?>
			</td>
		</tr>
		</table>
