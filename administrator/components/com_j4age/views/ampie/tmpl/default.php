<?php defined('_JEXEC') or die('JS: No Direct Access');
$flashPath	= JURI::base() . 'components/com_j4age/views/ampie/tmpl';
?>
<div class="clr" style="margin: 5px;"></div>
<div id="submenu-box">
    <div class="t">
        <div class="t">
            <div class="t"></div>

        </div>
    </div>
    <div class="m">
        <div style="text-align:center; margin:2px auto; width:100%;">
        <!-- ampie script-->
        <?php
          foreach($this->charts as $chart)
          {
        ?>

          <script type="text/javascript" src="<?php echo $flashPath;?>/swfobject.js"></script>
            <div id="<?php echo $chart->getId();?>" style="float:left;width:<?php echo (count($this->charts) > 1 ? (100 / 2): 100);?>%;">
                <strong>You need to upgrade your Flash Player</strong>
            </div>

            <script type="text/javascript">
                // <![CDATA[
                var so = new SWFObject("<?php echo $flashPath;?>/ampie.swf", "ampie", "100%", "250", "6", "#f6f6f6");//#f6f6f6
                so.addVariable("path", "<?php echo $flashPath;?>/");
                so.addVariable("settings_file", encodeURIComponent("<?php echo $flashPath;?>/ampie_settings.xml"));                // you can set two or more different settings files here (separated by commas)
                //so.addVariable("data_file", encodeURIComponent("amline/amline_data.xml"));
                so.addVariable("chart_data", encodeURIComponent( '<?php echo $chart->getDataXML(); ?>' ) );                    // you can pass chart data as a string directly from this file
                //	so.addVariable("chart_settings", encodeURIComponent("<settings>...</settings>"));                 // you can pass chart settings as a string directly from this file
                so.addVariable("additional_chart_settings", encodeURIComponent('<?php echo $chart->getSettingsXML(); ?>'));      // you can append some chart settings to the loaded ones
                //  so.addVariable("loading_settings", "LOADING SETTINGS");                                           // you can set custom "loading settings" text here
                //  so.addVariable("loading_data", "LOADING DATA");                                                   // you can set custom "loading data" text here
                //	so.addVariable("preloader_color", "#999999");
                so.write("<?php echo $chart->getId();?>");
                // ]]>
            </script>
        <!-- end of ampie script -->
        <?php
          }
        ?>
        </div>

    <div class="clr"></div>
    </div>
    <div class="b">
        <div class="b">
            <div class="b"></div>
        </div>
    </div>

</div>
