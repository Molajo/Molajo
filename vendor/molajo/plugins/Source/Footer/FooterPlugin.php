<?php
/**
 * Footer Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Footer;

use DOMDocument;
use Molajo\Plugins\AbstractFieldsPlugin;
use stdClass;

/**
 * Footer Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class FooterPlugin extends AbstractFieldsPlugin
{
    /**
     * Creates $this->query_results array filled with RSS Stream Results
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRenderView()
    {
        if ($this->processFooterPlugin() === false) {
            return $this;
        }

        return $this->setFooter();
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processFooterPlugin()
    {
        if (isset($this->parameters['template_view'])
            && strtolower($this->parameters['template_view']) === 'footer'
        ) {
            return true;
        }

        return false;
    }

    /**
     * Set Footer Data
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setFooter()
    {
        list($rss, $count) = $this->setCriteria();

        $feed = $this->getRssFeedElements($rss);

        $this->createFeedQueryResults($count, $feed);

        return $this;
    }

    /**
     * Set RSS Feed
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setCriteria()
    {
        $rss_feed = $this->setCriteriaRssFeed();
        $count    = $this->setCriteriaCount();
        $rss      = $this->loadRssFeedData($rss_feed);

        return array($count, $rss);
    }

    /**
     * Set RSS Feed Criteria Value
     *
     * @return  string
     * @since   1.0.0
     */
    protected function setCriteriaRssFeed()
    {
        if (isset($this->parameters->criteria_rss_feed)) {
        } else {
            $this->parameters->criteria_rss_feed = 'http://wordpress.com/rss';
        }

        return $this->parameters->criteria_rss_feed;
    }

    /**
     * Set RSS Feed
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setCriteriaCount()
    {
        $count = $this->parameters->criteria_count;
        if ((int)$count === 0) {
            $this->parameters->count = 5;
        }

        return $this->parameters->count;
    }

    /**
     * Set RSS Feed
     *
     * @param   string $rss_feed
     *
     * @return  DOMDocument
     * @since   1.0.0
     */
    protected function loadRssFeedData($rss_feed)
    {
        $rss = new DOMDocument();
        $rss->load($rss_feed);

        return $rss;
    }

    /**
     * Set RSS Feed
     *
     * @param   object $rss
     *
     * @return  array
     * @since   1.0.0
     */
    protected function getRssFeedElements($rss)
    {
        $feed = array();

        foreach ($rss->getElementsByTagName('item') as $node) {

            $item = array(
                'title'       => $node->getElementsByTagName('title')->item(0)->nodeValue,
                'description' => $node->getElementsByTagName('description')->item(0)->nodeValue,
                'link'        => $node->getElementsByTagName('link')->item(0)->nodeValue,
                'date'        => $node->getElementsByTagName('pubDate')->item(0)->nodeValue,
            );

            array_push($feed, $item);
        }

        return $feed;
    }

    /**
     * Create Feed Query Results
     *
     * @param   integer $count
     * @param   array   $feed
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function createFeedQueryResults($count, $feed)
    {
        $this->query_results = array();

        for ($x = 0; $x < $count; $x ++) {
            $temp_row = new stdClass();

            $temp_row->title          = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
            $temp_row->link           = $feed[$x]['link'];
            $temp_row->description    = $feed[$x]['desc'];
            $temp_row->published_date = date('l F d, Y', strtotime($feed[$x]['date']));

            $this->query_results[] = $temp_row;
        }

        return $this;
    }
}
