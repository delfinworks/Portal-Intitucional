<?php defined('_JEXEC') or die('JS: No Direct Access');
?>

<div style="font-size: 1px;">&nbsp;</div><!-- This div is needed to show content of tab correctly in \'IE 7.0\' in \'j1.5.6 Legacy\'. Tested in: FF, IE, j1.0.15, j1.5.6 and works OK -->
		<table class="adminform" width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
		        <td>
					<b><?php echo JTEXT::_( 'Optimize j4age database' ); ?></b><br/>
					<?php echo JTEXT::_( 'Optimize j4age database - DETAILED DESCRIPTION' ); ?><br/>
					<br/>
		            <input type="button" name="optimize_database" style="width:165px" value="<?php echo  JTEXT::_( 'Optimize database' ); ?>" onclick="submitbutton('doOptimizeDatabase');" />
		        </td>
			</tr>
            <tr>
                <td>
                    <b><?php echo JTEXT::_( 'Drop statistics' ); ?></b><br/>
                    <?php echo JTEXT::_( 'Delete all captured statistic values older then the selected period' ); ?><br/>
                    <br/>
                    <input type="button" name="optimize_database" style="width:165px" value="<?php echo  JTEXT::_( 'Drop statistics' ); ?>" onclick="submitbutton('doDropOldData');" />
                    <select name="periodIndays" >
                        <option value="7-days" <?php echo ($this->periodIndays == 7 ? 'selected="true"' : '' );?>><?php echo JText::_("Older then 7 days");?></option>
                        <option value="14-days" <?php echo ($this->periodIndays == 14 ? 'selected="true"' : '' );?>><?php echo JText::_("Older then 14 days");?></option>
                        <option value="30-days" <?php echo ($this->periodIndays == 30 ? 'selected="true"' : '' );?>><?php echo JText::_("Older then 30 days");?></option>
                        <option value="60-days" <?php echo ($this->periodIndays == 60 ? 'selected="true"' : '' );?>><?php echo JText::_("Older then 60 days");?></option>
                        <option value="90-days" <?php echo ($this->periodIndays == 90 ? 'selected="true"' : '' );?>><?php echo JText::_("Older then 90 days");?></option>
                        <option value="365-days" <?php echo ($this->periodIndays == 365 ? 'selected="true"' : '' );?>><?php echo JText::_("Older then 365 days");?></option>
                        <option value="730-days" <?php echo ($this->periodIndays == 730 || empty($this->periodIndays)  ? 'selected="true"' : '' );?>><?php echo JText::_("Older then 730 days");?></option>
                    </select>
                </td>
            </tr>
		</table>