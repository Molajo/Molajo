<?php
/**
 * @version     $id: aloha.php
 * @package     Molajo
 * @subpackage  Aloha Editor
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Aloha Editor Plugin
 *
 * @package Aloha
 */
class plgEditorAloha extends MolajoApplicationPlugin
{
    /**
     * onInit
     *
     * Load the JS
     *
     * @return
     */
	function onInit()
	{
		JHTML::_('script', 'editors/aloha/aloha/aloha.js', true, true);
		JHTML::_('script', 'editors/aloha/aloha/plugins/com.gentics.aloha.plugins.Format/plugin.js', true, true);
		JHTML::_('script', 'editors/aloha/aloha/plugins/com.gentics.aloha.plugins.Table/plugin.js', true, true);
		JHTML::_('script', 'editors/aloha/aloha/plugins/com.gentics.aloha.plugins.List/plugin.js', true, true);

		return;
	}

	/**
	 * Set the editor content.
	 *
	 * @param	string	The id of the editor field.
	 * @param	string	The content to set.
	 */

    /**
     * onSetContent
     * @param  $id
     * @param  $html
     * @return string
     */
	function onSetContent($id, $html)
	{
		return 'document.id(\''.$id.'\').retrieve(\'Aloha\').setContent('.$html.');'."\n";
	}


    /**
     * onGetContent
     *
     * Get the editor content
     *
     * @param  $id
     * @return string
     */
	function onGetContent($id)
	{
		return 'document.id(\''.$id.'\').retrieve(\'Aloha\').setContent();'."\n";
	}

    /**
     * onGetInsertMethod
     *
     * Adds the editor specific insert method.
     *
     * @param  $id
     * @return bool
     */
	function onGetInsertMethod($id)
	{
		static $done = false;
		if ($done) {
        } else {
            $doc = MolajoFactory::getDocument();
            $js = "\tfunction jInsertEditorText(text, editor) {
                document.id(editor).retrieve('Aloha').selection.insertContent(text);
            }";
            $doc->addScriptDeclaration($js);
		}

		return true;
	}

	/**
     * onDisplay
     *
	 * Display the editor area.
     *
     * @param  $name    string	The name of the editor area.
     * @param  $content string	The content of the field.
     * @param  $width   string  The width of the editor area.
     * @param  $height  string	The height of the editor area.
     * @param  $col     int		The number of columns for the editor area.
     * @param  $row     int		The number of rows for the editor area.
     * @param  $buttons boolean	True and the editor buttons will be displayed.
     * @param  $id      string	An optional ID for the textarea (note: since 1.6). If not supplied the name is used.
     *
     * @return string
     */
	function onDisplay($name, $content, $width, $height, $col, $row, $buttons = true, $id = null)
	{
		if (empty($id)) {
			$id = $name;
		}

		$this->parameters->def('baseURL', JURI::root());
		$this->parameters->set('actions', implode('', (array)$this->parameters->get('buttons', 'bold italic underline strikethrough insertunorderedlist insertorderedlist indent outdent undo redo unlink createlink urlimage')));
		$this->parameters->set('buttons', '');
		$doc = MolajoFactory::getDocument();
		$doc->addScriptDeclaration('window.addEvent(\'domready\', function() {
			$(\''.$id.'\').Aloha('.$this->parameters->toString().');
		});');
		// Only add "px" to width and height if they are not given as a percentage
		if (is_numeric($width)) {
			$width .= 'px';
		}
		if (is_numeric($height)) {
			$height .= 'px';
		}

		$buttons = $this->_displayButtons($id, $buttons);
		$editor  = "<textarea name=\"$name\" id=\"$id\" cols=\"$col\" rows=\"$row\" style=\"width: $width; height: $height;\">$content</textarea>".$buttons;

		return $editor;
	}

    /**
     * _displayButtons
     *
     * Display Buttons
     *
     * @param  $name
     * @param  $buttons
     *
     * @return string
     */
	function _displayButtons($name, $buttons)
	{
		JHtml::_('behavior.modal', 'a.modal-button');

		$args['name'] = $name;
		$args['event'] = 'onGetInsertMethod';

		$return = '';
		$results[] = $this->update($args);
		foreach ($results as $result) {
			if (is_string($result) && trim($result)) {
				$return .= $result;
			}
		}

		if(is_array($buttons) || (is_bool($buttons) && $buttons)) {
			$results = $this->_subject->getButtons($name, $buttons);

			// This will allow plugins to attach buttons or change the behavior on the fly using AJAX
			$return .= "\n<div id=\"editor-xtd-buttons\">\n";
			foreach ($results as $button)
			{
				// Results should be an object
				if ($button->get('name'))
				{
					$modal		= ($button->get('modal')) ? 'class="modal-button"' : null;
					$href		= ($button->get('link')) ? 'href="'.JURI::base().$button->get('link').'"' : null;
					$onclick	= ($button->get('onclick')) ? 'onclick="'.$button->get('onclick').'"' : null;
					$return .= "<div class=\"button2-left\"><div class=\"".$button->get('name')."\"><a ".$modal." title=\"".$button->get('text')."\" ".$href." ".$onclick." rel=\"".$button->get('options')."\">".$button->get('text')."</a></div></div>\n";
				}
			}
			$return .= "</div>\n";
		}

		return $return;
	}


    /**
     * onSave
     *
     * Save content from the editor to the form field
     *
     * @return string
     */
	function onSave()
	{
		return '$$(\'textarea\').each(function(el) {if(el.retrieve(\'Aloha\')) {el.retrieve(\'Aloha\').saveContent();}});';
	}
}