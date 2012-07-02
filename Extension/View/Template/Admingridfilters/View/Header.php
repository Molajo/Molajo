<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see license folder
 */
use Molajo\Service\Services;
defined('MOLAJO') or die;

$action = Services::Registry()->get('Triggerdata', 'PageURL'); ?>
<form action="<?php echo $action; ?>" method="post" name="Admingridfilters">
<ul class="filter">
