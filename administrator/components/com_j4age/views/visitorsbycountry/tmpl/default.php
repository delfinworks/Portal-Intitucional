<?php defined('_JEXEC') or die('JS: No Direct Access');

        $where = array();
        $objtype = JRequest::getInt('objtype', 1);
        JRequest::setVar('objtype', $objtype);

        if($objtype > -1)
        {
            //only display specific clients
            if($objtype == 3)
            {
                $objtype = _JS_DB_IPADD__TYPE_BOT_VISITOR;
                $where[] = "c.client_type = $objtype and c.browser_id = 1024";
            }
            else
            {
                $where[] = "c.client_type = $objtype";
            }
        }

        echo JoomlaStats_Engine::renderFilters(true, true);
		global $option;

        /** Make sure we have resolved all IP address information */
        $rows = array();
        IPInfoHelper::CheckIPAddresses($rows);

		$JSUtil = new js_JSUtil();
		$JSSystemConst = new js_JSSystemConst();


        // mic: search not activated as of 2006.12.23, prepared for later
        //$search		= $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
        //$search		= $this->engine->db->getEscaped( trim( strtolower( $search ) ) );

		$date_from;
		$date_to;
		$this->engine->FilterTimePeriod->getTimePeriodsDatesAsTimestamp( $date_from, $date_to );
        $where[] = $this->engine->JSDatabaseAccess->getConditionStringFromTimestamps( $date_from, $date_to);

		$query = 'SELECT count( DISTINCT a.code, c.client_id ) AS numbers, a.code, t.country'
		. ' FROM '
		. '   #__jstats_visits AS v'
        . '   LEFT JOIN #__jstats_clients c ON (c.client_id = v.client_id)'
        . '   LEFT JOIN #__jstats_ipaddresses a ON (a.ip = v.ip)'
		. '   LEFT OUTER JOIN #__ip2nationCountries t ON (a.code = t.code)'
		. ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' )
		. ' GROUP BY a.code'
		. ' ORDER BY numbers DESC, a.code ASC, c.client_id'
		;
		$this->engine->db->setQuery( $query );
		$rows = $this->engine->db->loadObjectList();
         
		$total = 0;
		$max_value = 0;
		$sum_all_values = 0;
		if ( $rows ) {
			$total = count( $rows );

            foreach( $rows as $row ) {
                $sum_all_values   += $row->numbers;

                if( $row->numbers > $max_value ) {
                    $max_value = $row->numbers;
                }
            }
		}

		$JSStatisticsCommonTpl = new js_JSStatisticsCommonTpl();

		$retval  = '<table class="adminlist">' . "\n"
		. '<thead>' . "\n"
		. '<tr>' . "\n"
		. '<th style="width: 1%;">#</th>'
		. '<th style="width: 2%;">' . JTEXT::_ ('Flag' ) . '</th>'
		. '<th style="width: 3%;">' . JTEXT::_( 'Code' ) . '</th>'
		. '<th style="width: 10%; white-space: nowrap;" title="' . JTEXT::_( 'Number of visitors' ) .'">' . JTEXT::_( 'Visitors' ) . '</th>'
		. '<th style="width: 20%;">' . JTEXT::_( 'Percent' ) . '</th>'
		. '<th style="width: 65%; text-align: left;">' . JTEXT::_( 'Country/Domain' ) . '</th>'
		. '</tr>' . "\n"
		. '</thead>' . "\n"
		;

		if( $rows ) {

		    $k		= 0;
			$order_nbr	= 0;
            foreach( $rows as $row ) {
				$order_nbr++;

				$style = '';
				if( $row->code == '' ) {
					$style = ' style="background-color:#FFEFEF;"';
				}

                $retval .= '<tr class="row' . $k . '"' . $style . '>' . "\n"
			  	. '<td style="text-align: right;"><em>'.$order_nbr.'.</em></td>'
                . '<td align="center">'. js_JSUtil::renderImg(empty($row->code) || $row->code == '01'?'unknown':$row->code, $JSSystemConst->defaultPathToImagesTld,$row->code). '</td>'
        		. '<td align="left">&nbsp;' . ($row->code != '01'?$row->code : '') . '</td>'
        		. '<td align="center">&nbsp;' . $row->numbers . '</td>'
        		. '<td align="left">' . $JSStatisticsCommonTpl->getPercentBarWithPercentNbr( $row->numbers, $max_value, $sum_all_values ) . '</td>'
                . '<td align="left">&nbsp;'
                . ( ( ( empty($row->code) ) )
                	? JTEXT::_( 'Localhost' )
                	: ( !empty($row->country) ? $row->country : '<span style="color:#FF0000;">' . JTEXT::_( 'Unknown' ) . '</span>' ) ) // $row->fullname
                . '</td>'
                . '</tr>' . "\n";

				$k = 1 - $k;
            }
        }else{
        	$retval .= '<tr>' . "\n"
        	. '<td colspan="6" style="text-align:center">'
        	. JTEXT::_( 'No data' )
        	. '</td></tr>' . "\n";
        }

		//total line
		$retval .= ''
		. '<thead>' . "\n"
		. '<tr>' . "\n"
		;

		if( $total == 0 ) {
			$retval .= '<th colspan="6" align="left">&nbsp;' . JTEXT::_( 'No countries/domains' ) . '</th>';
		} else {
			$retval .= ''
			. '<th>&nbsp;</th>'
			. '<th colspan="2">' . JTEXT::_( 'Total' ) . '</th>'
			. '<th>' . $sum_all_values . '</th>'
			. '<th>&nbsp;</th>'
			. '<th style="text-align: left;">'
				. $total . '&nbsp;'
				. ( $total == 1 ? JTEXT::_( 'Country' ) : JTEXT::_( 'Countries' ) )
			. '</th>';
		}

		$retval .= ''
		. '</tr>' . "\n"
		. '</thead>' . "\n"
		. '</table>' . "\n"
		;

		echo $retval;

?>


