<?php defined('_JEXEC') or die('JS: No Direct Access');
echo JoomlaStats_Engine::renderFilters(false, true);
?>
<input type="hidden" name="afilter" value="<?php echo $this->afilter ;?>"/>

<table class="adminlist">
    <thead>
        <tr>
            <td colspan="11"><?php echo $this->pagination->getListFooter();?></td>
        </tr>
        <tr>
            <th style=" text-align: center;"><?php echo JTEXT::_( 'User-Agent Str' ); ?></th>
            <th style=" text-align: center;"><?php echo JTEXT::_( 'Parsed Products' ); ?></th>
            <th style=" text-align: center;"><?php echo JTEXT::_( 'Client-ID (old)' ); ?></th>
            <th style=" text-align: center;"><?php echo JTEXT::_( 'Client-ID (new)' ); ?></th>
            <th style=" text-align: center;"><?php echo JTEXT::_( 'Version (old)' ); ?></th>
            <th style=" text-align: center;"><?php echo JTEXT::_( 'Version (new)' ); ?></th>
            <th style=" text-align: center;"><?php echo JTEXT::_( 'Client name (new)' ); ?></th>
            <th style=" text-align: center; width:40px"><?php echo JTEXT::_( 'OS-ID (old)' ); ?></th>
            <th style=" text-align: center;"><?php echo JTEXT::_( 'OS-ID (new)' ); ?></th>
            <th style=" text-align: center;"><?php echo JTEXT::_( 'OS-Name (old)' ); ?></th>
            <th style=" text-align: center;"><?php echo JTEXT::_( 'OS-Name (new)' ); ?></th>
        </tr>
    </thead>
<?php
if( count( $this->rows ) > 0 ) {
    $k = 0;
    $order_nbr = 0;
    foreach( $this->rows as $index=>$row ) {
        $order_nbr++;

        //$newBrowserStr = trim($row->browserObj->browser_name." ".$row->browserObj->version);

        $style = '';
        $brstyle = '';
        $osstyle = '';

        if( $row->conflictInBrowser ) {
            $brstyle = ' style="background-color:#FFEFEF"';
        }

        if( $row->conflictInBrowserVersion  ) {
            $style = ' style="background-color:#FFEFEF"';
        }

        if( $row->conflictInSystem  ) {
            $osstyle = ' style="background-color:#FFEFEF"';
        }
?>
    <tr class="row<?php echo $k; ?>" >
        <td style="text-align: center;"><?php echo $row->useragent; ?></td>
        <td>
         <?php  
            if($row->isConflict)
            {
         ?>
            <input type="hidden" name="cid[<?php echo $row->conflictId; ?>]" value="<?php echo $row->client_id; ?>" />
            <input type="hidden" name="browser_id[<?php echo $row->conflictId; ?>]" value="<?php echo $row->browserObj->browser_id; ?>" />
            <input type="hidden" name="browser_version[<?php echo $row->conflictId; ?>]" value="<?php echo empty($row->browserObj->version)? 'NULL':$row->browserObj->version; ?>" />
            <input type="hidden" name="visitor_type[<?php echo $row->conflictId; ?>]" value="<?php echo $row->browserObj->browser_type; ?>" />
            <input type="hidden" name="os_id[<?php echo $row->conflictId; ?>]" value="<?php echo $row->osObj->os_id; ?>" />
<?php
            }
            if($row->browserObj->products)
            foreach( $row->browserObj->products as $product )
            {
               echo "[".implode(',', $product) . "] \n";
            }
?>
        </td>
        <td <?php echo $brstyle; ?>>
            <div  style="float:left; text-align: left">
                <?php echo $row->browser_id; ?>
            </div>
            <div style="float:right; width:16px; text-align: right">
               <a href="javascript:document.adminForm.afilter.value=' c.browser_id=<?php echo $row->browser_id; ?>';document.adminForm.submit();" class="tooltipSupport" style="text-align: right"><div class="icon-filter-by">&nbsp;</div><span><?php echo  JTEXT::_( 'Filter by value' ) ;?></span></a>
           </div>
        </td>
        <td <?php echo $brstyle; ?>><?php echo $row->browserObj->browser_id; ?></td>
        <td <?php echo $style; ?>><?php echo $row->browser_version;?></td>
        <td <?php echo $style; ?>><?php echo $row->browserObj->version ?></td>
        <td <?php echo $brstyle; ?>><?php echo $row->browserObj->browser_name ?></td>
        <td <?php echo $osstyle; ?>>
            <div  style="float:left; text-align: left">
                <?php echo $row->os_id; ?>
            </div>
            <div style="float:right; width:16px; text-align: right">
               <a href="javascript:document.adminForm.afilter.value=' c.os_id=<?php echo $row->os_id; ?>';document.adminForm.submit();" class="tooltipSupport" style="text-align: right"><div class="icon-filter-by">&nbsp;</div><span><?php echo  JTEXT::_( 'Filter by value' ) ;?></span></a>
           </div>
        </td>
        <td <?php echo $osstyle; ?>><?php echo $row->osObj->os_id ?></td>
        <td <?php echo $osstyle; ?>><?php echo $row->os_name ?></td>
        <td <?php echo $osstyle; ?>><?php echo $row->osObj->os_name ?></td>
    </tr>
<?php
        $k = 1 - $k;
    }
}

?>
    <!--tfoot>
        <tr>
            <td colspan="12"><?php echo $this->pagination->getListFooter();?></td>
        </tr>
    </tfoot-->
</table>


