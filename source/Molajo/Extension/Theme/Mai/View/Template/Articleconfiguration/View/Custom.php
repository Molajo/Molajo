<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>

<div id="m-options" style="">
	<h3><?php echo Services::Language()->translate('Configuration Options'); ?></h3>
	<ol>
		<li><a href="#"
			<?php include __DIR__ . '/' . 'reveal-parameters.php'; ?>
			<?php echo Services::Language()->translate('Change Page Title'); ?></a></li>

		<li><a href="#"
			<?php include __DIR__ . '/' . 'reveal-parameters.php'; ?>
			<?php echo Services::Language()->translate('Change Status Menu Items'); ?></a></li>

		<li><a href="#"
			<?php include __DIR__ . '/' . 'reveal-parameters.php'; ?>
			<?php echo Services::Language()->translate('Change List and/or Search Filters'); ?></a></li>

		<li><a href="#"
			<?php include __DIR__ . '/' . 'reveal-parameters.php'; ?>
			<?php echo Services::Language()->translate('Change List Columns'); ?></a></li>

		<li><a href="#"
			<?php include __DIR__ . '/' . 'reveal-parameters.php'; ?>
			<?php echo Services::Language()->translate('Change List Length and Ordering'); ?></a></li>
	</ol>
</div>
<div id="b-options"></div>
