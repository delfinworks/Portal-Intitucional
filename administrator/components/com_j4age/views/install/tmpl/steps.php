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
$keepRedirecting = JRequest::getVar('rGo', 0);
if($keepRedirecting){
?>

<input type="hidden" name="layout" value="default"/>
<script language="JavaScript" type="text/javascript">
<!--
    document.adminForm.layout.value='steps';
    setTimeout("submitbutton('executeAll')",1000);
// -->
</script>
<?php }?>
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
                    <div class="button1-right"><div class="prev"><a href="index.php?option=com_j4age&controller=installer&task=default&view=install&layout=default" title="<?php echo JText::_('Back'); ?>"><?php echo JText::_('Back'); ?></a></div></div>
					<div class="button1-left"><div class="refresh"><a href="index.php?option=com_j4age&controller=installer&task=default&view=install&layout=steps" title="<?php echo JText::_('Refresh'); ?>"><?php echo JText::_('Check again'); ?></a></div></div>
					<div class="button1-left"><div class="next"><a href="javascript:document.adminForm.controller.value='installer';document.adminForm.view.value='install';submitbutton('executeAll');" title="<?php echo JText::_('All Steps at Once'); ?>"><?php echo JText::_('Perform all at once'); ?></a></div></div>
					<div class="button1-left"><div class="next"><a href="javascript:document.adminForm.controller.value='installer';document.adminForm.view.value='install';submitbutton('executeStep');" title="<?php echo JText::_('Next'); ?>"><?php echo JText::_('Step by Step'); ?></a></div></div>
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
				<h2><?php echo JTEXT::_( 'Datebase Migration / Update' ); ?>
                    <?php
                if( $this->currentVersion == $this->nextVersion)
                {
                    echo  " for <strong>".$this->nextVersion."</strong> - Step ".$this->nextStep." of ".$this->nextVersionStepAmount." - ".($this->totalAmountOfStepsLeft)." Steps remaining to <strong>".$this->versionToBeUpgraded."</strong>";
                }
                else
                {
                    echo " from version <strong>".$this->currentVersion."</strong> to <strong>".$this->nextVersion."</strong> - Step ".$this->nextStep." of ".$this->nextVersionStepAmount." - ".($this->totalAmountOfStepsLeft)." Steps remaining to <strong>".$this->versionToBeUpgraded."</strong>";
                }

                ?>

                </h2>
				<div class="install-text">
                    <div>
                     Just few seconds away from being able to few your webpage statistics!<br/><br/> What happens next? j4age does not fill the required static data into the database during the initial physical installation to prevent conflicts with the database or PHP time-out. Same also valid for any required update process, if you have installed a newer version of j4age.
                    </div>
                    <div>
                    <br/>Some steps might end into an SQL or PHP timeout for big databases, this should be not a problem as we considered that fact. Just refresh the screen to perform it again. If you end in a never ending circle, please check manually if this step has done it's job and increment the value of 'current_step' within your DB table #__jstats_configuration by 1. We can't do it automatically in this case as we have to be sure that the SQL statement was successfully performed!
                    </div>

                    <div style="color:orange">
                    <br/>Press 'Perform All at Once' to start
                    </div>
                    <div style="color:red">
                    <br/>Perform an Database backup before you start! Once j4age has started the upgrade / post-installation, the JoomlaStats and all JoomlaStats Modules wont work anymore. Disable them to prevent error messages.
                    </div>
				</div>
				<div class="install-body">
					<div class="t">
						<div class="t">
							<div class="t"></div>
						</div>
					</div>
					<div class="m">
						<fieldset>
                           <?php
                             foreach($this->steps_per_Versions as $version => $stepsPerVersion)
                             {
                                //$isNextStep = ( $version == $this->nextVersion);
                                ?>
                                   <p>
                                   <h1><?php echo ($this->buildVersion == $version? JTEXT::_( 'Static Data' ) : JTEXT::_( 'Upgrade to' )." $version") ?></h1>
                                    <br/>
                                   <?php foreach($stepsPerVersion as $index=>$step)
                                    {
                                      $isNextStep = ( $version == $this->nextVersion) && ($index == $this->nextStep -1 );
                                      if( $isNextStep ) echo '<font color="#0000ff">';
                                      echo "<br/><strong>Step ".($index + 1)." of ".(count($stepsPerVersion))."</strong><br/>".$step->description." (".$step->number.")<br/>";
                                      echo "<strong>SQL: </strong><br/>";
                                      foreach($step->query as $queryIndex=>$query)
                                      {
                                          $queryStr = $query;
                                          $queryLength = strlen($queryStr);
                                          if($queryLength > 600)
                                          {
                                             $queryStr = substr($queryStr, 0, 200).'...';
                                          }
                                          echo $queryIndex.'. '.$queryStr . '<br/>';
                                      }
                                       if( $isNextStep ) echo "</font>";
                                    };?>
                                   </p>
                                <?php
                             }
                           ?>
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

