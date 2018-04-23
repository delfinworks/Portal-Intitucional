<?php defined('_JEXEC') or die('JS: No Direct Access');
echo JoomlaStats_Engine::renderFilters(false, true);
?>

		<table class="adminlist">
		<thead>
            <tr>
                <th style="width: 1%;">#</th>
                <th style="width: 1px; text-align: center;"><?php echo JTEXT::_( 'TLD' );?></th>
                <th style="width: 1px; text-align: center;"><?php echo JTEXT::_( 'Country/Domain' );?></th>
                <th style="width: 1px;"><?php echo JTEXT::_( 'Pages' );?></th>
                <th style="width: 1px;"><?php echo JTEXT::_( 'Time' );?></th>
                <th style="width: 100%;"><?php echo JTEXT::_( 'Bot/Robot/Crawler/Spider name' );?></th>
                <th align="left"><?php echo JTEXT::_( 'IP' );?></th>
                <th align="left"><?php echo JTEXT::_( 'NSLookup' );?></th>
                <th align="left"><?php echo JTEXT::_( 'Actions' );?></th>
            </tr>
		</thead>
<?php
        $rowCount = count($this->rows);
 		if ( $rowCount > 0 )
{
			$k = 0;
			$order_nbr = $this->pagination->limitstart;
            $rowLimit = $this->pagination->limitstart+$this->pagination->limit;
			for ($i=$order_nbr; ($i<$rowCount && $i<($rowLimit)); $i++) {
                if(!js_JSDatabaseAccess::executionTimeAvailable(30) )
                {
                   break; //We avoid timeouts by executiong to many enquires
                }
				$row = $this->rows[$i];
				$order_nbr++;

				$time =& js_getDate($row->changed_at);
				//$time->setOffset($this->time_zone_offset);//no we are in local time zone
				$time_str = $time->toFormat();
                ?>

				<tr class="row<?php echo $k ;?>" <?php echo ( ($row->pages_nbr === null) ? (' style="color:#666666" title="' . JTEXT::_( 'Data already purged' ) . '"' ) : ('')) ;?> >
			  	<td style="text-align: right;"><em><?php echo $order_nbr;?></em></td>
				<td style="text-align: center;"><?php echo $row->code;?></td>
				<td align="left"><?php echo JTEXT::_( $row->code );?></td>

                <?php
				// PAGES column
				if (!isset($row->pages_nbr) || $row->pages_nbr <= 0) {
	                ?><td style="text-align: center;">***</td><?php //*** placeholder for archived/purged items
				} else {
	                ?>
					<td style="text-align: right;" nowrap="nowrap" title="<?php echo JTEXT::_( 'Click for additional details' );?>">
                        <?php echo js_renderPopupIcon( _JSAdminImagePath."pathinfo.png", $row->pages_nbr, 'index.php?option=com_j4age&amp;header=0&amp;controller=main&amp;tmpl=component&amp;task=detailVisitInformation&amp;moreinfo='.$row->visit_id, JTEXT::_( 'Path info' ));?>
	                </td>

					<?php
				}

				?>
				<td nowrap="nowrap"><?php echo $time_str ;?></td>
				<td nowrap="nowrap">
					<?php echo $row->browser_name.' '.$row->browser_version. ( $row->useragent ? ' (' . substr( $row->useragent, 0, 70 ) . ')' : '' )?>
				</td>
                <td align="left" nowrap="nowrap" >
                    <div  style="float:left; text-align: left">
                        <a href="javascript:document.adminForm.returnTask.value='bots';document.adminForm.vid.value='<?php echo $row->ip ;?>';document.adminForm.controller.value='maintenance';submitbutton('<?php echo ($row->ip_exclude ? "js_do_ip_include" : "js_do_ip_exclude") ;?>');" title="<?php echo  ($row->ip_exclude ? JTEXT::_( 'Click to include IP' ) : JTEXT::_( 'Click to exclude IP' )) ;?>">
                          <img src="images/<?php echo ($row->ip_exclude ? 'publish_x.png' : 'tick.png');?>" border="0" alt="<?php echo  ($row->ip_exclude ? JTEXT::_( 'Click to include IP' ) : JTEXT::_( 'Click to exclude IP' )) ;?>" />
                        </a>
                        <a href="javascript:document.adminForm.returnTask.value='bots';document.adminForm.vid.value='<?php echo $row->ip; ?>';document.adminForm.controller.value='maintenance';submitbutton('<?php echo ($row->ip_type != 2 ? "classifyIPAsBot" : "classifyIPAsBrowser") ;?>');" title="<?php echo  ($row->ip_type != 2 ? JTEXT::_( 'Classify as Bot' ) : JTEXT::_( 'Classify as Browser' )) ;?>">
                          <img src="<?php echo  _JSAdminImagePath;?><?php echo ($row->ip_type != 2 ? 'user.png': 'bot.png');?>" border="0" width="16" alt="<?php echo  ($row->ip_type != 2 ? JTEXT::_( 'Classify IP as Bot' ) : JTEXT::_( 'Classify IP as Browser' )) ;?>" />
                        </a>
                        <?php echo long2ip($row->ip); ?>
                    </div>
                    <div style="float:right; width:16px; text-align: right">
                   </div>
                 </td>
				<td nowrap="nowrap"><?php echo empty($row->nslookup)? long2ip($row->ip) : $row->nslookup ;?></td>
                <td>
                    <a href="javascript:document.adminForm.returnTask.value='bots';document.adminForm.vid.value='<?php echo $row->client_id ;?>';document.adminForm.controller.value='maintenance';submitbutton('<?php echo ($row->client_exclude ? "includeClients" : "excludeClients") ;?>');" title="<?php echo  ($row->client_exclude ? JTEXT::_( 'Click to include Useragent' ) : JTEXT::_( 'Click to exclude Useragent' )) ;?>">
                      <img src="images/<?php echo ($row->client_exclude ? 'publish_x.png' : 'tick.png');?>" border="0" alt="<?php echo  ($row->client_exclude ? JTEXT::_( 'Click to include Useragent' ) : JTEXT::_( 'Click to exclude Useragent' )) ;?>" />
                    </a>
                    <a href="javascript:document.adminForm.returnTask.value='bots';document.adminForm.vid.value='<?php echo $row->client_id ;?>';document.adminForm.controller.value='maintenance';submitbutton('<?php echo ($row->client_type == 1 ? "classifyAsBot" : "classifyAsBrowser") ;?>');" title="<?php echo  ($row->client_type == 1 ? JTEXT::_( 'Classify as Bot' ) : JTEXT::_( 'Classify as Browser' )) ;?>">
                      <img src="<?php echo  _JSAdminImagePath;?><?php echo ($row->client_type == 1 ? 'user.png': 'bot.png');?>" border="0" width="16" alt="<?php echo  ($row->client_type == 2 ? JTEXT::_( 'Classify as Bot' ) : JTEXT::_( 'Classify as Browser' )) ;?>" />
                    </a>
                </td>
				</tr>

                <?php
				$k = 1 - $k;
			}
		} else {
			?><tr><td colspan="9" style="text-align:center"><?php echo JTEXT::_( 'No data' );?></td></tr><?php
		}

		?>
		<tfoot>
            <tr>
                <td colspan="9"><?php echo $this->pagination->getListFooter();?></td>
            </tr>
		</tfoot>
</table>



