<?php
/**
 * @version		$Id: view.html.php 21656 2011-06-23 05:57:14Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * The HTML Menus Menu Items View.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @version		1.6
 */
class MenusViewItems extends JView
{
	protected $f_levels;
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$lang 		= MolajoFactory::getLanguage();
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			MolajoError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->ordering = array();

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as $item) {
			$this->ordering[$item->parent_id][] = $item->id;

			// item type text
			switch ($item->type) {
				case 'url':
					$value = MolajoText::_('MENU_TYPE_EXTERNAL_URL');
					break;

				case 'alias':
					$value = MolajoText::_('MENU_TYPE_ALIAS');
					break;

				case 'separator':
					$value = MolajoText::_('MENU_TYPE_SEPARATOR');
					break;

				case 'component':
				default:
					// load language
						$lang->load($item->componentname.'.sys', JPATH_ADMINISTRATOR, null, false, false)
					||	$lang->load($item->componentname.'.sys', JPATH_ADMINISTRATOR.'/components/'.$item->componentname, null, false, false)
					||	$lang->load($item->componentname.'.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
					||	$lang->load($item->componentname.'.sys', JPATH_ADMINISTRATOR.'/components/'.$item->componentname, $lang->getDefault(), false, false);

					if (!empty($item->componentname)) {
						$value	= MolajoText::_($item->componentname);
						$vars	= null;

						parse_str($item->link, $vars);
						if (isset($vars['view'])) {
							// Attempt to load the view xml file.
							$file = JPATH_SITE.'/components/'.$item->componentname.'/views/'.$vars['view'].'/metadata.xml';
							if (JFile::exists($file) && $xml = simplexml_load_file($file)) {
								// Look for the first view node off of the root node.
								if ($view = $xml->xpath('view[1]')) {
									if (!empty($view[0]['title'])) {
										$vars['layout'] = isset($vars['layout']) ? $vars['layout'] : 'default';

										// Attempt to load the layout xml file.
										// If Alternative Menu Item, get template folder for layout file
										if (strpos($vars['layout'], ':') > 0)
										{
											// Use template folder for layout file
											$temp = explode(':', $vars['layout']);
											$file = JPATH_SITE.'/templates/'.$temp[0].'/html/'.$item->componentname.'/'.$vars['view'].'/'.$temp[1].'.xml';
											// Load template language file
											$lang->load('template_'.$temp[0].'.sys', JPATH_SITE, null, false, false)
											||	$lang->load('template_'.$temp[0].'.sys', JPATH_SITE.'/templates/'.$temp[0], null, false, false)
											||	$lang->load('template_'.$temp[0].'.sys', JPATH_SITE, $lang->getDefault(), false, false)
											||	$lang->load('template_'.$temp[0].'.sys', JPATH_SITE.'/templates/'.$temp[0], $lang->getDefault(), false, false);

										}
										else
										{
											// Get XML file from component folder for standard layouts
											$file = JPATH_SITE.'/components/'.$item->componentname.'/views/'.$vars['view'].'/layouts/'.$vars['layout'].'.xml';
										}
										if (JFile::exists($file) && $xml = simplexml_load_file($file)) {
											// Look for the first view node off of the root node.
											if ($layout = $xml->xpath('layout[1]')) {
												if (!empty($layout[0]['title'])) {
													$value .= ' » '.MolajoText::_(trim((string) $layout[0]['title']));
												}
											}
											if (!empty($layout[0]->message[0])) {
												$item->item_type_desc = MolajoText::_(trim((string) $layout[0]->message[0]));
											}
										}
									}
								}
								unset($xml);
							}
							else {
								// Special case for absent views
								$value .= ' » '.MolajoText::_($item->componentname.'_'.$vars['view'].'_VIEW_DEFAULT_TITLE');
							}
						}
					}
					else {
						if (preg_match("/^index.php\?option=([a-zA-Z\-0-9_]*)/", $item->link, $result)) {
							$value = MolajoText::sprintf('MENU_TYPE_UNEXISTING',$result[1]);
						}
						else {
							$value = MolajoText::_('MENU_TYPE_UNKNOWN');
						}
					}
					break;
			}
			$item->item_type = $value;
		}

		// Levels filter.
		$options	= array();
		$options[]	= MolajoHTML::_('select.option', '1', MolajoText::_('J1'));
		$options[]	= MolajoHTML::_('select.option', '2', MolajoText::_('J2'));
		$options[]	= MolajoHTML::_('select.option', '3', MolajoText::_('J3'));
		$options[]	= MolajoHTML::_('select.option', '4', MolajoText::_('J4'));
		$options[]	= MolajoHTML::_('select.option', '5', MolajoText::_('J5'));
		$options[]	= MolajoHTML::_('select.option', '6', MolajoText::_('J6'));
		$options[]	= MolajoHTML::_('select.option', '7', MolajoText::_('J7'));
		$options[]	= MolajoHTML::_('select.option', '8', MolajoText::_('J8'));
		$options[]	= MolajoHTML::_('select.option', '9', MolajoText::_('J9'));
		$options[]	= MolajoHTML::_('select.option', '10', MolajoText::_('J10'));

		$this->assign('f_levels', $options);

		parent::display($tpl);
		$this->addToolbar();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.0
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/helpers/menus.php';

		$canDo	= MenusHelper::getActions($this->state->get('filter.parent_id'));

		MolajoToolbarHelper::title(MolajoText::_('MENU_VIEW_ITEMS_TITLE'), 'menumgr.png');

		if ($canDo->get('core.create')) {
			MolajoToolbarHelper::addNew('item.add');
		}
		if ($canDo->get('core.edit')) {
			MolajoToolbarHelper::editList('item.edit');
		}
		if ($canDo->get('core.edit.state')) {
			MolajoToolbarHelper::divider();
			MolajoToolbarHelper::publish('items.publish');
			MolajoToolbarHelper::unpublish('items.unpublish');
		}
		if (MolajoFactory::getUser()->authorise('core.admin')) {
			MolajoToolbarHelper::divider();
			MolajoToolbarHelper::checkin('items.checkin');
		}
		if ($canDo->get('core.edit.state')) {
			MolajoToolbarHelper::trash('items.trash');
		}
		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			MolajoToolbarHelper::deleteList('', 'items.delete', 'JTOOLBAR_EMPTY_TRASH');
		}


		if ($canDo->get('core.edit.state')) {
			MolajoToolbarHelper::makeDefault('items.setDefault', 'MENU_TOOLBAR_SET_HOME');
			MolajoToolbarHelper::divider();
		}
		if (MolajoFactory::getUser()->authorise('core.admin')) {
			MolajoToolbarHelper::custom('items.rebuild', 'refresh.png', 'refresh_f2.png', 'JToolbar_Rebuild', false);
			MolajoToolbarHelper::divider();
		}
		MolajoToolbarHelper::help('JHELP_MENUS_MENU_ITEM_MANAGER');
	}
}
