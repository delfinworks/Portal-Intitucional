<?php defined('_JEXEC') or die('JS: No Direct Access');
        echo JoomlaStats_Engine::renderFilters(true, true);
        if(!empty($this->chartView)) { echo $this->chartView->display();}

        $cblinkEnable = false;
        if( file_exists( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_comprofiler' .DS. 'admin.comprofiler.php' ) )
        {
            $cblinkEnable = true;
        }


		?>
        <input type="hidden" name="afilter" value="<?php echo $this->afilter ;?>"/>

		<table class="adminlist">
            <thead>
                <tr>
                    <th style="width: 1%;">#</th>
                    <th align="left"><?php echo JTEXT::_( 'Time' ) ;?></th>
                    <th align="left"><?php echo JTEXT::_( 'First Visit' ) ;?></th>
                    <th align="left"><?php echo JTEXT::_( 'Username' );?></th>
                    <th align="left"><?php echo  JTEXT::_( 'Code' );?></th>
                    <th align="left"><?php echo  JTEXT::_( 'Country' );?></th>
                    <th align="left"><?php echo JTEXT::_( 'IP' );?></th>
                    <th align="left"><?php echo  JTEXT::_( 'NS-Lookup' );?></th>
                    <th align="left"><?php echo JTEXT::_( 'Visits' );?></th>
                    <th align="left"><?php echo JTEXT::_( 'Last Visit' );?></th>
                    <th align="left"><?php echo JTEXT::_( 'OS' );?></th>
                    <th align="left"><?php echo JTEXT::_( 'Browser' );?></th>
                    <th align="left"><?php echo JTEXT::_( 'Actions' );?></th>
                </tr>
            </thead>
		<?php

		if( $this->rows ) {
			$k = 0;
			$n = count( $this->rows );

			for( $i = 0; $i < $n; $i++ ) {
				$row = &$this->rows[$i];
				$vid = $row->visit_id;
				$order_nbr = $i+1+$this->limitstart;

				//$time =& JFactory::getDate($row->changed_at, js_getJSTimeZone());
				//$time->setOffset($this->engine->time_zone_offset);//no we are in local time zone
				//$time_str = $time->toFormat();
                
				$query = 'SELECT count(*) AS count'
				. ' FROM #__jstats_impressions i'
				. ' WHERE i.visit_id = ' . $vid
				;
                $this->engine->db->setQuery( $query );
				$count = $this->engine->db->loadResult();

                //$count = $row->visits;
                $ulink = '';  
                if(version_compare(JVERSION, '1.6' ,'>=') )
                {
                    $ulink = '?option=com_users&amp;view=user&amp;layout=edit&amp;hidemainmenu=1&amp;tmpl=component&amp;cid[]=';
                }
                else
                {
                    $ulink = '?option=com_users&amp;view=user&amp;task=edit&amp;hidemainmenu=1&amp;tmpl=component&amp;cid[]=';
                }

                $referrers = array();

                //Find Referrer
				if( 1 == 2 )
                {
                    $query  = 'SELECT DISTINCT r.referrer, r.domain, k.*, s.*'
                    . ' FROM #__jstats_referrer AS r'
                    . " LEFT OUTER JOIN #__jstats_keywords AS k ON k.referrer_id = r.refid"
                    . " LEFT JOIN #__jstats_searchers AS s ON (s.searcher_id = k.searcher_id)"
                    . ' WHERE r.visit_id = \''.$row->visit_id.'\''
                    ;

	                $this->engine->db->setQuery( $query );
	                $referrers = $this->engine->db->loadObjectList();

                    $query  = "SELECT DISTINCT CONCAT('../index.php?option=com_search&searchword=',k.keywords) as referrer, k.*, s.*"
                    . ' FROM #__jstats_keywords AS k'
                    . " LEFT JOIN #__jstats_searchers AS s ON (s.searcher_id = k.searcher_id)"
                    . ' WHERE k.visit_id = \''.$row->visit_id.'\' and referrer_id IS NULL'
                    ;

	                $this->engine->db->setQuery( $query );
	                $localKeywords = $this->engine->db->loadObjectList();
                    if($localKeywords)
                    foreach($localKeywords as $localKeyword)
                    {
                       $referrers[] = $localKeyword;
                    }
				}

                ?>
				<tr class="row<?php echo $k;?>" <?php echo ( $row->visits ? '' : ' style="color:#666666; background-color:#EFFFFF" title="'. JTEXT::_( 'Data already purged' ) . '"' )?> >
                    <td style="text-align: right;"><em><?php echo $order_nbr;?></em></td>
                    <td align="left" nowrap="nowrap"><?php echo  js_formatGMTTimestamp($row->changed_at);?></td>
                    <td align="left" nowrap="nowrap"><?php echo  js_formatGMTTimestamp($row->first_visit_at);?></td>
                    <td align="left" nowrap="nowrap">
                        <?php
                            if($row->joomla_userid && !empty($row->joomla_username))
                            {
                                echo js_renderPopupIcon( _JSAdminImagePath."person1.png", ( strlen( $row->joomla_username ) > 14 ? ((substr( $row->joomla_username, 0, 12 )).'...'):$row->joomla_username), $ulink . $row->joomla_userid, $row->joomla_username, 800, 600 );

                               if($cblinkEnable)
                               {
                                    // Community Builder userlink
                                   ?>
                                   &nbsp;
                                   <a target="popup" href="index.php?option=com_comprofiler&task=edit&cid=<?php echo $row->joomla_userid;?>&amp;hidemainmenu=1"
                                    onclick="window.open('','popup','resizable=yes,status=no,toolbar=no,location=no,scrollbars=yes,width=690,height=560')"
                                    title="<?php echo  JTEXT::_( 'Click to view profile' ) ;?>">
                                   <img src="<?php echo  _JSAdminImagePath;?>person1.png" border="0" /></a>
                                   <?php
                               }
                            }
                            else
                            {
                                ?><img src="<?php echo _JSAdminImagePath;?>disconnect.png" border="0" title="<?php echo JTEXT::_('Not logged in');?>"/><?php
                            }
                        ?>
                    </td>
                    <td align="left" nowrap="nowrap"><?php echo ($row->code == '01'? '': $row->code) ;?></td>
                    <td align="left" nowrap="nowrap">
                        <div  style="float:left; text-align: left">
                            <?php echo js_JSUtil::renderImg((empty($row->code) || $row->code == '01' )?'unknown':$row->code, $this->JSSystemConst->defaultPathToImagesTld);?>
                            <?php echo ( $row->ip == 2130706433 || empty($row->ip) ? JTEXT::_( 'Localhost' ): JTEXT::_( $row->country ) );?>
                        </div>
                        <div style="float:right; width:16px; text-align: right">
                           <a href="javascript:document.adminForm.afilter.value=' a.code=\'<?php echo $row->code; ?>\'';document.adminForm.submit();" class="tooltipSupport" style="text-align: right"><div class="icon-filter-by">&nbsp;</div><span><?php echo  JTEXT::_( 'Filter by value' ) ;?></span></a>
                       </div>

                    </td>
                    <td align="left" nowrap="nowrap">
                        <div  style="float:left; text-align: left">
                            <a href="javascript:document.adminForm.vid.value='returnView';document.adminForm.vid.value='<?php echo $row->aid ;?>';document.adminForm.controller.value='maintenance';submitbutton('<?php echo ($row->ip_exclude ? "js_do_ip_include" : "js_do_ip_exclude") ;?>');" class="tooltipSupport"><div class="icon-exclude-<?php echo ($row->ip_exclude);?>">&nbsp;</div><span><?php echo  ($row->ip_exclude ? JTEXT::_( 'Include IP' ) : JTEXT::_( 'Exclude IP' )) ;?></span></a>
                            <?php echo long2ip($row->ip); ?>
                        </div>
                        <div style="float:right; width:32px; text-align: right">
                            <a href="javascript:document.adminForm.returnTask.value='visitors';document.adminForm.vid.value='<?php echo $row->ip ;?>';document.adminForm.controller.value='maintenance';submitbutton('<?php echo ($row->ip_type == 2 ? "classifyIPAsGhost" : "classifyIPAsBot") ;?>');" class="tooltipSupport"><div class="icon-class-<?php echo ($row->ip_type);?>">&nbsp;</div><span><?php echo  ($row->ip_type == 2 ? JTEXT::_( 'Classify as Ghost' ) : JTEXT::_( 'Classify as Bot' )) ;?></span></a>
                            <a href="javascript:document.adminForm.afilter.value=' v.ip=<?php echo $row->ip; ?>'; document.adminForm.submit();"  class="tooltipSupport" style="text-align: right"><div class="icon-filter-by">&nbsp;</div><span><?php echo  JTEXT::_( 'Filter by value' ) ;?></span></a>
                       </div>
                     </td>
                    <td align="left">
                    <?php
                    if( strlen( $row->nslookup ) > 20 )
                    {
                        ?>
                        <span class="editlinktip hasTip" title="<?php echo $row->nslookup;?>">
                            <?php echo  substr( $row->nslookup, 0, 19 );?><strong style="color:#FF0000">&raquo;</strong>
                        </span>
                        <!--strong style="color:#FF0000">&raquo;</strong-->
                        <?php
                    }else{
                        echo $row->nslookup;
                    }
                    ?>
                    </td>
                    <?php
                    if ($row->visits <= 0)
                    { //*** placeholder for archived/purged items
                    ?>
                        <td style="text-align: center;">***</td>
                     <?php
                    } else {
                        ?>
                        <td style="text-align: left;" title="<?php echo JTEXT::_( 'Click for additional details' );?>">
                            <?php echo js_renderIcon( _JSAdminImagePath."pathinfo.png", $row->visits, 'index.php?option=com_j4age&amp;controller=main&amp;view=visits&amp;afilter='.urlencode(' c.client_id=').$row->client_id, JTEXT::_( 'Visits' ));?>
                        </td>
                      <?php
                    }
                    ?>
                    <?php
                    if ($count <= 0)
                    { //*** placeholder for archived/purged items
                    ?>
                        <td style="text-align: center;">***</td>
                     <?php
                    } else {
                        ?>
                        <td style="text-align: left;" title="<?php echo JTEXT::_( 'Click for additional details' );?>">
                            <?php echo js_renderPopupIcon( _JSAdminImagePath."pathinfo.png", $count, 'index.php?option=com_j4age&amp;header=0&amp;controller=main&amp;tmpl=component&amp;task=detailVisitInformation&amp;moreinfo='.$vid, JTEXT::_( 'Path info' ));?>
                        </td>
                      <?php
                    }
                    ?>
                    <td align="left" nowrap="nowrap">
                        <?php
                        if (!empty($row->os_img))
                        {
                            echo js_JSUtil::renderImg($row->os_img, $this->JSSystemConst->defaultPathToImagesOs);
                        }
                        else
                        {
                            echo js_JSUtil::renderImg('unknown', $this->JSSystemConst->defaultPathToImagesOs);
                        }
                        ?>
                       <?php echo $row->system;?>
                    </td>
                    <td align="left" nowrap="nowrap hasTip" title="<?php echo htmlentities($row->useragent);?>">
                        <?php
                        if (!empty($row->browser_img))
                        {
                            echo js_JSUtil::renderImg($row->browser_img, $this->JSSystemConst->defaultPathToImagesBrowser);
                        }
                        else
                        {
                            echo js_JSUtil::renderImg('unknown', $this->JSSystemConst->defaultPathToImagesBrowser);
                        }
                        ?>
                        <?php echo $row->browser_name.' '.$row->browser_version;?>
                    </td>
                    <td>
                        <div  style="float:left; text-align: left">
                            <a href="javascript:document.adminForm.returnTask.value='visitors';document.adminForm.vid.value='<?php echo $row->client_id ;?>';document.adminForm.controller.value='maintenance';submitbutton('<?php echo ($row->client_exclude ? "includeClients" : "excludeClients") ;?>');"  class="tooltipSupport"><div class="icon-exclude-<?php echo ($row->client_exclude);?>">&nbsp;</div><span><?php echo  ($row->client_exclude ? JTEXT::_( 'Include Useragent' ) : JTEXT::_( 'Exclude Useragent' )) ;?></span></a>
                            <?php
                                if( !empty($row->ip)  && $row->ip != js_ip2long('127.0.0.1') )
                                {
                                    echo js_renderPopupIcon( _JSAdminImagePath."whois.png", '', 'index.php?option=com_j4age&amp;task=js_view_whois_popup&amp;no_html=1&amp;header=0&amp;tmpl=component&amp;address_to_check='.long2ip($row->ip ), JTEXT::_( 'WHOIS query' ), 690, 560);
                                }
                            ?>
                            <a href="javascript:document.adminForm.returnTask.value='visitors';document.adminForm.vid.value='<?php echo $row->client_id ;?>';document.adminForm.controller.value='maintenance';submitbutton('<?php echo ($row->client_type == 2 ? "classifyAsBrowser" : "classifyAsBot") ;?>');" class="tooltipSupport"><div class="icon-class-<?php echo ($row->client_type);?>">&nbsp;</div><span><?php echo  ($row->client_type == 2 ? JTEXT::_( 'Classify as Browser' ) : JTEXT::_( 'Classify as Bot' )) ;?></span></a>
                            <?php
                            foreach($referrers as $referrer)
                            {
                                if(!empty($referrer->keywords))
                                {
                                    ?>
                                       <a href="<?php echo $referrer->referrer;?>" target="_blank">
                                       <img src="<?php echo  _JSAdminImagePath;?>unknown.png" border="0" height="15" width="15" title="Search Engine <?php echo $referrer->searcher_name;?> using <?php echo htmlentities($referrer->keywords);?>"/></a>
                                    <?php
                                }
                                else
                                {
                                    ?>
                                      <a href="<?php echo $referrer->referrer;?>" target="_blank">
                                      <img src="<?php echo  _JSAdminImagePath;?>pathinfo.png" border="0" height="15" width="15" title="Referrer <?php echo $referrer->domain;?>"/></a>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <div style="float:right; width:16px; text-align: right"><a href="javascript:document.adminForm.afilter.value=' (v.client_id=<?php echo $row->client_id; ?> <?php echo empty($row->visitor_id)? '' : 'OR c.visitor_id='.$row->visitor_id  ; ?> )';document.adminForm.submit();" style="text-align: right" class="tooltipSupport"><div class="icon-filter-by">&nbsp;</div><span><?php echo  JTEXT::_( 'Filter by values' ) ;?></span></a></div>
                    </td>
            	</tr>
                <?php
				$k = 1 - $k;
			}
		}else{
            ?>
           	<tr>
           	   <td colspan="13" style="text-align:center"><?php echo JTEXT::_( 'No data' );?></td>
           	</tr>
          	<?php
        }
?>
    <tfoot>
        <tr>
            <td colspan="13"><?php echo $this->pagination->getListFooter();?></td>
        </tr>
    </tfoot>
</table>




