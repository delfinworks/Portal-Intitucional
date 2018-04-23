<?php defined('_JEXEC') or die('JS: No Direct Access');
$JSTemplate = new js_JSTemplate();

?>

		<div style="font-size: 1px;">&nbsp;</div><!-- This div is needed to show content of tab correctly in \'IE 7.0\' in \'j1.5.6 Legacy\'. Tested in: FF, IE, j1.0.15, j1.5.6 and works OK -->
		<table class="adminform" width="100%" border="0" cellspacing="0" cellpadding="0">
            <!--tr>
                <td nowrap="nowrap"><label for="include_summarized"><?php echo JTEXT::_( 'Count including summarized data' ); ?></label></td>
                <td>
                    <input type="checkbox" name="include_summarized" id="include_summarized"<?php echo ( $this->JSConf->include_summarized ? ' checked="checked"' : '' ); ?> onclick="if (document.adminForm.include_summarized.checked == true) document.adminForm.show_summarized.checked = true; else document.adminForm.show_summarized.checked = false;" />
                </td>
                <td width="100%">
                    <?php echo $JSTemplate->jsToolTip( JTEXT::_( '<b>If off summarization works like purge option (summarized data not visible nor counted).<br/>If agreed statistics will be counted with summarized data.</b><br/><br/>Eg.<br/>if On:<br/><b>35 [21]</b> - current + summarized [summarized]<br/><br/>if Off:<br/><b>14</b> - only current' ) ); ?>
                    &nbsp;
                    <em>
                        <?php echo ( $this->LastSummarizationDate ? JTEXT::_( 'Last summarization' ) . ':&nbsp;'    . $this->LastSummarizationDate : JTEXT::_( 'No summarized data availiable' ) ); ?>
                    </em>
                </td>
            </tr-->
            <!--tr>
                <td nowrap="nowrap"><label for="show_summarized"> <?php echo JTEXT::_( 'Show summarized data' );?></label></td>
                <td>
                    <input type="checkbox" disabled="disabled" name="show_summarized" id="show_summarized"<?php echo ( $this->JSConf->show_summarized ? ' checked="checked"' : '' ); ?>/>
                </td>
                <td>
                    <?php echo $JSTemplate->jsToolTip( JTEXT::_( 'This option apply only if <i>Count including summarized data</i> is On<br/><br/>It show/hide value in rectangle brackets.<br/><br/>Eg.<br/>show / hide<br/><b>35 [21]</b> / <b>35</b>' ) ); ?>
                </td>
            </tr-->
            <tr>
                <td nowrap="nowrap"><label for="enable_index_clients_useragent"> <?php echo JTEXT::_( 'Index clients by useragent strings' );?></label></td>
                <td>
                    <input type="checkbox" name="enable_index_clients_useragent" id="enable_index_clients_useragent"<?php echo ( $this->enable_index_clients_useragent ? ' checked="checked"' : '' ); ?>/>
                </td>
                <td>
                    <?php echo $JSTemplate->jsToolTip( JTEXT::_( 'Adding a index would increase your DB size, but would speed up specific processes' ) ); ?>
                </td>
            </tr>
            <tr>
                <td nowrap="nowrap"><label for="enable_index_impressions_visit"> <?php echo JTEXT::_( 'Index impressions by visit' );?></label></td>
                <td>
                    <input type="checkbox" name="enable_index_impressions_visit" id="enable_index_impressions_visit"<?php echo ( $this->enable_index_impressions_visit ? ' checked="checked"' : '' ); ?>/>
                </td>
                <td>
                    <?php echo $JSTemplate->jsToolTip( JTEXT::_( 'Adding a index would increase your DB size, but would speed up specific processes' ) ); ?>
                </td>
            </tr>
            <tr>
                <td nowrap="nowrap"><label for="enable_index_visits_changed_at"> <?php echo JTEXT::_( 'Index visits by visit time' );?></label></td>
                <td>
                    <input type="checkbox" name="enable_index_visits_changed_at" id="enable_index_visits_changed_at"<?php echo ( $this->enable_index_visits_changed_at ? ' checked="checked"' : '' ); ?>/>
                </td>
                <td>
                    <?php echo $JSTemplate->jsToolTip( JTEXT::_( 'Adding a index would increase your DB size, but would speed up specific processes' ) ); ?>
                </td>
            </tr>
            <tr>
                <td nowrap="nowrap"><label for="enable_index_visits_ip"> <?php echo JTEXT::_( 'Index visits by IP' );?></label></td>
                <td>
                    <input type="checkbox" name="enable_index_visits_ip" id="enable_index_visits_ip"<?php echo ( $this->enable_index_visits_ip ? ' checked="checked"' : '' ); ?>/>
                </td>
                <td>
                    <?php echo $JSTemplate->jsToolTip( JTEXT::_( 'Adding a index would increase your DB size, but would speed up specific processes' ) ); ?>
                </td>
            </tr>
		</table>
