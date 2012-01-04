<?php
/**
 * @version        $Id: helper.php 20926 2011-03-09 06:59:31Z infograf768 $
 * @package        Joomla.Site
 * @subpackage    feed
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('MOLAJO') or die;

class modFeedHelper
{
    static function getFeed($parameters)
    {
        // module parameters
        $rssurl = $parameters->get('rssurl', '');

        //  get RSS parsed object
        $options = array();
        $options['rssUrl'] = $rssurl;
        if ($parameters->get('cache')) {
            $options['cache_time'] = $parameters->get('cache_time', 15);
            $options['cache_time'] *= 60;
        } else {
            $options['cache_time'] = null;
        }

        $rssDoc = MolajoController::getXMLParser('RSS', $options);

        $feed = new stdclass();

        if ($rssDoc != false) {
            // channel header and link
            $feed->title = $rssDoc->get_title();
            $feed->link = $rssDoc->get_link();
            $feed->description = $rssDoc->get_description();

            // channel image if exists
            $feed->image->url = $rssDoc->get_image_url();
            $feed->image->title = $rssDoc->get_image_title();

            // items
            $items = $rssDoc->get_items();

            // feed elements
            $feed->items = array_slice($items, 0, $parameters->get('rssitems', 5));
        } else {
            $feed = false;
        }

        return $feed;
    }
}