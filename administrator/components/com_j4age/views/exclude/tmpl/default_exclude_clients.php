<?php defined('_JEXEC') or die('JS: No Direct Access');
echo JoomlaStats_Engine::renderFilters(false, true);

?>
    <input type="hidden" name="tpl" value="exclude_clients" />
    <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
        <thead>
        <tr>
            <th width="1%" class="title">#</th>
            <th width="1%" class="title">
                <input type="checkbox" name="toggle" id="toggle" value="" onClick="checkAll(<?php echo count($this->rows); ?>);" />
            </th>
            <th width="8%" class="title"><?php echo JTEXT::_( 'Last Visit' ); ?></th>
            <th width="1%" class="title"><?php echo JTEXT::_( 'Type' ); ?></th>
            <th width="6%" class="title"><?php echo JTEXT::_( 'OS' ); ?></th>
            <th width="10%" class="title"><?php echo JTEXT::_( 'Browser' ); ?></th>
            <th width="100%" class="title"><?php echo JTEXT::_( 'Useragent' ); ?></th>
            <th width="5%" class="title"><?php echo JTEXT::_( 'Visits' ); ?></th>
            <th width="5%" class="title"><?php echo JTEXT::_( 'Exclude' ); ?></th>
        </tr>
        </thead>
            <?php
            $k = 0;
            $n = count($this->rows);

            for ($i = 0; $i < $n; $i++) {
                $row	=& $this->rows[$i];
                $img	= $row->client_exclude ? 'publish_x.png' : 'tick.png';
                $task	= $row->client_exclude ? 'includeClients' : 'excludeClients';
                $alt	= $row->client_exclude ? JTEXT::_( 'Click to include' ) : JTEXT::_( 'Click to exclude' );
                ?>
            <tr class="row<?php echo $k; ?>">
                <td title="<?php echo $row->client_id;?>"><?php echo $i + 1 + $this->pagination->limitstart;?></td>
                <td>
                    <input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->client_id; ?>" onClick="isChecked(this.checked);" />
                </td>
                <td>
                    <?php echo js_getDate($row->changed_at)->toFormat();?>
                </td>
                <td>
                    <img src="<?php echo  _JSAdminImagePath;?><?php echo ($row->client_type == 1 ? 'user.png': 'bot.png');?>" border="0" width="16" alt="<?php echo  ($row->client_type == 2 ? JTEXT::_( 'Bot' ) : JTEXT::_( 'Browser' )) ;?>" />
                </td>
                <td>
                   <?php
                    if (!empty($row->os_img))
                    {
                      ?><img src="<?php echo $this->JSUtil->getImageWithUrl($row->os_img, $this->JSSystemConst->defaultPathToImagesOs);?>" border="0" /><?php
                    }
                    else
                    {
                        ?><img src="<?php echo $this->JSUtil->getImageWithUrl('unknown', $this->JSSystemConst->defaultPathToImagesOs);?>" border="0" /><?php
                    }
                    ?>
                   <?php echo $row->os_name;?>
                </td>
                <td>
                    <?php
                    if (!empty($row->browser_img))
                    {
                      ?><img src="<?php echo $this->JSUtil->getImageWithUrl($row->browser_img, $this->JSSystemConst->defaultPathToImagesBrowser);?>" border="0" /><?php
                    }
                    else
                    {
                        ?><img src="<?php echo $this->JSUtil->getImageWithUrl('unknown', $this->JSSystemConst->defaultPathToImagesBrowser);?>" border="0" /><?php
                    }
                    ?>
                    <?php echo $row->browser_name;?> <?php echo $row->browser_version;?>
                </td>
                <td>
                    <?php echo $row->useragent;?>
                </td>
                <td>
                    <?php echo $row->visits;?>
                </td>
                <td width="10%" align="center">
                    <a href="javascript:void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>');" title="<?php echo $alt; ?>"><img src="images/<?php echo $img;?>" border="0" alt="<?php echo $alt; ?>" /></a>
                </td>
            </tr>
            <?php
            $k = 1 - $k;
        } ?>
        <tfoot>
        <tr>
            <td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
        </tr>
        </tfoot>
    </table>
