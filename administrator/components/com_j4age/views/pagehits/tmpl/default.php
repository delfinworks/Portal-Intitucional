<?php defined('_JEXEC') or die('JS: No Direct Access');
echo JoomlaStats_Engine::renderFilters(false, true);
?>

<table class="adminlist">
    <thead>
        <tr>
            <th style="width: 3%;">#</th>
            <th style="width: 5%;"><?php echo JTEXT::_( 'Count' );?></th>
            <th style="width: 20%;"><?php echo JTEXT::_( 'Percent' );?></th>
            <th style="width: 72%; text-align: left;"><?php echo JTEXT::_( 'Page' );?></th>
        </tr>
    </thead>
    <?php
		if ( count($this->result_arr) > 0 )
        {
			$k		= 0;
			$order_nbr = $this->pagination->limitstart;

			foreach( $this->result_arr as $result_row )
            {
                $order_nbr++;
?>
				<tr class="row<?php echo $k;?>">
                    <td align="right"><em><?php echo $order_nbr ;?></em></td>
                    <td style="text-align: center;" nowrap="nowrap"><?php echo $result_row->page_impressions;?></td>
                    <td align="left"><?php echo $this->statisticsCommon->getPercentBarWithPercentNbr( $result_row->page_impressions, $this->max_page_impressions, $this->sum_all_pages_impressions ) ;?></td>
                    <td nowrap="nowrap">
                        <a href="<?php echo htmlentities( $result_row->page_url );?>" target="_blank" title="<?php echo htmlentities( $result_row->page_url );?>"><?php echo ( ($result_row->page_title!='') ? $result_row->page_title : $result_row->page_url );?></a>
                    </td>
				</tr>
                <?php
				$k = 1 - $k;
			}
		}
		else
		{
        	?><tr><td colspan="4" style="text-align:center"><?php echo JTEXT::_( 'No data' );?></td></tr><?php 
        }

        //last row of table contain total values
		?>
    <thead>
        <tr>
            <th>&nbsp;</th>
            <th nowrap="nowrap"><?php echo $this->sum_all_pages_impressions;?></th>
            <th>&nbsp;</th>
            <th style="text-align: left;"><?php echo $this->nbr_visited_pages . '&nbsp;'. ( $this->nbr_visited_pages == 1 ? JTEXT::_( 'Page' ) : JTEXT::_( 'Pages' ) );?></th>
        </tr>
    </thead>
    <tfoot><tr><td colspan="4"><?php echo $this->pagination->getListFooter();?></td></tr></tfoot>
</table>



