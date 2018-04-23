<?php defined('_JEXEC') or die('JS: No Direct Access');
echo JoomlaStats_Engine::renderFilters(true, false);
?>

    <table class="adminlist">
		<thead>
            <tr>
                <th style="width: 1%;">#</th>
                <th align="left" width="10%"><?php echo JTEXT::_( 'Time' );?></th>
                <th align="left" width="5%"><?php echo JTEXT::_( 'Code' );?></th>
                <!--th align="left" width="10%"><?php echo JTEXT::_( 'Country/Domain' );?></th-->
                <th align="left" width="50%"><?php echo JTEXT::_( 'UserAgent' );?></th>
                <th align="left" width="50"><?php echo  JTEXT::_( 'IP' );?></th>
                <th align="left" width="10%"><?php echo  JTEXT::_( 'NSLookup' );?></th>
                <th align="left"><?php echo JTEXT::_( 'Actions' );?></th>
            </tr>
		</thead>
		<?php
		if ( $this->rows )
        {
			$k = 0;
			$order_nbr	= 0;
		    foreach( $this->rows as $row ) {
				$order_nbr++;
				$time =& js_getDate($row->changed_at);
				//$time->setOffset($time_zone_offset);//no we are in local time!
				$time_str = $time->toFormat();

                ?>
				<tr class="row<?php echo $k;?>">
                    <td style="text-align: right;"><em><?php echo $order_nbr;?></em></td>
                    <td nowrap="nowrap"><?php echo $time_str;?></td>
                    <td nowrap="nowrap"><?php echo $row->country;?></td>
                    <?php //td nowrap="nowrap"><?php echo $row->fullname;</td>?>
                    <td nowrap="nowrap"><?php echo $row->useragent;?></td>
                    <td align="left" nowrap="nowrap">
                        <div  style="float:left; text-align: left">
                            <a href="javascript:document.adminForm.returnTask.value='notidentifiedvisitors';document.adminForm.vid.value='<?php echo $row->ip ;?>';document.adminForm.controller.value='maintenance';submitbutton('<?php echo ($row->ip_exclude ? "js_do_ip_include" : "js_do_ip_exclude") ;?>');" title="<?php echo  ($row->ip_exclude ? JTEXT::_( 'Click to include IP' ) : JTEXT::_( 'Click to exclude IP' )) ;?>">
                              <img src="images/<?php echo ($row->ip_exclude ? 'publish_x.png' : 'tick.png');?>" border="0" alt="<?php echo  ($row->ip_exclude ? JTEXT::_( 'Click to include IP' ) : JTEXT::_( 'Click to exclude IP' )) ;?>" />
                            </a>
                            <a href="javascript:document.adminForm.returnTask.value='notidentifiedvisitors';document.adminForm.vid.value='<?php echo $row->ip; ?>';document.adminForm.controller.value='maintenance';submitbutton('<?php echo ($row->ip_type != 2 ? "classifyIPAsBot" : "classifyIPAsBrowser") ;?>');" title="<?php echo  ($row->ip_type != 2 ? JTEXT::_( 'Classify as Bot' ) : JTEXT::_( 'Classify as Browser' )) ;?>">
                              <img src="<?php echo  _JSAdminImagePath;?><?php echo ($row->ip_type != 2 ? 'user.png': 'bot.png');?>" border="0" width="16" alt="<?php echo  ($row->ip_type != 2 ? JTEXT::_( 'Classify IP as Bot' ) : JTEXT::_( 'Classify IP as Browser' )) ;?>" />
                            </a>
                            <?php echo long2ip($row->ip); ?>
                        </div>
                        <div style="float:right; width:16px; text-align: right">
                       </div>
                     </td>
                    <td nowrap="nowrap"><?php echo  $row->nslookup ;?></td>
                    <td>
                        <a href="javascript:document.adminForm.returnTask.value='notidentifiedvisitors';document.adminForm.vid.value='<?php echo $row->client_id ;?>';document.adminForm.controller.value='maintenance';submitbutton('<?php echo ($row->client_exclude ? "includeClients" : "excludeClients") ;?>');" title="<?php echo  ($row->client_exclude ? JTEXT::_( 'Click to include Useragent' ) : JTEXT::_( 'Click to exclude Useragent' )) ;?>">
                          <img src="images/<?php echo ($row->client_exclude ? 'publish_x.png' : 'tick.png');?>" border="0" alt="<?php echo  ($row->client_exclude ? JTEXT::_( 'Click to include Useragent' ) : JTEXT::_( 'Click to exclude Useragent' )) ;?>" />
                        </a>
                        <a href="javascript:document.adminForm.returnTask.value='notidentifiedvisitors';document.adminForm.vid.value='<?php echo $row->client_id ;?>';document.adminForm.controller.value='maintenance';submitbutton('<?php echo ($row->client_type == 1 ? "classifyAsBot" : "classifyAsBrowser") ;?>');" title="<?php echo  ($row->client_type == 1 ? JTEXT::_( 'Classify as Bot' ) : JTEXT::_( 'Classify as Browser' )) ;?>">
                          <img src="<?php echo  _JSAdminImagePath;?><?php echo ($row->client_type == 1 ? 'user.png': 'bot.png');?>" border="0" width="16" alt="<?php echo  ($row->client_type == 2 ? JTEXT::_( 'Classify as Bot' ) : JTEXT::_( 'Classify as Browser' )) ;?>" />
                        </a>
                    </td>
				</tr>
				<?php
                $k = 1 - $k;
            }
        } else {
        	?><tr><td colspan="7" style="text-align:center"><?php echo JTEXT::_( 'No data' );?></td></tr><?php
    	}?>

		<tfoot>
            <tr>
              <td colspan="7"><?php echo $this->pagination->getListFooter();?></td>
            </tr>
		</tfoot>
    </table> 


