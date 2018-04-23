<?php defined('_JEXEC') or die('JS: No Direct Access');
?>

<div style="text-align: center; font-weight: bold; font-size: larger;"><?php echo JTEXT::_( 'Visited pages' );?></div>
<div style="text-align: center;">
<table class="adminlist" style="width: 90%;" align="center">
    <thead>
    <tr>
    <th style="width: 1%;">#</th>
    <th style="width: 1px;" title="<?php echo JTEXT::_( 'Number of impressions' );?>"><?php echo  JTEXT::_( 'IMP.' ); ?></th>
    <th style="width: 100%; text-align: left;"><?php echo  JTEXT::_( 'Page' ) ?></th>
    </tr>
    </thead>

<?php
 		$retval  = '';

		// Body
		if( count($this->impressions_result_arr) > 0 ) {
			$k			= 0;
			$order_nbr	= 0;

			foreach( $this->impressions_result_arr as $row ) {
				$order_nbr++;

				$retval .= '<tr class="row'.$k.'">'
			  	. '<td style="text-align: right;"><em>'.$order_nbr.'.</em></td>'
			  	. '<td style="text-align: right;">' . $row->impresions . '</td>'
				. '<td>'
					. '<a href="' . htmlentities($row->page) . '" target="_blank"' . 'title="' . JTEXT::_( 'Click opens new window' ) . '">'
						. ( $row->page_title == '' ? $row->page : $row->page_title )
					. '</a>'
				. '</td>'
				. '</tr>' . "\n";

				$k = 1 - $k;
			}
		}


		{// TotalLine - Footer
			$retval .= ''
			. '<thead>'
			. '<tr>'
			. '<th>&nbsp;</th>'
			. '<th style="text-align: center;">' . $this->impressions_sum_all . '</th>'
			. '<th>&nbsp;</th>'
			. '</tr>'
			. '</thead>'
			;
		}

		echo $retval;

?>
</table>
</div>