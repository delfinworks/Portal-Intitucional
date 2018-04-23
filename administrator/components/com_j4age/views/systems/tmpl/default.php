<?php defined('_JEXEC') or die('JS: No Direct Access');
echo JoomlaStats_Engine::renderFilters(false, true);
		$totalsystems = count($this->result_arr);
        $JSSystemConst = new js_JSSystemConst();

		$retval = '<table class="adminlist">' . "\n";

		{// Header
			$ostype_name_str = JTEXT::sprintf('j4age group OS into %s sets', count($this->ostype_name_arr)) .': '. implode('; ', $this->ostype_name_arr);
			$retval .= ''
			. '<thead>' . "\n"
			. '<tr>'
			. '<th style="width: 1%;">#</th>'
			. '<th style="width: 1px;">' . JTEXT::_( 'Count' ) . '</th>'
			. '<th style="width: 1px; text-align: center;">' . JTEXT::_( 'Percent' ) . '</th>'
			. '<th style="width: 100%">' . JTEXT::_( 'Operating Systems' ) .' ('. JTEXT::_( 'OS' ) .')'. '</th>'
			. '<th style="width: 1px; text-align: center;" title="'.$ostype_name_str.'">' . JTEXT::_( 'OS Type' ) . '</th>'
			. '</tr>' . "\n"
			. '</thead>' . "\n"
			;
		}

		// Body
		if( $totalsystems > 0 ) {
			$k			= 0;
			$order_nbr	= 0;

			foreach( $this->result_arr as $row ) {
				$order_nbr++;

				$retval .= '<tr class="row'.$k.'">'
			  	. '<td style="text-align: right;"><em>'.$order_nbr.'.</em></td>'
			  	. '<td style="text-align: center;">' . $row->os_visits . '</td>'
			  	. '<td>' . $this->statisticsCommon->getPercentBarWithPercentNbr( $row->os_visits, $this->max_system_visits, $this->sum_all_system_visits ) . '</td>'
				. '<td nowrap="nowrap">'.$row->os_img_html.'&nbsp;&nbsp;'
				. ( $row->os_name ? $row->os_name : '<span style="color:#FF0000;">' . JTEXT::_( 'Unknown' ) . '</span>' )
				. '</td>'
				. '<td style="text-align: center;">'.js_JSUtil::renderImg(empty($row->os_img)?'unknown':$row->os_img, $JSSystemConst->defaultPathToImagesOs,$row->ostype_name);'</td>'
				. '</tr>' . "\n";

				$k = 1 - $k;
			}
		}


		{// TotalLine - Footer
			$retval .= ''
			. '<thead>'
			. '<tr>'
			. '<th>&nbsp;</th>'
			. '<th style="text-align: center;">' . $this->sum_all_system_visits . '</th>'
			. '<th>&nbsp;</th>'
			. '<th>'.$totalsystems.'&nbsp;'. ( ($totalsystems<=1) ? JTEXT::_( 'Operating System' ) : JTEXT::_( 'Operating Systems' ) ) . '</th>'
			. '<th>&nbsp;</th>'
			. '</tr>'
			. '</thead>'
			;
		}

		$retval .= '</table>' . "\n";

		echo $retval;
?>


