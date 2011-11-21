<?php
/**
 * @version		$Id: image.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('MOLAJO') or die;


/**
 * Editor Image buton
 *
 * @package		Joomla.Plugin
 * @subpackage	Editors-xtd.image
 * @since 1.5
 */
class plgButtonImage extends MolajoPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Display the button
	 *
	 * @return array A two element array of (imageName, textToInsert)
	 */
	function onDisplay($name, $asset, $author)
	{
		$app = MolajoFactory::getApplication();
		$parameters = JComponentHelper::getParameters('com_media');
 		$user = MolajoFactory::getUser();
		if (	$user->authorise('edit', $asset)
			||	$user->authorise('create', $asset)
			||  count($user->getAuthorisedCategories($asset, 'create')) > 0
			|| ($user->authorise('edit.own', $asset) && $author == $user->id))
		{
			$link = 'index.php?option=com_media&amp;view=images&amp;layout=component&amp;e_name='.$name.'&amp;asset='.$asset.'&amp;author='.$author;
			JHtml::_('behavior.modal');
			$button = new JObject;
			$button->set('modal', true);
			$button->set('link', $link);
			$button->set('text', MolajoText::_('PLG_IMAGE_BUTTON_IMAGE'));
			$button->set('name', 'image');
			$button->set('options', "{handler: 'iframe', size: {x: 800, y: 500}}");
			return $button;
		}
				else
		{
			return false;
		}
	}
}
