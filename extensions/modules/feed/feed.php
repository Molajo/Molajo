<?php
/**
 * @version        $Id: feed.php 20806 2011-02-21 19:44:59Z dextercowley $
 * @package        Joomla.Site
 * @subpackage    feed
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('MOLAJO') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

$rssurl = $parameters->get('rssurl', '');
$rssrtl = $parameters->get('rssrtl', 0);

//check if feed URL has been set
if (empty ($rssurl)) {
    echo '<div>';
    echo MolajoTextHelper::_('FEED_ERR_NO_URL');
    echo '</div>';
    return;
}

$feed = modFeedHelper::getFeed($parameters);
$layout_class_suffix = htmlspecialchars($parameters->get('layout_class_suffix'));

require MolajoModule::getLayoutPath('feed', $parameters->get('layout', 'default'));
