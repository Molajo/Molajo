<?php
/**
 * Feed Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Feed;

use CommonApi\Event\ReadEventInterface;
use DOMDocument;
use Molajo\Plugins\ReadEvent;
use stdClass;

/**
 * Feed Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class FeedPlugin extends ReadEvent implements ReadEventInterface
{
    /**
     * Prepare Data for Injecting into Template
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onGetTemplateData()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        return $this;

        return $this->setFeed();
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
    {
        if (strtolower($this->controller['parameters']->token->name) === 'feed') {
        } else {
            return false;
        }

        if (isset($this->controller['parameters']->criteria_rss_feed)) {
        } else {
            return false;
        }

        if (trim($this->controller['parameters']->criteria_rss_feed) === '') {
            return false;
        }

        return true;
    }

    /**
     * Set Feed Data
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setFeed()
    {
        $entries_count = $this->setCriteriaCount();
        $rss           = $this->loadRssFeedData();

        $feed = $this->getRssFeedElements($rss, $entries_count);

        $this->createFeedQueryResults($entries_count, $feed);

        return $this;
    }


    /**
     * Set RSS Feed
     *
     * @return  integer
     * @since   1.0.0
     */
    protected function setCriteriaCount()
    {
        $count = 0;

        if (isset($this->controller['parameters']->criteria_count)) {
            $count = $this->controller['parameters']->criteria_count;
        }

        if ((int)$count === 0) {
            $this->controller['parameters']->count = 5;
        }

        $this->controller['parameters']->count = $count;

        return $this->controller['parameters']->count;
    }

    /**
     * Set RSS Feed
     *
     * @return  DOMDocument
     * @since   1.0.0
     */
    protected function loadRssFeedData()
    {
        $rss = new DOMDocument();

        $rss->load($this->controller['parameters']->criteria_rss_feed);

        return $rss;
    }

    /**
     * Set RSS Feed
     *
     * @param   object  $rss
     * @param   integer $entries_count
     *
     * @return  array
     * @since   1.0.0
     */
    protected function getRssFeedElements($rss, $entries_count)
    {
        $feed = array();

        $count = 1;

        foreach ($rss->getElementsByTagName('item') as $node) {

            $item = array(
                'title'       => $node->getElementsByTagName('title')->item(0)->nodeValue,
                'description' => $node->getElementsByTagName('description')->item(0)->nodeValue,
                'link'        => $node->getElementsByTagName('link')->item(0)->nodeValue,
                'date'        => $node->getElementsByTagName('pubDate')->item(0)->nodeValue,
            );

            $feed[] = $item;

            if ($count++ > $entries_count) {
                break;
            }
        }

        return $feed;
    }

    /**
     * Create Feed Query Results
     *
     * @param   array $feed
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function createFeedQueryResults($feed)
    {
        $this->controller['query_results'] = array();

        $count = count($feed);

        for ($x = 0; $x < $count; $x++) {
            $temp_row = new stdClass();

            $temp_row->title          = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
            $temp_row->link           = $feed[$x]['link'];
            $temp_row->description    = $feed[$x]['desc'];
            $temp_row->published_date = date('l F d, Y', strtotime($feed[$x]['date']));

            $this->controller['query_results'][] = $temp_row;
        }

        return $this;
    }
}
