<?php defined('_JEXEC') or die('JS: No Direct Access');
echo JoomlaStats_Engine::renderFilters(false, false, true);
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
<?php if(!empty($this->chartView)) { echo $this->chartView->display();} ?>
		
<table class="adminlist">
    <thead>
        <tr>
            <th nowrap="nowrap"><?php echo JTEXT::_( 'Month' )?></th>
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

foreach( $this->rows as $row )
        {
			// Now we have all data, let's show the lines of each month
            $JSTemplate = new js_JSTemplate();

            ?>
			<tr class="row<?php echo $row->alternator;?>">
                <td align="center"><?php echo $this->JSTemplate->monthToString($row->month, true);?></td>
                <td align="right"><?php echo ( $row->uv ? $row->uv : '.' );?></td>
                <td align="left" style="width:10px"><?php echo js_summary_trend_icon($row, $previousRow, 'uv');?></td>
                <td align="right"><?php echo ( $row->v  ? $row->v  : '.' );?></td>
                <td align="left" style="width:10px"><?php echo js_summary_trend_icon($row, $previousRow, 'v');?></td>
                <td align="center"><?php echo $row->vavg;?></td>
                <td align="center"><?php echo ( $row->p ? $row->p : '.' ) . ' ' ;?></td>
                <td style="width:10px"><?php echo js_summary_trend_icon($row, $previousRow, 'p');?></td>
                <td align="center"><?php echo ( $row->r ? $row->r : '.' );?></td>
                <td align="center"><?php echo ( $row->inquiries ? $row->inquiries : '.' );?></td>
                <td align="center"><?php echo ( $row->ub ? $row->ub : '.' ) . ' ' ;?></td>
                <td style="width:10px"><?php echo js_summary_trend_icon($row, $previousRow, 'ub');?></td>
                <td align="center"><?php echo ( $row->b ? $row->b : '.' ) . ' ';?></td>
                <td style="width:10px"><?php echo js_summary_trend_icon($row, $previousRow, 'b');?></td>
                <td><?php echo ( ($row->univ) ? $row->univ : '.' );?></td>
                <td><?php echo ( ($row->niv) ? "<a href=\"javascript:SelectMonth(".$row->month.");SelectDay('all');submitbutton('notidentifiedvisitors');\" title=\"" . JTEXT::_( 'Click for additional details' ) . '">' . $row->niv . '</a>' : '.' );?></td>
                <td><?php echo ( $row->usum ? $row->usum : '.' ) ;?></td>
                <td><?php echo ( $row->sum ? $row->sum : '.' ) ;?></td>
			</tr>
			<?php
            $previousRow = $row;
		}
 ?>
    <thead>
        <tr>
            <th align="center"><?php  echo $this->total->month_or_year ;?></th>
            <th colspan="2" align="right"><?php  echo $this->JSStatisticsTpl->addSummStyleLine( false, $this->total->tuv, 0 ) ;?></th>
            <th colspan="2" align="right"><?php  echo $this->JSStatisticsTpl->addSummStyleLine( false, $this->total->tv, 0 ) ;?></th>
            <th align="center"><?php  echo $this->visits_average;?></th>
            <th colspan="2" align="right"><?php  echo $this->JSStatisticsTpl->addSummStyleLine( false, $this->total->tp, 0 ) ;?></th>
            <th align="center"><?php  echo $this->total->tr ;?></th>
            <th align="center"><?php  echo $this->total->inquiries ;?></th>
            <th colspan="2" align="right"><?php  echo $this->JSStatisticsTpl->addSummStyleLine( false, $this->total->tub, 0 ) ;?></th>
            <th colspan="2" align="right"><?php  echo $this->JSStatisticsTpl->addSummStyleLine( false, $this->total->tb, 0 ) ;?></th>
            <th><?php  echo $this->JSStatisticsTpl->addSummStyleLine( false, $this->total->tuniv, 0 ) ;?></th>
            <th><?php  echo $this->JSStatisticsTpl->addSummStyleLine( false, $this->total->tniv, 0 ) ;?></th>
            <th><?php  echo $this->JSStatisticsTpl->addSummStyleLine( false, $this->total->tusum, 0 ) ;?></th>
            <th><?php  echo $this->JSStatisticsTpl->addSummStyleLine( false, $this->total->tsum,  0 ) ;?></th>
        </tr>
    </thead>
</table>


