<?php
/**
 * @package     Molajo
 * @subpackage  Module
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$layout = $parameters->def('layout', 'admin_header');
$wrap = $parameters->def('wrap', 'header');

require_once dirname(__FILE__) . '/helper.php';
$rowset = MolajoHeaderHelper::getList($parameters);