<?php defined('_JEXEC') or die('JS: No Direct Access');
echo JoomlaStats_Engine::renderFilters(false, false);
?>

<table class="adminlist">
    <thead>
    <tr>
        <th style="width: 1%;">#</th>
        <th style="width: 1px; text-align: center;"><?php echo JTEXT::_( 'Count' );?></th>
        <th style="width: 1px; text-align: center;"><?php echo JTEXT::_( 'Percent' );?></th>
        <th style="width: 100%;"><?php echo (($this->byPage) ? JTEXT::_( 'Referrer page' ) : JTEXT::_( 'Referrer domain' ));?></th>
    </tr>
    </thead>
    <?php
    $rowCount = count($this->rows);
    if ( $rowCount > 0 ) {
        $k = 0;
        $order_nbr = $this->pagination->limitstart;

        $rowLimit = $this->pagination->limitstart+$this->pagination->limit;
        for ($i=$order_nbr; ($i<$rowCount && $i<($rowLimit)); $i++) {
            $row = $this->rows[$i];
            $order_nbr++;

            ?>
            <tr class="row<?php echo $k;?>">
            <td style="text-align: right;"><em><?php echo $order_nbr;?></em></td>
            <td style="text-align: center;"><?php echo $row->counter;?></td>
            <td align="left"><?php echo $this->statisticsCommon->getPercentBarWithPercentNbr( $row->counter, $this->max_value, $this->sum_all_values );?></td>
            <td nowrap="nowrap"><?php
                if($this->byPage)
                {
                    ?><a href="<?php echo $row->referrer;?>" target="_blank" title="<?php echo JTEXT::_( 'Opens URL in new window' );?>"><?php echo $row->referrer;?></a><?php
                }
                else
                { ?>
                  <a href="javascript:document.adminForm.dom.value='<?php echo $row->domain ;?>'; document.adminForm.limitstart.value=0; submitbutton('referrersByPage');" title="<?php echo JTEXT::_( 'Click to view referring page' ) ;?>"><?php echo $row->domain ;?></a>
                  <?php
                }
                ?>
            </td>
            </tr>
            <?php
            $k = 1 - $k;
        }
    } else {
        ?><tr><td colspan="4" style="text-align:center"><?php echo JTEXT::_( 'No data' );?></td></tr><?php
    }
    ?>
    <thead>
    <tr>
    <th>&nbsp;</th>
    <th style="text-align: center;"><?php echo $this->sum_all_values;?></th>
    <th>&nbsp;</th>
    <th nowrap="nowrap" style="text-align: left;"><?php echo ( ( $this->total == 0) ?
        ( JTEXT::_('No referring domains') )
        :
        ( $this->total.'&nbsp;' . (($this->total == 1) ? JTEXT::_( 'Referring domain' ) : JTEXT::_( 'Referring domains' )))
      );?>
    </th>
    </tr>
    </thead>
    <tfoot><tr><td colspan="4"><?php echo $this->pagination->getListFooter();?></td></tr></tfoot>
</table>


