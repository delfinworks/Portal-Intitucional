<?php defined('_JEXEC') or die('JS: No Direct Access');

$JSTemplate = new js_JSTemplate();

?>
<div style="text-align: left;"><!-- needed by j1.0.15 -->
<?php
echo $JSTemplate->startBlock(  );
echo JTEXT::_( 'To upgrade j4age, do not use this function!! Use instead the Joomla Installer (Menu -> Extensions -> Install/Uninstall -> Components) and then install new j4age version.<br/>Previously collected statistics will be retained!' );
echo $JSTemplate->endBlock(  );
?>
</div><!-- needed by j1.0.15 -->


