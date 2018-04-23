<?php defined('_JEXEC') or die('JS: No Direct Access');
echo JoomlaStats_Engine::renderFilters(false, false);
		$retval = ''
		. "\n"
		. '<table class="adminlist">'
		. '<thead>'
		. '<tr>'
			. '<th style="width: 1%;">#</th>'
			. '<th style="width: 1px; text-align: center;">' . JTEXT::_( 'Count' ) . '</th>'
			. '<th style="width: 1px; text-align: center;">' . JTEXT::_( 'Percent' ) . '</th>'
			. '<th style="width: 100%;">' . (($this->isKeywords) ? JTEXT::_( 'Search Keyphrases' ) : JTEXT::_( 'Search Engines' )) . '</th>'
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
				. '<td style="text-align: center;">' . $row->count . '</td>'
				. '<td align="left">' . $this->statisticsCommon->getPercentBarWithPercentNbr( $row->count, $this->max_value, $this->sum_all_values ) . '</td>'
				. '<td nowrap="nowrap">'
				. ( ($this->isKeywords) ?
					wordwrap( $row->query, 100, '<br />' )
				:
					(
					'<a href="javascript:document.adminForm.dom.value=\''. $row->searcher_name. '\';'
						. 'document.adminForm.limitstart.value=0;'
						. 'submitbutton(\'keywords\');"'
						. ' title="' . JTEXT::_( 'View search items' ) . '">'
							. $row->searcher_name
						. '</a>'
					)
				)
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
        . '<th nowrap="nowrap" style="text-align: left;">'
        	. $this->total . '&nbsp;'
        	. ( ($this->isKeywords) ?
        		( ($this->total) == 1 ? JTEXT::_( 'Keyword' ) : JTEXT::_( 'Keywords' ) )
        	:
        		( ($this->total) == 1 ? JTEXT::_( 'Search engine entry' ) : JTEXT::_( 'Different search engine entries' ) )
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


