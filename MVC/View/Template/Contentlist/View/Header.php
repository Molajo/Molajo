<?php
/**
 * Contentlist Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('MOLAJO') or die; ?>
<div class="portlet-header">
    <h4><?php echo Services::Registry()->get('parameters', 'criteria_title'); ?></h4>
</div>
<div class="portlet-content">
