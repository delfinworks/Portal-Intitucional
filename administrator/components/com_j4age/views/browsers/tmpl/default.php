<?php defined('_JEXEC') or die('JS: No Direct Access');
echo JoomlaStats_Engine::renderFilters(false, true);
?>
<table class="adminlist">
    <thead>
        <tr>
            <th style="width: 1px;">#</th>
            <th style="width: 1px; text-align: center;"><?php echo JTEXT::_( 'Count' ); ?></th>
            <th style="width: 1px; text-align: center;"><?php echo JTEXT::_( 'Percent' ); ?></th>
            <th style="width: 1px; text-align: center;">&nbsp;</th>
            <th style="width: 100%;"><?php echo JTEXT::_( 'Browser' ); ?></th>
        </tr>
    </thead>
<?php
if( count( $this->rows ) > 0 ) {
    $k = 0;
    $order_nbr = 0;
    foreach( $this->rows as $row ) {
        $order_nbr++;

        $style = '';
        if( !$row->browser ) {
            $style = ' style="background-color:#FFEFEF"';
        }
?>
    <tr class="row<?php echo $k; ?>" <?php echo $style; ?> >
        <td style="text-align: right;"><em><?php echo $order_nbr; ?></em></td>
        <td style="text-align: center;"><?php echo $row->numbers; ?></td>
        <td><?php echo $this->JSStatisticsCommonTpl->getPercentBarWithPercentNbr( $row->numbers, $this->max_value, $this->sum_all_values ); ?></td>
        <td><?php
        echo js_JSUtil::renderImg(empty($row->browser_img)?'unknown':$row->browser_img, $this->JSSystemConst->defaultPathToImagesBrowser);;
      ?></td>
        <td><?php echo $row->browser; ?></td>
    </tr>

<?php
        $k = 1 - $k;
    }
}

?>
    <thead>
        <tr>
        <th>&nbsp;</th>
        <th style="text-align: center;"><?php echo $this->totalnmb; ?></th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th><?php echo $this->totalbrowsers; ?>&nbsp;<?php echo ( ($this->totalbrowsers<=1) ? JTEXT::_( 'Browser type' ) : JTEXT::_( 'Browser types' ) ); ?></th>
        </tr>
    </thead>
</table>


