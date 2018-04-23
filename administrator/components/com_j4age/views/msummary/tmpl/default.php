<?php defined('_JEXEC') or die('JS: No Direct Access');
echo JoomlaStats_Engine::renderFilters(false, false, true);
?>
<?php if(!empty($this->chartView)) { echo $this->chartView->display();}

$this->JSSystemConst = new js_JSSystemConst();
$this->JSUtil = new js_JSUtil();

function js_summary_trend_icon($current, $previous, $parameter)
{
    if(empty($current->$parameter) || empty($previous))
    {
        return '';
    }

    $html = '<img src="';
    if($current->$parameter == $previous->$parameter)
    {
        $html .= _JSAdminImagePath.'equal.png';
    }
    else if($current->$parameter > $previous->$parameter)
    {
        $html .= _JSAdminImagePath.'greater.png';
    }
    else if($current->$parameter < $previous->$parameter)
    {
        $html .= _JSAdminImagePath.'lower.png';
    }
    $html .='" border="0" height="15" width="15" title=""/>';
    return $html;
}
?>
<table class="adminlist">
    <thead>
        <tr>
            <th nowrap="nowrap"><?php echo JTEXT::_( 'Day' )?></th>
            <th colspan="2" nowrap="nowrap" title="<?php echo  JTEXT::_( 'Number of unique visitors' ) ;?>"><?php echo  JTEXT::_( 'Unique visitors' ) ;?></th>
            <th colspan="2" nowrap="nowrap" title="<?php echo  JTEXT::_( 'Number of visitors' ) ;?>"><?php echo  JTEXT::_( 'Visitors' ) ;?></th>
            <th nowrap="nowrap" title="<?php echo  JTEXT::_( 'Number of visitors' ) . ' / ' . JTEXT::_( 'Number of unique visitors' ) ;?>"><?php echo  JTEXT::_( 'Visits average' ) ;?></th>
            <th colspan="2" nowrap="nowrap" title="<?php echo  JTEXT::_( 'Number of visited pages' );?>"><?php echo JTEXT::_( 'Page impressions' ) ;?></th>
            <th nowrap="nowrap"><?php echo  JTEXT::_( 'Referrers' ) ;?></th>
            <th nowrap="nowrap"><?php echo  JTEXT::_( 'Search Engines' ) ;?></th>
            <th colspan="2" nowrap="nowrap" title="<?php echo  JTEXT::_( 'Number of unique bots/spiders' );?>"><?php echo  JTEXT::_( 'Unique bots/spiders' ) ;?></th>
            <th colspan="2" nowrap="nowrap" title="<?php echo  JTEXT::_( 'Number of bots/spiders' ) ;?>"><?php echo  JTEXT::_( 'Bots/spiders' ) ;?></th>
            <th nowrap="nowrap" title="<?php echo  JTEXT::_( 'Number of unique not identified visitors' ) ;?>"><?php echo  JTEXT::_( 'Unique NIV' ) ;?></th>
            <th nowrap="nowrap" title="<?php echo  JTEXT::_( 'Number of not identified visitors' );?>"><?php echo  JTEXT::_( 'NIV' ) ;?></th>
            <th nowrap="nowrap"><?php echo  JTEXT::_( 'Unique sum' ) ;?></th>
            <th nowrap="nowrap"><?php echo  JTEXT::_( 'Sum' ) ;?></th>
        </tr>
    </thead>
        <?php
        $previousRow = null;

		foreach( $this->rows as $row ) {

			// now we have all values, now draw the row (day)
			if( date( 'w', strtotime( $row->year.'-'.$row->month.'-'.$row->day ) ) == 6 ) {
				$cls = 'row0'; // info: background-color: #F9F9F9;
			}elseif (date( 'w', strtotime( $row->year.'-'.$row->month.'-'.$row->day ) ) == 0 ) {
				$cls = 'row2" style="background-color:#efefef; border-bottom: 1px dotted #ff0000';
			}else{
				$cls = 'row1'; // info: background-color: #F1F1F1;
			}
            ?>
			<tr class="<?php echo $cls;?>">
                <td align="center"><?php echo $row->i;?></td>
                <td align="right"><?php echo  ($row->uv ? $row->uv : '.');?></td>
                <td align="left" style="width:10px"><?php echo js_summary_trend_icon($row, $previousRow, 'uv');?></td>
                <td align="right">
                    <a href="javascript:SelectMonth('.$row->month.');SelectDay(<?php echo $row->i;?>);submitbutton('visitors');" title="<?php echo JTEXT::_( 'Click for visitors details' );?>">
                        <?php echo ($row->v ? $row->v : '.') ;?>
                    </a>
                </td>
                <td align="left" style="width:10px"><?php echo js_summary_trend_icon($row, $previousRow, 'v');?></td>
                <td align="center"><?php echo $row->vavg;?></td>
                <td align="right">
                    <?php echo ( $row->p ? '<a href="javascript:SelectMonth('.$row->month.');SelectDay('.$row->i.');submitbutton(\'pageHits\');" title="'. JTEXT::_( 'Click for page details' ).'">'.  $row->p .'</a>' : '.' ) ;?>
                </td>
                <td align="left" style="width:10px"><?php echo js_summary_trend_icon($row, $previousRow, 'p');?></td>
                <td align="center">
                    <?php echo ( $row->r ? '<a href="javascript:SelectMonth('.$row->month.');SelectDay('.$row->i.');submitbutton(\'referrersByDomain\');" title="'. JTEXT::_( 'Click for referrer details' ).'">'.$row->r.'</a>' : '.' );?>
                </td>
                <td align="center"><?php echo ( $row->inquiries ? $row->inquiries : '.' );?></td>
                <td align="right"><?php echo  ( $row->ub ? $row->ub : '.' );?></td>
                <td align="left" style="width:10px"><?php echo js_summary_trend_icon($row, $previousRow, 'ub');?></td>
                <td align="right">
                    <?php echo ( $row->b ? '<a href="javascript:SelectMonth('.$row->month.');SelectDay('.$row->i.');submitbutton(\'botsByDomain\');" title="'. JTEXT::_( 'Click for additional details' ).'">'. $row->b.'</a>' : '.' );?>
                </td>
                <td style="width:10px"><?php echo js_summary_trend_icon($row, $previousRow, 'b');?></td>
                <td><?php echo  ( ($row->univ) ? ($row->univ ) : '.' );?></td>
                <td>
                    <?php echo  ( ($row->niv) ? '<a href="javascript:SelectMonth('.$row->month.');SelectDay('.$row->i.');submitbutton(\'notidentifiedvisitors\');" title="' . JTEXT::_( 'Click for additional details' ) . '">'.$row->niv.'</a>' : '.' );?>
                </td>
                <td><?php echo  ( $row->usum ? $row->usum : '.' );?></td>
                <td><?php echo  ( $row->sum ? $row->sum : '.' );?></td>
			</tr>

			<?php
            $previousRow = $row;

		}
 ?>
    <thead>
        <tr>
            <th align="center"><?php  echo $this->JSTemplate->monthToString($this->total->month_or_year, true) ;?></th>
            <th colspan="2" align="right"><?php  echo $this->JSStatisticsTpl->addSummStyleLine( false, $this->total->tuv, 0 ) ;?></th>
            <th colspan="2" align="right"><?php  echo $this->JSStatisticsTpl->addSummStyleLine( false, $this->total->tv, 0 ) ;?></th>
            <th align="center"><?php  echo $this->visits_average;?></th>
            <th colspan="2" align="right"><?php  echo $this->JSStatisticsTpl->addSummStyleLine( false, $this->total->tp, 0 ) ;?></th>
            <th align="center"><?php  echo $this->total->tr ;?></th>
            <th align="center"><?php  echo $this->total->inquiries ;?></th>
            <th colspan="2" align="right"><?php  echo $this->JSStatisticsTpl->addSummStyleLine( false, $this->total->tub, 0 ) ;?></th>
            <th colspan="2" align="right"><?php  echo $this->JSStatisticsTpl->addSummStyleLine( false, $this->total->tb, 0 ) ;?></th>
            <th><?php  echo $this->JSStatisticsTpl->addSummStyleLine( false, $this->total->tuniv, 0 ) ;?></th>
            <th><?php  echo $this->JSStatisticsTpl->addSummStyleLine( false, $this->total->tniv,  0 ) ;?></th>
            <th><?php  echo $this->JSStatisticsTpl->addSummStyleLine( false, $this->total->tusum, 0 ) ;?></th>
            <th><?php  echo $this->JSStatisticsTpl->addSummStyleLine( false, $this->total->tsum,  0 ) ;?></th>
        </tr>
    </thead>
</table>
<?php
		js_echoJSDebugInfo($this->prof->mark('end'), '');
?>


