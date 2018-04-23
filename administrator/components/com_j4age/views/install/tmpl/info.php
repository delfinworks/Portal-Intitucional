<?php
/**
 * @version		$Id: default.php 12772 2009-09-18 02:23:53Z eddieajau $
 * @package		Joomla.Installation
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
//JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the JavaScript behaviors.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>

<script language="JavaScript" type="text/javascript">
<!--
	function validateForm(frm, task) {
		Joomla.submitform(task);
	}
// -->
</script>

<div id="stepbar">
	<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
	<div class="m">
		<div class="box"></div>
  	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>

<div id="right">
	<div id="rightpad">
		<div id="step">
			<div class="t">
				<div class="t">
					<div class="t"></div>
				</div>
			</div>
			<div class="m">
				<div class="far-right">
                    <div class="button1-left"><div class="next"><a href="index.php?option=com_j4age" title="<?php echo JText::_('Close'); ?>"><?php echo JText::_('Close'); ?></a></div></div>
				</div>
				<span class="step"><?php echo JText::_('Congratulations'); ?></span>
			</div>
			<div class="b">
				<div class="b">
					<div class="b"></div>
				</div>
			</div>
		</div>
		<div id="installer">
			<div class="t">
				<div class="t">
					<div class="t"></div>
				</div>
			</div>
			<div class="m">
                <div>
                    Congratulations you successfully finished the installation; Ecomize is hoping you will enjoy this component!<br/><br/>
                </div>
                <h2><?php echo JText::sprintf('About ecomize'); ?></h2>
                <div class="install-text">
                    <img src="<?php echo JURI::base(true) . '/components/com_j4age/images/logo.gif';?>" alt="ecomize" title="ecomize" />
                    <strong>ecomize</strong> was founded in 2004 by a team of SAP CRM, Marketing and Business specialists, originally as a Swiss public limited company with a focus on organisation and personality development. With established expertise in SAP CRM we now operate successfully Europe-wide from offices in the UK, Norway, Germany and Switzerland. Together with our partners we provide comprehensive expertise, economic know-how and overall views. From our locations we are able to offer cost-effective services around Europe to German and English speaking customers.
                    <p>Our special expertise is within the SAP sphere</p>
                    <ul>
                        <li>CRM</li>
                        <li>Web-Channel</li>
                        <li>Mobile Applications</li>
                        <li>NetWeaver</li>
                    </ul>
                    <p>Internet Sales Web-Shops, Mobile Sales, e-billing,... are not just only terms for us. We make it happen!</p>
                </div>
                <div class="newsection"></div>
    
				<h2><?php echo JText::sprintf('Your Feedback Required'); ?></h2>
				<div class="install-text">
                    <a href="http://extensions.joomla.org/extensions/site-management/site-traffic-statistics/11750" target="_blank">j4age</a> is for what you were looking for? Please let other people know about your positive experiences by providing your vote and feedback on the <a href="http://extensions.joomla.org/extensions/site-management/site-traffic-statistics/" target="_blank">Joomla JED</a> page.
				</div>

				<div class="newsection"></div>

				<h2><?php echo JText::_('Donate this project'); ?></h2>

				<div class="install-text">
                    <a href="http://www.ecomize.com/en/j4age.html" target="_blank" title="j4age"><img src="http://www.paypal.com/en_US/i/btn/x-click-but04.gif" alt="j4age" title="j4age" /></a>
                    <p>Using j4age is easy and free, but maintaining and developing it unfortunately not. Support the j4age project and donate to allow further developments on this component</p>
				</div>
				<div class="clr"></div>
			</div>
			<div class="b">
				<div class="b">
					<div class="b"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="clr"></div>

