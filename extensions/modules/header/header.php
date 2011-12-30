<?php
/**
 * @package     Molajo
 * @subpackage  Module
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$view = $parameters->def('view', 'header');
$wrap = $parameters->def('wrap', 'header');

require_once dirname(__FILE__) . '/helper.php';
$rowset = MolajoHeaderHelper::getList($parameters);