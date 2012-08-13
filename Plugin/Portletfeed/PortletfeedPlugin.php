<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Portletfeed;

use Molajo\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Portletfeed
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class PortletfeedPlugin extends ContentPlugin
{

	/**
	 * Retrieves feed of data, according to parameters
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterReadall()
	{

		if (strtolower($this->get('template_view_path_node')) == 'portletfeed') {
		} else {
			return true;
		}

		$rss_feed = $this->parameters['criteria_rss_feed'];
		if ($rss_feed == '') {
			$rss_feed = 'http://wordpress.com/rss';
		}

		$count = $this->parameters['criteria_count'];
		if ((int) $count == 0) {
			$count = 5;
		}

		$rss = new \DOMDocument();
		$rss->load($rss_feed);

		$feed = array();
		foreach ($rss->getElementsByTagName('item') as $node) {
			$item = array (
				'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
				'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
				'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
				'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue,
			);
			array_push($feed, $item);
		}

		$query_results = array();

		for ($x=0;  $x < $count; $x++) {
			$row = new \stdClass();

			$row->title = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
			$row->link = $feed[$x]['link'];
			$row->description = $feed[$x]['desc'];
			$row->published_date = date('l F d, Y', strtotime($feed[$x]['date']));

			$query_results[] = $row;
		}

		$this->data = $query_results;

		return true;
	}
}
