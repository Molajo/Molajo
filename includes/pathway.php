<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Class to manage the site application pathway.
 *
 * @package		Molajo
 * @subpackage	Pathway
 * @since		1.0
 */
class MolajoPathwaySite extends MolajoPathway
{
	/**
	 * Class constructor.
	 *
	 * @param	array
	 *
	 * @return	MolajoPathwaySite
	 * @since	1.5
	 */
	public function __construct($options = array())
	{
		//Initialise the array.
		$this->_pathway = array();

		$app	= MolajoFactory::getApplication();
		$menu	= $app->getMenu();

		if ($item = $menu->getActive()) {
			$menus = $menu->getMenu();
			$home = $menu->getDefault();

			if (is_object($home) && ($item->id != $home->id)) {
				foreach($item->tree as $menupath)
				{
					$url = '';
					$link = $menu->getItem($menupath);

					switch($link->type)
					{
						case 'separator':
							$url = null;
							break;

						case 'url':
							if ((strpos($link->link, 'index.php?') === 0) && (strpos($link->link, 'Itemid=') === false)) {
								// If this is an internal Joomla link, ensure the Itemid is set.
								$url = $link->link.'&Itemid='.$link->id;
							}
							else {
								$url = $link->link;
							}
							break;

						case 'alias':
							// If this is an alias use the item id stored in the parameters to make the link.
							$url = 'index.php?Itemid='.$link->params->get('aliasoptions');
							break;

						default:
							$router = JSite::getRouter();
							if ($router->getMode() == JROUTER_MODE_SEF) {
								$url = 'index.php?Itemid='.$link->id;
							}
							else {
								$url .= $link->link.'&Itemid='.$link->id;
							}
							break;
					}

					$this->addItem($menus[$menupath]->title, $url);
				}
			}
		}
	}
}
