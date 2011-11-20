<?php
/**
 * @version		$Id: categories.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('MOLAJO') or die;


require_once JPATH_SITE.'/components/com_articles/helpers/route.php';

/**
 * Categories Search plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	Search.categories
 * @since		1.6
 */
class plgSearchCategories extends MolajoPlugin
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
	 * @return array An array of search areas
	 */
	function onContentSearchAreas()
	{
		static $areas = array(
		'categories' => 'PLG_SEARCH_CATEGORIES_CATEGORIES'
		);
		return $areas;
	}

	/**
	 * Categories Search method
	 *
	 * The sql must return the following fields that are
	 * used in a common display routine: href, title, section, created, text,
	 * browsernav
	 * @param string Target search string
	 * @param string matching option, exact|any|all
	 * @param string ordering option, newest|oldest|popular|alpha|category
	 * @param mixed An array if restricted to areas, null if search all
	 */
	function onContentSearch($text, $phrase='', $ordering='', $areas=null)
	{
		$db		= MolajoFactory::getDbo();
		$user	= MolajoFactory::getUser();
		$app	= MolajoFactory::getApplication();

		$searchText = $text;

		if (is_array($areas)) {
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas()))) {
				return array();
			}
		}

		$sContent		= $this->parameters->get('search_content',		1);
		$sArchived		= $this->parameters->get('search_archived',		1);
		$limit			= $this->parameters->def('search_limit',		50);
		$state			= array();
		if ($sContent) {
			$state[]=1;
		}
		if ($sArchived) {
			$state[]=2;
		}


		$text = trim($text);
		if ($text == '') {
			return array();
		}

		switch ($ordering) {
			case 'alpha':
				$order = 'a.title ASC';
				break;

			case 'category':
			case 'popular':
			case 'newest':
			case 'oldest':
			default:
				$order = 'a.title DESC';
		}

		$text	= $db->Quote('%'.$db->getEscaped($text, true).'%', false);
		$query	= $db->getQuery(true);

		$return = array();
		if (!empty($state)) {
			$query->select('a.title, a.description AS text, "" AS created, "2" AS browsernav, a.id AS catid, '
						.'CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
			$query->from('#__categories AS a');
			$query->where('(a.title LIKE '. $text .' OR a.description LIKE '. $text .') AND a.published IN ('.implode(',',$state).') AND a.extension = \'com_articles\')');
            $acl = new MolajoACL ();
            $acl->getQueryInformation ('', $query, 'viewaccess', array('table_prefix'=>'a'));
			$query->group('a.id');
			$query->order($order);

			// Filter by language
            $lang = MolajoFactory::getLanguage()->getTag();
            $query->where('a.language IN ('.$db->Quote($lang).','.$db->Quote('*').')');

			$db->setQuery($query, 0, $limit);
			$rows = $db->loadObjectList();

			if ($rows) {
				$count = count($rows);
				for ($i = 0; $i < $count; $i++) {
					$rows[$i]->href = ContentHelperRoute::getCategoryRoute($rows[$i]->slug);
					$rows[$i]->section	= MolajoText::_('JCATEGORY');
				}

				$return = array();
				foreach($rows AS $key => $category) {
					if (searchHelper::checkNoHTML($category, $searchText, array('name', 'title', 'text'))) {
						$return[] = $category;
					}
				}
			}
		}
		return $return;
	}
}
