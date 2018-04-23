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
					<div class="button1-left"><div class="refresh"><a href="index.php?option=com_j4age&controller=installer&task=default&view=install&layout=default" title="<?php echo JText::_('Check Again'); ?>"><?php echo JText::_('Check Again'); ?></a></div></div>
					<!--div class="button1-right"><div class="prev"><a href="index.php?view=language" title="<?php echo JText::_('JPrevious'); ?>"><?php echo JText::_('JPrevious'); ?></a></div></div-->
					<div class="button1-left"><div class="next"><a href="index.php?option=com_j4age&controller=installer&task=default&view=install&layout=steps" title="<?php echo JText::_('Next'); ?>"><?php echo JText::_('Next'); ?></a></div></div>
				</div>
				<span class="step"><?php echo JText::_('Post-Installation'); ?></span>
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
				<h2><?php echo JText::sprintf('Pre-installation check for %s:', ''.$this->buildVersion); ?></h2>
				<div class="install-text">
					<?php echo JText::_('System Requirements'); ?>
				</div>
				<div class="install-body">
					<div class="t">
						<div class="t">
							<div class="t"></div>
						</div>
					</div>
					<div class="m">
						<fieldset>
							<table class="content">
								<tbody>
<?php foreach ($this->options as $option) : ?>
								<tr>
									<td class="item" valign="top">
										<?php echo $option->label; ?>
									</td>
									<td valign="top">
										<span class="<?php echo ($option->state) ? 'green' : 'red'; ?>">
											<?php echo JText::_(($option->state) ? 'Yes' : 'No'); ?>
										</span>
										<span class="small">
											<?php echo $option->notice; ?>&nbsp;
										</span>
									</td>
								</tr>
<?php endforeach; ?>
								</tbody>
							</table>
						</fieldset>
					</div>
					<div class="b">
						<div class="b">
							<div class="b"></div>
						</div>
					</div>

					<div class="clr"></div>
				</div>

				<div class="newsection"></div>

				<h2><?php echo JText::_('Recommended Settings'); ?></h2>
				<div class="install-text">
					<?php echo JText::_('Please find here the minimum recommended requirements'); ?>
				</div>
				<div class="install-body">
					<div class="t">
						<div class="t">
							<div class="t"></div>
						</div>
					</div>
					<div class="m">
						<fieldset>
							<table class="content">
								<thead>
								<tr>
									<td class="toggle">
										<?php echo JText::_('Directive'); ?>
									</td>
									<td class="toggle">
										<?php echo JText::_('Recommended'); ?>
									</td>
									<td class="toggle">
										<?php echo JText::_('Actual'); ?>
									</td>
								</tr>
								</thead>
								<tbody>
<?php foreach ($this->settings as $setting) : ?>
								<tr>
									<td class="item">
										<?php echo $setting->label; ?>:
									</td>
									<td class="toggle">
										<span>
										<?php echo JText::_(($setting->recommended) ? 'On' : 'Off'); ?>
										</span>
									</td>
									<td>
										<span class="<?php echo ($setting->state === $setting->recommended) ? 'green' : 'red'; ?>">
										<?php echo JText::_(($setting->state) ? 'On' : 'Off'); ?>
										</span>
                                        <span class="small">
											<?php echo $setting->notice; ?>&nbsp;
										</span>
									</td>
								</tr>
<?php endforeach; ?>
								</tbody>
							</table>
						</fieldset>
					</div>
					<div class="b">
						<div class="b">
							<div class="b"></div>
						</div>
					</div>
					<div class="clr"></div>
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


