<?php defined('_JEXEC') or die('JS: No Direct Access');
echo JoomlaStats_Engine::renderFilters(true, false);
?>
    <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
        <thead>
        <tr>
            <th width="1%" class="title">#</th>
            <th width="1%" class="title">
                <input type="checkbox" name="toggle" id="toggle" value="" onClick="checkAll(<?php echo count($this->rows); ?>);" />
            </th>
            <th width="5%" class="title"><?php echo JTEXT::_( 'IP-Address' ); ?></th>
            <th width="1%" class="title"><?php echo JTEXT::_( 'Type' ); ?></th>
            <th width="100%" class="title"><?php echo JTEXT::_( 'NS-Lookup' ); ?></th>
            <th width="2%" class="title"><?php echo JTEXT::_( 'Exclude' ); ?></th>
        </tr>
        </thead>
            <?php
            $k = 0;
            $n = count($this->rows);

            for ($i = 0; $i < $n; $i++) {
                $row	=& $this->rows[$i];
                $img	= $row->ip_exclude ? 'publish_x.png' : 'tick.png';
                $task	= $row->ip_exclude ? 'js_do_ip_include' : 'js_do_ip_exclude';
                $alt	= $row->ip_exclude ? JTEXT::_( 'Click to include' ) : JTEXT::_( 'Click to exclude' );
                ?>
            <tr class="row<?php echo $k; ?>">
                <td><?php echo $i + 1 + $this->pagination->limitstart;?></td>
                <td>
                    <input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->ip; ?>" onClick="isChecked(this.checked);" />
                </td>
                <td>
                    <a href="http://<?php echo long2ip($row->ip); ?>" target="_blank" title="<?php echo JTEXT::_( 'Click opens new window' ); ?>"><?php echo long2ip($row->ip); ?></a>
                </td>
                <td>
                    <img src="<?php echo  _JSAdminImagePath;?><?php echo ($row->ip_type != 2 ? 'user.png': 'bot.png');?>" border="0" width="16" alt="<?php echo  ($row->ip_type == 2 ? JTEXT::_( 'Bot' ) : JTEXT::_( 'Browser' )) ;?>" />
                </td>
                <td><?php echo $row->nslookup; ?></td>
                <td width="10%" align="center">
                <a href="javascript:void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>');" title="<?php echo $alt; ?>"><img src="images/<?php echo $img;?>" border="0" alt="<?php echo $alt; ?>" /></a>
                </td>
            </tr>
            <?php
            $k = 1 - $k;
        } ?>
        <tfoot>
        <tr>
            <td colspan="6"><?php echo $this->pagination->getListFooter(); ?></td>
        </tr>
        </tfoot>
    </table>