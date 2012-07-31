<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
$bread_crumbs = Services::Registry()->get('Triggerdata', 'Adminbreadcrumbs'); ?>
<h3><?php echo $bread_crumbs[1]->title; ?></h3>
<dl class="nice vertical tabs">
