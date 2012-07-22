<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$action = Services::Registry()->get('Triggerdata', 'full_page_url'); ?>
<div id="m-filters" style="display: none;">
	<h3><?php echo Services::Language()->translate('Filter Content'); ?></h3>
	<form action="<?php echo $action; ?>" method="post" name="Admingridfilters">
	<ul class="filter">
