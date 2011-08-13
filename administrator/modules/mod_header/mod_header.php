<?php
/**
 * @package     Molajo
 * @subpackage  Module
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$layout = $params->def('layout', 'adminheader');
$wrap = $params->def('wrap', 'none');

$linked_text = $params->def('linked_text', '');
if ($linked_text == '') {
    $linked_text = MolajoFactory::getApplication()->getCfg('sitename');
}
$this->rowset[0]->site_title = $linked_text;

$link = $params->def('link', '');
$target = $params->def('target', '');
if ($target == '') {
} else {
    $target = ' target ="'.$target.'"';
}

if ($link == '') {
    $this->rowset[0]->link = '';
} else {
    $this->rowset[0]->link = '<a href="'.$link.$target.'">';
    $this->rowset[0]->endlink = '<a href="'.$link.$target.'">';
}
