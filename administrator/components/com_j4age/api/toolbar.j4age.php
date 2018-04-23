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

/**
* Utility class for the submenu
*
* @package		Joomla
*/
class js_JSSubToolBarMenu
{
    /**
     * writes the header of JoomlaStats with all actions as links
     *
     * @param array $MenuArrIdAndText
     * @return string
     *
     * @todo mic: has to be reworked, because of the changes with translations to JTEXT
     */
    function addMenuItems( $MenuArrIdAndText )
    {

        foreach( $MenuArrIdAndText as $id => $entry ) {

            if ($id == 'detailVisitInformation')
                continue; //easy hack, should be removed

            $description = null;
            if(isset($entry['label']))
            {
                $description = $entry['label'];
            }
            $view = null;
            if(isset($entry['view']))
            {
                $view = $entry['view'];
            }
            $controller = null;
            if(isset($entry['controller']))
            {
                $controller = $entry['controller'];
            }
            $task = null;
            if(isset($entry['task']))
            {
                $task = $entry['task'];
            }
            $tpl = null;
            if(isset($entry['tpl']))
            {
                $tpl = $entry['tpl'];
            }
            $linkType = null;
            if(isset($entry['linkType']))
            {
                $linkType = $entry['linkType'];
            }

            if(empty($task))
            {
               $task = "default";//$id;
            }

            if(empty($linkType))
            {
               $linkType = "link";
            }

            /**
             * We only show links, if there view is physically available
             * @author Andreas Halbig
             */
            if( !file_exists( dirname(__FILE__).DS.'..' .DS. 'views' .DS. $view .DS. 'view.html.php' ) )
            {
                continue;
            }

            if(strcmp($linkType, 'get') == 0 || strcmp($linkType, 'post') == 0)
            {
                js_JSSubToolBarMenu::addEntry($description, 'javascript:if(document.adminForm.limitstart) document.adminForm.limitstart.value=0;document.adminForm.mid.value=\'' .$id. '\';document.adminForm.method=\''.$linkType.'\';'.( $tpl == null ? '' : 'document.adminForm.tpl.value=\'' .$tpl. '\'').';document.adminForm.view.value=\'' .$view. '\';document.adminForm.controller.value=\'' .$controller. '\'; submitbutton(\'' .$task. '\')');
            }
            else if( strcmp($linkType, 'link') == 0 )
            {
                js_JSSubToolBarMenu::addEntry($description, "index.php?option=com_j4age&limitstart=0&mid=".$id.( $tpl == null ? '' : '&tpl=' .$tpl).( $view == null ? '' : '&view=' .$view).( $controller == null ? '' : '&controller=' .$controller).( $task == null ? '' : '&task=' .$task));
            }
        }
    }


	function addEntry($name, $link = '', $active = false)
	{
		$menu = &JToolBar::getInstance('js_submenu');
		$menu->appendButton($name, $link, $active);
	}

    function render()
    {
		// Lets get some variables we are going to need
		$menu = JToolBar::getInstance('js_submenu');
        $list = null;
        /**
         *  _bar is protected within Joomla 1.6, but we have a method instead
         */
        if(method_exists($menu, "getItems"))
        {
            $list = $menu->getItems();
        }
		else
        {
            $list = $menu->_bar;
        }

		if (!is_array($list) || !count($list)) {
			return null;
		}

        $txt = '<div id="submenu-box">
                <div class="t">
                    <div class="t">
                        <div class="t"></div>

                    </div>
                </div>
                <div class="m">
        ';


		$hide = JRequest::getInt('hidemainmenu');
		$txt .= "<ul id=\"submenu\">\n";

		$n = 0;

		$txt .=
		'<table width="100%" border="0" cellpadding="2" cellspacing="5">' . "\n"
		. '<tr><td width="10">&nbsp;</td>'; 

		/*
		 * Iterate through the link items for building the menu items
		 */
		foreach ($list as $item)
		{
            $itemTxt = $item[0];
            $itemLink = $item[1];
            $itemActive = isset ($item[2]) ? $item[2] : 0;

			$n++;
			//if( strlen( $id ) == 3 )
            {
				// we hit a menu item (not an empty line for example)
				if( ( $n != 1 ) && ( ( $n - 1 ) % 6 == 0 ) ) {
					// We just started a new line and we have some items left, so start a new line
					$txt .= '<tr><td width="10">&nbsp;</td>';	// start with same whitespace on the left
				}

				// $html .= "<a href=\"index2.php?option=com_j4age&task=$id&d=".$this->d."&m=".$this->m."&y=".$this->y."\">$description</a>";
				$txt .= '<td style="text-align:left" class="'.($itemActive == 1? 'nolink active js_menu_active' : 'nolink js_menu_inactive').'"><a href="' . JFilterOutput::ampReplace($itemLink) . '">' . $itemTxt . '</a></td>';

				if( $n % 6 == 0 ) {
					$txt .= '<td>&nbsp;</td></tr>' . "\n";
				}
			}
		}

		if( $n % 6 != 0 ) {
			// if we didn't just finish the row than do it now.
			// mic: leaving that here results in XHTML.error because 1 tr is too much
			//$html .= '</tr>' . "\n";
		}

		$txt .= '</table>' . "\n";
        $txt .= '
			<div class="clr"></div>
			</div>
			<div class="b">
				<div class="b">
		 			<div class="b"></div>
				</div>
			</div>

		</div>
        ';
		return $txt;
    }

    function renderInJoomlaLayout()
    {
		// Lets get some variables we are going to need
		$menu = JToolBar::getInstance('js_submenu');
		$list = $menu->_bar;

		if (!is_array($list) || !count($list)) {
			return null;
		}

        $txt = '<div id="submenu-box">
                <div class="t">
                    <div class="t">
                        <div class="t"></div>

                    </div>
                </div>
                <div class="m">
        ';


		$hide = JRequest::getInt('hidemainmenu');
		$txt .= "<ul id=\"submenu\">\n";

		/*
		 * Iterate through the link items for building the menu items
		 */
		foreach ($list as $item)
		{
            $itemTxt = $item[0];
            $itemLink = $item[1];
            $itemActive = isset ($item[2]) ? $item[2] : 0;

			$txt .= "<li>\n";
			if ($hide)
			{
				if ($itemActive == 1) {
					$txt .= "<span class=\"nolink active\">".$itemTxt."</span>\n";
				}
				else {
					$txt .= "<span class=\"nolink\">".$itemTxt."</span>\n";
				}
			}
			else
			{
				if ($itemActive == 1) {
					$txt .= "<a class=\"active\" href=\"".JFilterOutput::ampReplace($itemLink)."\">".$itemTxt."</a>\n";
				}
				else {
					$txt .= "<a href=\"".JFilterOutput::ampReplace($itemLink)."\">".$itemTxt."</a>\n";
				}
			}
			$txt .= "</li>\n";
		}

		$txt .= "</ul>\n";
        $txt .= '
			<div class="clr"></div>
			</div>
			<div class="b">
				<div class="b">
		 			<div class="b"></div>
				</div>
			</div>

		</div>
        ';
		return $txt;
	}
}

class js_JSToolBarMenu extends JToolBarHelper
{

	function CONFIG_MENU() {

		JToolBarHelper::title( 'j4age'.': <small><small>[ ' . JTEXT::_( 'Configuration' ) . ' ]</small></small>', 'big-logo-icon.png' ); // this generate demand for css style 'icon-48-js_js-logo'

        js_JSToolBarMenu::jsButton('js_default', JTEXT::_( 'Default' ) , 'js_do_configuration_set_default', 'configuration', 'maintenance');
        js_JSToolBarMenu::jsButton('js_save', JTEXT::_( 'Save' ) , 'js_do_configuration_save', 'configuration', 'maintenance');
        js_JSToolBarMenu::jsButton('js_apply', JTEXT::_( 'Apply' ) , 'js_do_configuration_apply', 'configuration', 'maintenance');
        js_JSToolBarMenu::jsButton('js_cancel', JTEXT::_( 'Cancel' ) , 'js_view_statistics_default', 'configuration', 'maintenance');
	}

	function TOOLS_MENU() {

		JToolBarHelper::title( 'j4age'.': <small><small>[ ' . JTEXT::_( 'Tools' ) . ' ]</small></small>', 'js_js-logo.png' ); // this generate demand for css style 'icon-48-js_js-logo'

		//summarization disabled since v2.5.0.313 (It increase DB size instead of decrease)
		//JToolBarHelper::custom('js_view_summarize', 'js_summarize.png', 'js_summarize.png', JTEXT::_( 'Summarize' ), false);

        js_JSToolBarMenu::jsButton('js_uninstall', JTEXT::_( 'Uninstall' ) , 'js_view_uninstall', 'uninstall', 'maintenance');
        js_JSToolBarMenu::jsButton('js_back', JTEXT::_( 'Back' ) , 'js_view_statistics_default', '', 'main');
	}

	function UNINSTALL_MENU() {

		JToolBarHelper::title( 'j4age'.': <small><small>[ ' . JTEXT::_( 'Uninstall' ) . ' ]</small></small>', 'js_js-logo.png' ); //this generate demand for css style 'icon-48-js_js-logo'

        js_JSToolBarMenu::jsButton('js_uninstall', JTEXT::_( 'Uninstall' ) , 'js_do_uninstall', 'uninstall', 'maintenance');
		//JToolBarHelper::custom('js_do_uninstall', 'js_uninstall.png', 'js_uninstall.png', JTEXT::_( 'Uninstall' ), false);
		JToolBarHelper::custom('js_view_tools', 'js_back.png', 'js_back.png', JTEXT::_( 'Back' ), false);
	}

	function BACK_TO_STAT_MENU( $task_name ) {

		JToolBarHelper::title( 'j4age'.': <small><small>[ '.$task_name.' ]</small></small>', 'js_js-logo.png' ); // this generate demand for css style 'icon-48-js_js-logo'

        js_JSToolBarMenu::jsButton('js_back', JTEXT::_( 'Back' ) , 'js_view_statistics_default', '', 'main');
	}

	function BACK_TO_MAINTENANCE_MENU( $task_name ) {

		JToolBarHelper::title( 'j4age'.': <small><small>[ '.$task_name.' ]</small></small>', 'js_js-logo.png' ); //this generate demand for css style 'icon-48-js_js-logo'

        js_JSToolBarMenu::jsButton('js_back', JTEXT::_( 'Back' ) , 'js_view_tools', 'graphics', 'maintenance');
	}

	function DEFAULT_MENU( $task_name ) {

		JToolBarHelper::title( 'j4age'.': <small><small>[ '.$task_name.' ]</small></small>', 'js_js-logo.png' ); //this generate demand for css style 'icon-48-js_js-logo'

        //js_JSToolBarMenu::jsButton('js_configuration', JTEXT::_( 'Configuration' ) , 'js_view_configuration', 'graphics', 'maintenance');
        JToolBarHelper::divider( );
        js_JSToolBarMenu::custom('js_view_help', 'about', 'about', JTEXT::_( 'About' ), false);
	}

   /**
	* Writes a configuration button and invokes a cancel operation (eg a checkin)
	* @param	string	The name of the component, eg, com_content
	* @param	int		The height of the popup
	* @param	int		The width of the popup
	* @param	string	The name of the button
	* @param	string	An alternative path for the configuation xml relative to JPATH_SITE
	* @since 1.0
	*/
	function jsButton( $icon, $alt = 'Button', $task = 'default', $view = null, $controller = null, $list = false, $hide = false, $params = array())
	{
		$user =& JFactory::getUser();
		/*if ($user->get('gid') != 25) {
			return;
		}*/

 		//$component	= urlencode( $component );
		//$path		= urlencode( $path );
		$bar = & JToolBar::getInstance('toolbar');


        $link = js_JSToolBarMenu::getLinkCommand($alt, $task, $list, $hide, $view, $controller, $params );
        $icon	= preg_replace('#\.[^.]*$#', '', $icon);
        
		// Add a configuration button
		$bar->appendButton( 'Link', $icon, $alt, $link );
	}

    /**
	 * Get the JavaScript command for the button
	 *
	 * @access	private
	 * @param	string	$name	The task name as seen by the user
	 * @param	string	$task	The task used by the application
	 * @param	???		$list
	 * @param	boolean	$hide
	 * @return	string	JavaScript command string
	 * @since	1.5
	 */
	function getLinkCommand($name, $task = 'default', $list, $hide = false, $view, $controller, $params = array())
	{
        $link = 'javascript:';
		if ($list) {
            $todo		= JString::strtolower(JText::_( $name ));
            $message	= JText::sprintf( 'Please make a selection from the list to', $todo );
            $message	= addslashes($message);
			$link .= "if(document.adminForm.boxchecked.value==0){alert('$message');}else{ ";
		}
        if($hide)
        {
            $link .= 'hideMainMenu();';
        }
        if($controller)
        {
            $link .= "document.adminForm.controller.value='$controller';";
        }
        if($view)
        {
            $link .= "document.adminForm.view.value='$view';";
        }
        foreach($params as $key=>$value)
        {
            $link .= "document.adminForm.$key.value='$value';";
        }
        if($task)
        {
            $link .= "submitbutton('$task');";
        }
        else
        {
            $link .= "submitbutton('display');";
        }
        if ($list) {
            $link .= " }";
        }

		return $link;
	}
}

