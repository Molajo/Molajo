<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
$bread_crumbs = Services::Registry()->get('Plugindata', 'Adminbreadcrumbs'); ?>
<h3><?php echo $bread_crumbs[1]->title; ?></h3>
<dl class="nice vertical tabs">
