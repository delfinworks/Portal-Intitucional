<?php defined('_JEXEC') or die('JS: No Direct Access');
$flashPath	= JURI::base() . 'components/com_j4age/views/amline/tmpl';
?>
<div class="clr" style="margin: 5px;"></div>
<div id="submenu-box">
    <div class="t">
        <div class="t">
            <div class="t"></div>

        </div>
    </div>
    <div class="m">
        <div style="text-align:center; margin:2px auto; width:99%;">
        <!-- amline script-->
          <script type="text/javascript" src="<?php echo $flashPath;?>/swfobject.js"></script>
            <div id="<?php echo $this->chartId;?>">
                <strong>You need to upgrade your Flash Player</strong>
            </div>

            <script type="text/javascript">
                // <![CDATA[
                var so = new SWFObject("<?php echo $flashPath;?>/amline.swf", "amline", "100%", "250", "6", "#f6f6f6");
                so.addVariable("path", "<?php echo $flashPath;?>/");
                so.addVariable("settings_file", encodeURIComponent("<?php echo $flashPath;?>/amline_settings.xml"));                // you can set two or more different settings files here (separated by commas)
                //so.addVariable("data_file", encodeURIComponent("amline/amline_data.xml"));
                so.addVariable("chart_data", encodeURIComponent( '<?php echo $this->chartData; ?>' ) );                    // you can pass chart data as a string directly from this file
                //	so.addVariable("chart_settings", encodeURIComponent("<settings>...</settings>"));                 // you can pass chart settings as a string directly from this file
                so.addVariable("additional_chart_settings", encodeURIComponent('<?php echo $this->settings; ?>'));      // you can append some chart settings to the loaded ones
                //  so.addVariable("loading_settings", "LOADING SETTINGS");                                           // you can set custom "loading settings" text here
                //  so.addVariable("loading_data", "LOADING DATA");                                                   // you can set custom "loading data" text here
                //	so.addVariable("preloader_color", "#999999");
                so.write("<?php echo $this->chartId;?>");
                // ]]>
            </script>
        <!-- end of amline script -->
        </div>
    <div class="clr"></div>
    </div>
    <div class="b">
        <div class="b">
            <div class="b"></div>
        </div>
    </div>

</div>
