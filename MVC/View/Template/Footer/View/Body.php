<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$homeURL = Services::Registry()->get('Configuration', 'application_base_url'); ?>
<p>
    <?php echo $this->row->copyright_statement ?>
    <a href="<?php echo $this->row->link; ?>">
        <?php echo $this->row->linked_text; ?> </a>
    <?php echo ' ' . $this->row->remaining_text; ?>
</p>
