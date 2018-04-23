<?php
defined('_JEXEC') or die('Restricted access');

$cfg	 = & JEVConfig::getInstance();

$data = $this->datamodel->getWeekData($this->year, $this->month, $this->day);

$option = JEV_COM_COMPONENT;
$Itemid = JEVHelper::getItemid();

// previous and following month names and links
$followingWeek = $this->datamodel->getFollowingWeek($this->year, $this->month, $this->day);
$precedingWeek = $this->datamodel->getPrecedingWeek($this->year, $this->month, $this->day);

?>
<table width="100%" align="center" cellpadding="0" cellspacing="0" class="cal_table">
    <tr >
		<td colspan="2">
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="cal_table">
			    <tr valign="top" style="height:25px!important;line-height:25px;font-weight:bold;">
					<td  class="cal_td_month" style="text-align:center;">
							<?php echo "<a href='".$precedingWeek."' title='".JText::_("Preceeding Week")."' >"?>
							<?php echo JText::_("Preceeding Week")."</a>";?>
					</td>
					<td  colspan="2" class="cal_td_currentmonth" style="text-align:center;" width="100%"><?php echo  $data['startdate'] . ' - ' . $data['enddate'] ;?></td>
					<td  class="cal_td_month" style="text-align:center;" >
							<?php echo "<a href='".$followingWeek."' title='".JText::_("Following Week")."' >". JText::_("Following Week");?></a>
					</td>
				</tr>
			</table>
		</td>
    </tr>
<?php
for( $d = 0; $d < 7; $d++ ){

	$day_link = '<a class="ev_link_weekday" href="' . $data['days'][$d]['link'] . '" title="' . JText::_('JEV_CLICK_TOSWITCH_DAY') . '">'
	. JEV_CommonFunctions::jev_strftime("%A", mktime(3,0,0,$data['days'][$d]['week_month'], $data['days'][$d]['week_day'], $data['days'][$d]['week_year']))."<br/>"
	. JEventsHTML::getDateFormat( $data['days'][$d]['week_year'], $data['days'][$d]['week_month'], $data['days'][$d]['week_day'], 2 ).'</a>'."\n";

	if( $data['days'][$d]['today'])	$bg = 'class="ev_td_today"';
	else $bg = 'class="ev_td_left"';

	echo '<tr><td ' . $bg . ' >' . $day_link . '</td>' . "\n";
	echo '<td class="ev_td_right" >' . "\n";

	$num_events		= count($data['days'][$d]['rows']);
	if ($num_events>0) {
		echo "<ul class='ev_ul'>\n";

		for( $r = 0; $r < $num_events; $r++ ){
			$row = $data['days'][$d]['rows'][$r];

			$listyle = 'style="border-color:'.$row->bgcolor().';"';
			echo "<li class='ev_td_li' $listyle>\n";
			if (!$this->loadedFromTemplate('icalevent.list_row', $row, 0)){
				$this->viewEventRowNew ( $row);
				echo "&nbsp;::&nbsp;";
				$this->viewEventCatRowNew($row);
			}
			echo "</li>\n";
		}
		echo "</ul>\n";
	}
	echo '</td></tr>' . "\n";
} // end for days

echo '</table><br />' . "\n";
