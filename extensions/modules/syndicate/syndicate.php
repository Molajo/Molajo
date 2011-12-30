<?php
/**
 * @version        $Id: syndicate.php 20806 2011-02-21 19:44:59Z dextercowley $
 * @package        Joomla.Site
 * @subpackage    syndicate
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('MOLAJO') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

$parameters->def('text', 'Feed Entries');
$parameters->def('format', 'rss');

$link = modSyndicateHelper::getLink($parameters);

if (is_null($link)) {
    return;
}

$view_class_suffix = htmlspecialchars($parameters->get('view_class_suffix'));

$text = htmlspecialchars($parameters->get('text'));

require MolajoModule::getViewPath('syndicate', $parameters->get('view', 'default'));
