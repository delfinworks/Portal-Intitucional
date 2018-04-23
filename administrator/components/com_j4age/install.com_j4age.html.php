<?php

              
/**
 * @package j4age
 * @copyright Copyright (C) 2009-@THISYEAR@ j4age Team. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * Thank you to the project j4age and it's team, on which roots this project is build on.
 */
             
          

if( !defined( '_JS_STAND_ALONE' ) && !defined( '_JEXEC' ) )
{
	die( 'JS: No Direct Access to '.__FILE__ );
}

require_once( dirname(__FILE__) .DS. 'libraries'.DS. 'template.html.php' );
$JSConf = js_JSConf::getInstance();

$JSTemplate = new js_JSTemplate();

$introduce_msg  = '<img src="'. JURI::base(true) . '/components/com_j4age/images/icon-48-js_js-logo.png" width="48" height="48" alt="j4age" title="j4age" /><br clear="all" />';
 ?>
<div style="text-align: left;">
	<div style="width:95%; margin:5px auto 5px auto; padding:5px;">
		<table style="width: 100%; padding: 0px; border-width: 0px; border-collapse: collapse; /*not working in IE 6.0, 7.0 use cellspacing=0 */ border-spacing: 0px; /* no difference */" cellspacing="0">
		<tr>
			<td style="padding: 0px; text-align: left;"><?php echo $introduce_msg; ?></td>
			<td style="padding: 0px; text-align: right; vertical-align: top; font-weight: bold;">
				j4age version:&nbsp;'<?php echo $JSConf->BuildVersion; ?>'
			</td>
		</tr>
		</table>	
	    <div>
            <h3>Your Feedback required</h3>
            <a href="http://extensions.joomla.org/extensions/site-management/site-traffic-statistics/11750" target="_blank">j4age</a> is for what you were looking for? Please let other people know about your positive experiences by providing your vote and feedback on the <a href="http://extensions.joomla.org/extensions/site-management/site-traffic-statistics/" target="_blank">Joomla JED</a> page.
	    </div>
		<div>
			<?php $JSTemplate->startBlock();
			if( count( $StatusTData->errorMsg ) > 0 )
            {
                echo "<h3 style='color:red'>".JTEXT::_( 'Errors')."</h3>";
                foreach($StatusTData->errorMsg as $msg)
                    echo "<h4>".$msg['name']."</h4>"."<p>".$msg['description']."</p>";
			}
            if( count( $StatusTData->warningMsg ) > 0 )
            {
                echo "<h3 style='color:orange'>".JTEXT::_( 'Warnings')."</h3>";
                foreach($StatusTData->warningMsg as $msg)
                echo "<h4>".$msg['name']."</h4>"."<p>".$msg['description']."</p>";
            }
            if( count( $StatusTData->infoMsg ) > 0 )
            {
                echo "<h3 style='color:blue'>".JTEXT::_( 'Info')."</h3>";
                foreach($StatusTData->infoMsg as $msg)
                echo "<h4>".$msg['name']."</h4>"."<p>".$msg['description']."</p>";
            }
             $JSTemplate->endBlock();
            ?>
		</div>
	</div>
</div>
<div style="text-align:center; color:#9F9F9F; font-size:0.9em">
   <a href="http://www.ecomize.com" target="_blank" title="Visit our Homepage"><img src="<?php echo JURI::base(true);?>/components/com_j4age/images/logo.gif" alt="ecomize" title="ecomize" /></a>
</div><div style="text-align:center; color:#9F9F9F; font-size:0.9em">
   j4age &copy;2009-<?php echo js_gmdate( 'Y' ); ?> <a href="http://www.ecomize.com" target="_blank" title="Visit our Homepage">ecomize AG</a> - All rights reserved.<br />
    <P><a href="http://www.ecomize.com" target="_blank" title="Visit our Homepage">j4age</a>
    is released under the GNU/GPL License.</P>
</div>