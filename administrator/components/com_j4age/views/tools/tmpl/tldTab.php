<?php defined('_JEXEC') or die('JS: No Direct Access');?>



<div style="font-size: 1px;">&nbsp;</div><!-- This div is needed to show content of tab correctly in \'IE 7.0\' in \'j1.5.6 Legacy\'. Tested in: FF, IE, j1.0.15, j1.5.6 and works OK -->
<div>
    <b><?php echo JTEXT::_( 'Perform WHOIS query for provided IP or host address' );?></b><br/>
    <?php echo JTEXT::_( 'Perform WHOIS query for provided IP or host address - DETAILED DESCRIPTION' );?><br/>
    <br/>
    <small><?php echo JTEXT::_( 'eg.' );?> "97.102.244.231", "googlebot.com"</small><br/>
    <input type="text" name="address_to_check" value="" class="text_area" />
    <input type="button" name="js_tld_view_tld_check" value="<?php echo JTEXT::_( 'Check' );?>" onclick="newWin = window.open('index.php?option=com_j4age&amp;task=js_view_whois_popup&amp;address_to_check='+document.adminForm.address_to_check.value+'&amp;no_html=1','whois','resizable=yes,status=no,toolbar=no,location=no,scrollbars=yes,width=690,height=560'); newWin.focus(); return false;" />
</div>

