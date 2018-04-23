<?php defined('_JEXEC') or die('JS: No Direct Access');
		jimport('joomla.html.pane');

		$pane =& JPane::getInstance( 'tabs' );
		JHTML::_('behavior.tooltip');

        function js_is_writable($path) {

        if ($path{strlen($path)-1}=='/')
            return is__writable($path.uniqid(mt_rand()).'.tmp');

        if (file_exists($path)) {
            if (!($f = @fopen($path, 'r+')))
                return false;
            fclose($f);
            return true;
        }

        if (!($f = @fopen($path, 'w')))
            return false;
        fclose($f);
        unlink($path);
        return true;
        }
?>
		<?php echo JTEXT::_( 'Tools options - DETAILED DESCRIPTION' );?>
        <br/><br/>
		<table width="100%" border="0" cellpadding="2" cellspacing="0" class="adminForm">
		<tr>
			<td>
			<?php
				echo $pane->startPane( 'js_maintenance_pane' );

                echo $pane->startPanel( JTEXT::_( 'Extensions' ), 'extensions' );
                    ?>

                        <script language="javascript" type="text/javascript">
                        function submitbutton3(pressbutton) {
                            var form = document.adminForm;
                            form.encoding = "multipart/form-data";
                            // do field validation
                            if (form.install_directory.value == ""){
                                alert( "<?php echo JText::_('Select a folder for the upload'); ?>" );
                            } else {
                                form.submit();
                            }
                        }
                        function submitbutton4(pressbutton) {
                            var form = document.adminForm;
                            form.encoding = "multipart/form-data";
                            // do field validation
                            form.submit();
                        }
                        </script>
                        <table class="adminheading">
                        <tr>
                            <th class="install">
                            <?php echo JTEXT::_( 'Extensions' );?>
                            </th>
                        </tr>
                        </table>

                        <table class="adminform">
                        <tr>
                            <th>
                            <?php echo JTEXT::_( 'Install new Plugins' );?>
                            </th>
                        </tr>
                        <tr>
                            <td align="left">
                            <?php echo JTEXT::_( 'file' );?>
                            <input class="text_area" name="install_package" type="file" size="40"/>
                            <input class="button" type="button" onclick="document.adminForm.controller.value='installer';document.adminForm.task.value='uploadPlugin';document.adminForm.installtype.value='upload';submitbutton4();" value="<?php echo JTEXT::_( 'Install' ); ?>" />
                            </td>
                        </tr>
                        </table>

                        <input type="hidden" name="installtype" value="upload" />
                        <br />

                        <table class="adminform">
                        <tr>
                            <th>
                            <?php echo JTEXT::_( 'Install from directory ' );?>
                            </th>
                        </tr>
                        <tr>
                            <td align="left">
                            <?php echo JTEXT::_( 'Path' );?>:&nbsp;
                            <input type="text" name="install_directory" class="text_area" size="60" value="<?php echo JPATH_SITE; ?>"/>&nbsp;
                            <input type="button" class="button" value="<?php echo JTEXT::_( 'Install' ); ?>" onclick="document.adminForm.controller.value='installer';document.adminForm.task.value='installfromdir';document.adminForm.installtype.value='folder';submitbutton3()" />
                            </td>
                        </tr>
                        </table>
		                    <?php
                echo $pane->endPanel();

                echo $pane->startPanel( JTEXT::_( 'Installed Plugins' ), 'plugins' );
                    if (count($this->plugins))
                    {
                        echo '<input type="hidden" name="plugin" value=""/>';
                        $mosConfig_live_site = substr_replace(JURI::root(), "", -1, 1);
                        foreach ($this->plugins  as $plugin)
                        {
                		?>
		                    <div id="plugin<?php echo $row->id; ?>" class="plugin <?php echo $row->published? 'published':'unpublished'; ?>">
                                <div class="titlebar">
                                    <div class="tl tl1"><img src="<?php echo $mosConfig_live_site; ?>/images/M_images/blank.png" width="1" height=1" alt=" " /></div>
                                    <div class="tl tl2"><img src="<?php echo $mosConfig_live_site; ?>/images/M_images/blank.png" width="1" height=1" alt=" " /></div>
                                    <div class="tl tl3"><img src="<?php echo $mosConfig_live_site; ?>/images/M_images/blank.png" width="1" height=1" alt=" " /></div>
                                    <div class="tl">
                                        <div class="pluginname"><?php echo $plugin->name; ?></div>
                                        <div class="pluginversion"><?php echo @$plugin->version != "" ? $plugin->version : "&nbsp;"; ?></div>
                                        <div class="spacer"></div>
                                    </div>
                                </div>
                                <div class="insidebox">
                                    <div class="plugindate"><?php echo @$plugin->creationdate != "" ? $plugin->creationdate : "&nbsp;"; ?></div>
                                    <div class="pluginauthor"><?php echo JTEXT::_( 'Author' ).': ' . (!empty($plugin->author) ? $plugin->author : JTEXT::_( 'Unknown Author' )) . (!empty($plugin->authorEmail) ? ' &lt;'.$plugin->authorEmail.'&gt;' : "&nbsp;"); ?></div>
                                    <div class="pluginauthorurl"><?php echo @$plugin->authorUrl != "" ? "<a href=\"" .(substr( $plugin->authorUrl, 0, 7) == 'http://' ? $plugin->authorUrl : 'http://'.$plugin->authorUrl) ."\" target=\"_blank\">$plugin->authorUrl</a>" : "&nbsp;"; ?></div>
                                    <div class="plugintaskbar">
                                        <?php
                                        if(!$plugin->isProtected())
                                          {
                                        ?>
                                        <a href="javascript:document.adminForm.controller.value='installer';document.adminForm.task.value='uninstallPlugin';document.adminForm.plugin.value='<?php echo $plugin->id; ?>';submitbutton('uninstallPlugin');"><?php echo JTEXT::_( 'Uninstall j4age & All Statistics' ); ?></a>
                                        <?php };?>
                                        <?php echo js_renderPopupIcon(null, JTEXT::_( 'Options' ), 'index.php?option=com_j4age&amp;header=0&amp;controller=installer&amp;tmpl=component&amp;task=showPluginSettings&amp;plugin='.$plugin->id);?>
                                        <a href="javascript:document.adminForm.controller.value='installer';document.adminForm.task.value='changePluginState';document.adminForm.plugin.value='<?php echo $plugin->id; ?>';submitbutton('changePluginState');"><img id="pluginstate<?php echo $plugin->id; ?>" src="images/<?php echo $plugin->published?'publish_g.png" title="'.JTEXT::_( 'Published' ).'"':'publish_x.png" title="'.JTEXT::_( 'Unpublished' ).'"'; ?>" border="0" /></a>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                    }
                    else
                    {
                        ?>
                        <div><?php echo JTEXT::_( 'No plugins installed' ); ?></div>
                        <?php
                    }
                    ?>
                <?php
                echo $pane->endPanel();
				echo $pane->startPanel( JTEXT::_( 'Maintenance' ), 'maintenance' );
					require_once( dirname(__FILE__) .DS. 'maintenanceTab.php' );
				echo $pane->endPanel();

				echo $pane->startPanel( JTEXT::_( 'Export' ), 'export' );
				     require_once( dirname( __FILE__ ) .DS. 'exportTab.php' );
				echo $pane->endPanel();

				/* working code - temporary removed due to release */
				echo $pane->startPanel( JTEXT::_( 'WHOIS/TLD' ), 'tld' );
                     require_once( dirname( __FILE__ ) .DS. 'tldTab.php' );
				echo $pane->endPanel();



				/* no working options on backup tab, removed due to release
				echo $pane->startPanel( JTEXT::_( 'Backup' ), 'backup' );
				include_once( dirname( __FILE__ ) .DS. 'backup.php' );
				$JSBackup = new js_JSBackup();
				echo $JSBackup->getBackupTab();
				echo $pane->endPanel();
				*/

				echo $pane->endPane();

			?>
			</td>
		</tr>
		</table>


