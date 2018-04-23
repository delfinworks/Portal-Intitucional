<?php defined('_JEXEC') or die('JS: No Direct Access');
        echo JoomlaStats_Engine::renderFilters(true, true);
         if(!empty($this->chartView)) { echo $this->chartView->display();}

        $cblinkEnable = false;
        if( file_exists( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_comprofiler' .DS. 'admin.comprofiler.php' ) )
        {
            $cblinkEnable = true;
        }

        $showFavicon = true;

		?>
        <script type="text/javascript">
            function overlapElement(elementId, overlappingElementId)
            {
                var element = document.getElementById(elementId);
                var elementToMove = document.getElementById(overlappingElementId);
                elementToMove.top = element.top;
                elementToMove.left = element.left;
            }
        </script>
        <input type="hidden" name="afilter" value="<?php echo $this->afilter ;?>"/>

		<table class="adminlist">
            <thead>
                <tr>
                    <th style="width: 1%;">#</th>
                    <th align="left"><?php echo JTEXT::_( 'Time' ) ;?></th>
                    <th align="left"><?php echo JTEXT::_( 'Username' );?></th>
                    <th align="left"><?php echo  JTEXT::_( 'Code' );?></th>
                    <th align="left"><?php echo  JTEXT::_( 'Country' );?></th>
                    <th align="left"><?php echo JTEXT::_( 'IP' );?></th>
                    <th align="left"><?php echo  JTEXT::_( 'NS-Lookup' );?></th>
                    <th align="left"><?php echo JTEXT::_( 'Pages' );?></th>
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

				$time =& js_getDate($row->changed_at);
				//$time->setOffset($this->engine->time_zone_offset);//no we are in local time zone
				$time_str = $time->toFormat();

				$query = 'SELECT count(*) AS count'
				. ' FROM #__jstats_impressions i'
				. ' WHERE i.visit_id = ' . $vid
				;
                $this->engine->db->setQuery( $query );
				$count = $this->engine->db->loadResult();

				$ulink = '?option=com_users&amp;view=user&amp;task=edit&amp;hidemainmenu=1&amp;cid[]=';

                $referrers = array();

                //Find Referrer
				if( 1 == 1 )
                {
                    $query  = 'SELECT DISTINCT r.refid, r.referrer, r.domain, k.*, s.*'
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
				<tr class="row<?php echo $k;?>" <?php echo ( $count ? '' : ' style="color:#666666; background-color:#EFFFFF" title="'. JTEXT::_( 'Data already purged' ) . '"' )?> >
                    <td style="text-align: right;"><em><?php echo $order_nbr;?></em></td>
                    <td align="left" nowrap="nowrap"><?php echo  $time_str;?></td>
                    <td align="left" nowrap="nowrap">
                        <?php
                            if($row->joomla_userid && !empty($row->joomla_username))
                            {
                                // Joomla CMS User Details Link
                                ?>
                                <a target="popup" href="<?php echo $ulink . $row->joomla_userid ;?>" onclick="window.open('','popup','resizable=yes,status=no,toolbar=no,location=no,scrollbars=yes,width=690,height=560')" title="<?php echo JTEXT::_( 'View profile' );?>">
                                    <img src="<?php echo _JSAdminImagePath;?>person1.png" border="0" />
                                    &nbsp;
                                    <?php
                                        if( strlen( $row->joomla_username ) > 14 )
                                        {
                                            ?><span class="editlinktip hasTip" title="<?php echo $row->joomla_username;?>"><?php echo substr( $row->joomla_username, 0, 12 );?></span><?php
                                        }
                                        else
                                        {
                                            echo $row->joomla_username;
                                        }
                                     ?>
                                </a>
                                <?php
                               if($cblinkEnable)
                               {
                                    // Community Builder userlink
                                   ?>
                                   &nbsp;
                                   <a target="popup" href="index.php?option=com_comprofiler&task=edit&cid=<?php echo $row->joomla_userid;?>&amp;hidemainmenu=1"
                                    onclick="window.open('','popup','resizable=yes,status=no,toolbar=no,location=no,scrollbars=yes,width=690,height=560')"
                                    title="<?php echo  JTEXT::_( 'View profile' ) ;?>">
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
                            <?php echo ( $row->ip == 2130706433 ? JTEXT::_( 'Localhost' ): JTEXT::_( $row->country ) );?>
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
                            <a href="javascript:document.adminForm.returnTask.value='visits';document.adminForm.vid.value='<?php echo $row->ip ;?>';document.adminForm.controller.value='maintenance';submitbutton('<?php echo ($row->ip_type == 2 ? "classifyIPAsGhost" : "classifyIPAsBot") ;?>');" class="tooltipSupport"><div class="icon-class-<?php echo ($row->ip_type);?>">&nbsp;</div><span><?php echo  ($row->ip_type == 2 ? JTEXT::_( 'Classify as Ghost' ) : JTEXT::_( 'Classify as Bot' )) ;?></span></a>

                            <a href="javascript:document.adminForm.afilter.value=' v.ip=<?php echo $row->ip; ?>'; document.adminForm.submit();"  class="tooltipSupport" style="text-align: right"><div class="icon-filter-by">&nbsp;</div><span><?php echo  JTEXT::_( 'Filter by value' ) ;?></span></a>
                       </div>
                     </td>
                    <td align="left">
                        <div  style="float:left; text-align: left">
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
                        </div>
                        <?php
                            if( strlen( $row->nslookup ) > 0 )
                            {
                                $nsFragments = explode(".",$row->nslookup);
                                $domainStr = "";
                                $lenLimit = 2;

                                $nsIndex = count($nsFragments);
                                if($nsIndex> 0)
                                {
                                    $fragment = $nsFragments[--$nsIndex];
                                    $domainStr = $fragment;
                                    for(; $nsIndex > 0; $nsIndex--)
                                    {
                                        $fragment = $nsFragments[$nsIndex-1];

                                        $domainStr = $fragment.".".$domainStr;
                                        $lenLimit--;
                                        if( $lenLimit == 0 || strlen($fragment) >3 || $nsIndex == 1 )
                                        {
                                           break;
                                        }

                                    }


                        ?>
                                <?php if($showFavicon){?>
                                <div style="float:right; max-width:16px; text-align: right">
                                    <img style="" src="http://<?php echo $domainStr;?>/favicon.ico" border="0" height="16" width="16"/>
                                </div>
                                <?php }?>
                          <?php
                                }
                            }
                         ?>
                    </td>
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
                            $urlIndexStr = strrpos($row->useragent, "http://");

                            $agentURL = null;
                            $agentDomain = null;

                            if(!empty($urlIndexStr) && $urlIndexStr >= 0)
                            {
                                $agentURL = substr($row->useragent, $urlIndexStr);

                                $urlIndexStr = strpos($agentURL, ")");
                                if(!empty($urlIndexStr) && $urlIndexStr >= 0)
                                {
                                   $agentURL = substr($agentURL, 0, $urlIndexStr);
                                }
                                $urlIndexStr = strpos($agentURL, " ");
                                if(!empty($urlIndexStr) && $urlIndexStr >= 0)
                                {
                                   $agentURL = substr($agentURL, 0, $urlIndexStr);
                                }
                                $urlIndexStr = strpos($agentURL, ",");
                                if(!empty($urlIndexStr) && $urlIndexStr >= 0)
                                {
                                   $agentURL = substr($agentURL, 0, $urlIndexStr);
                                }
                                $agentURL = trim($agentURL);
                                $agentDomain = substr($agentURL, 0, strpos($agentURL, "/", 8) );
                                $agentDomain = trim($agentDomain);

                             }

                        if (!empty($row->browser_img))
                        {
                            if(!empty($agentDomain) && ($row->browser_img == "noimage" || $row->browser_img == "unknown"))
                            {
                                echo '<img src="'.$agentDomain.'/favicon.ico" width="16" height="16" border="0" />';
                            }
                            else
                            {
                                echo js_JSUtil::renderImg($row->browser_img, $this->JSSystemConst->defaultPathToImagesBrowser);
                            }
                        }
                        else
                        {
                            if(!empty($agentDomain))
                            {
                                echo '<img src="'.$agentDomain.'/favicon.ico" width="16" height="16"  border="0" />';
                            }
                            else
                            {
                                echo js_JSUtil::renderImg('unknown', $this->JSSystemConst->defaultPathToImagesBrowser);
                            }
                        }
                        ?>
                            <?php echo empty($agentURL)?"": "<a href='$agentURL' target='_blank'/>";?>
                            <?php echo $row->browser_name.' '.$row->browser_version;?>
                            <?php echo empty($agentURL)?"": "</a>";?>
                    </td>
                    <td>
                        <div  style="float:left; text-align: left">
                            <a href="javascript:document.adminForm.returnTask.value='visits';document.adminForm.vid.value='<?php echo $row->client_id ;?>';document.adminForm.controller.value='maintenance';submitbutton('<?php echo ($row->client_exclude ? "includeClients" : "excludeClients") ;?>');"  class="tooltipSupport"><div class="icon-exclude-<?php echo ($row->client_exclude);?>">&nbsp;</div><span><?php echo  ($row->client_exclude ? JTEXT::_( 'Include Useragent' ) : JTEXT::_( 'Exclude Useragent' )) ;?></span></a>
                            <?php
                            if( !empty($row->ip) && $row->ip != js_ip2long('127.0.0.1') )
                            {
                                echo js_renderPopupIcon( _JSAdminImagePath."whois.png", '', 'index.php?option=com_j4age&amp;task=js_view_whois_popup&amp;no_html=1&amp;header=0&amp;tmpl=component&amp;address_to_check='.long2ip($row->ip ), JTEXT::_( 'WHOIS query' ), 690, 560);
                            }
                            ?>
                            <a href="javascript:document.adminForm.returnTask.value='visits';document.adminForm.vid.value='<?php echo $row->client_id ;?>';document.adminForm.controller.value='maintenance';submitbutton('<?php echo ($row->client_type == 2 ? "classifyAsBrowser" : "classifyAsBot") ;?>');" class="tooltipSupport"><div class="icon-class-<?php echo ($row->client_type);?>">&nbsp;</div><span><?php echo  ($row->client_type == 2 ? JTEXT::_( 'Classify as Browser' ) : JTEXT::_( 'Classify as Bot' )) ;?></span></a>
                            <?php
                            foreach($referrers as $referrer)
                            {
                                if(!empty($referrer->keywords))
                                {
                                    ?>
                                       <a href="<?php echo $referrer->referrer;?>" target="_blank" >
                                           <span id="" class="containing_div" style="display: inline-block;position: relative;left: 0px;width: 16px;height: 16px">
                                               <div id="lowerReferrerDiv<?php echo $referrer->refid ?>" class="lower_div" style="opacity:0.3;filter:alpha(opacity=30);width: 16px;height: 16px;top: 0px;background-repeat:no-repeat;background-image: url('http://<?php echo $referrer->domain;?>/favicon.ico')">
                                               </div>
                                               <div id="overlappingReferrerDiv<?php echo $referrer->refid ?>" class="overlapping_div" style="position:absolute;width: 16px;height: 16px;top: 0px;left: 0px;"><img style="" src="<?php echo  _JSAdminImagePath;?>unknown.png" border="0" height="16" width="16" title="Search Engine <?php echo $referrer->searcher_name;?> using <?php echo htmlentities($referrer->keywords);?>"/></div>
                                           </span>
                                        </a>
                                        <?php
                                }
                                else
                                {
                                    ?>
                                      <a href="<?php echo $referrer->referrer;?>" target="_blank">
                                        <span class="containing_div" style="display: inline-block;position: relative;left: 0px;width: 16px;height: 16px">
                                            <div id="lowerReferrerDiv<?php echo $referrer->refid ?>" class="lower_div" style="opacity:0.5;filter:alpha(opacity=50);width: 16px;height: 16px;top: 0px;background-repeat:no-repeat;background-image: url('http://<?php echo $referrer->domain;?>/favicon.ico')">
                                            </div>
                                            <div id="overlappingReferrerDiv<?php echo $referrer->refid ?>" class="overlapping_div" style="position:absolute;width: 16px;height: 16px;top: 0px;left: 0px;"><img style="" src="<?php echo  _JSAdminImagePath;?>pathinfo.png" border="0" height="16" width="16" title="Referrer <?php echo $referrer->domain;?>"/></div>
                                        </span>
                                      </a>
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
           	   <td colspan="12" style="text-align:center"><?php echo JTEXT::_( 'No data' );?></td>
           	</tr>
          	<?php
        }
?>
    <tfoot>
        <tr>
            <td colspan="12"><?php echo $this->pagination->getListFooter();?></td>
        </tr>
    </tfoot>
</table>




