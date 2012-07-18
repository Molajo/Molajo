<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
use Molajo\Service\Services;

defined('MOLAJO') or die;

$action = Services::Registry()->get('Triggerdata', 'full_page_url'); ?>
<form action="<?php echo $action; ?>" method="post" name="Admingridfilters">
    <ul class="filter">
