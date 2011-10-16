<?php
/**
 * @package     Molajo
 * @subpackage  Module
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$layout = $params->def('layout', 'admin_header');
$wrap = $params->def('wrap', 'header');

$tmpobj = new JObject();
$tmpobj->set('site_title', MolajoFactory::getApplication()->getCfg('site_title', 'Molajo'));
$rowset[]=$tmpobj;