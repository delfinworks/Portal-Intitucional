<?php defined('_JEXEC') or die('JS: No Direct Access');
		jimport('joomla.html.pane');
		$pane =& JPane::getInstance( 'tabs' );

		$JSTemplate = new js_JSTemplate();
		$JSTemplate->jsLoadToolTip();

?>

		<table width="100%" border="0" cellpadding="2" cellspacing="0" class="adminForm">
		<tr>
			<td>
			<?php
				echo $pane->startPane( 'js_configuration_pane' );

				echo $pane->startPanel( JTEXT::_( 'Common' ), 'general' );
                    require_once( dirname(__FILE__) .DS. 'commonTab.php' );
				echo $pane->endPanel();

				echo $pane->startPanel( JTEXT::_( 'Performance' ), 'performance' );
                    require_once( dirname(__FILE__) .DS. 'performanceTab.php' );
				echo $pane->endPanel();

				echo $pane->endPane();
			?>
			</td>
		</tr>
		</table>


