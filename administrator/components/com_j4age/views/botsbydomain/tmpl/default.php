<?php defined('_JEXEC') or die('JS: No Direct Access');
echo JoomlaStats_Engine::renderFilters(false, false);
 		$retval = ''
         . "\n<input type=\"hidden\" name=\"objtype\" value=\"\" />"
         . "\n<input type=\"hidden\" name=\"afilter\" value=\"\" />"
		. '<table class="adminlist">'
		. '<thead>'
		. '<tr>'
			. '<th style="width: 1%;">#</th>'
			. '<th style="width: 1px; text-align: center;">' . JTEXT::_( 'Visits' ) . '</th>'
			. '<th style="width: 1px; text-align: center;">' . JTEXT::_( 'Percent' ) . '</th>'
			. '<th style="width: 100%;">' . JTEXT::_( 'Bot/Spider' ) . '</th>'
		. '</tr>'
		. '</thead>'
		. "\n"
		;

		if ( count($this->rows) > 0 ) {
			$k = 0;
			$order_nbr = $this->pagination->limitstart;
			for ($i=$order_nbr; ($i<count($this->rows) && $i<($this->pagination->limitstart+$this->pagination->limit)); $i++) {
				$row = $this->rows[$i];
				$order_nbr++;

				$retval .= ''
				. '<tr class="row' . $k . '">'
			  	. '<td style="text-align: right;"><em>'.$order_nbr.'.</em></td>'
				. '<td style="text-align: center;">' . $row->numbers . '</td>'
				. '<td align="left">' . $this->statisticsCommon->getPercentBarWithPercentNbr( $row->numbers, $this->max_value, $this->sum_all_values ) . '</td>'
				. '<td nowrap="nowrap">'
                	. '<a title="' . JTEXT::_( 'Details' )
                	. '" href="javascript:document.adminForm.objtype.value=\'2\';document.adminForm.afilter.value=\'(c.browser_id='. rawurlencode( $row->browser_id ) . ')\';submitbutton(\'visits\');">'
                	. $row->browser
                	. '</a>'
				. '</td>'
				. '</tr>'
				. "\n"
				;

				$k = 1 - $k;
			}
		} else {
			$retval .= '<tr><td colspan="4" style="text-align:center">'. JTEXT::_( 'No data' ) . '</td></tr>';
		}

		// TotalLine
		$retval .= ''
		. '<thead>'
		. '<tr>'
		. '<th>&nbsp;</th>'
		. '<th style="text-align: center;">' . $this->sum_all_values . '</th>'
        . '<th>&nbsp;</th>'
        . '<th nowrap="nowrap">'
		. ( ( $this->total == 0) ?
			( JTEXT::_('No Bots') )
			:
       		( $this->total . '&nbsp;' . (($this->total == 1) ? JTEXT::_( 'Bot' ) : JTEXT::_( 'Different Bots' )))
		  )
        . '</th>'
        . '</tr>'
		. '</thead>'
		. '<tfoot><tr><td colspan="4">'.$this->pagination->getListFooter().'</td></tr></tfoot>'
		. '</table>'
		. "\n"
		;

		echo $retval;
?>


