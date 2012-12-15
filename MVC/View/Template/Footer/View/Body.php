<?php
use Molajo\Service\Services;

/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('NIAMBIE') or die;

$homeURL = Services::Registry()->get(PAGE_LITERAL, 'home_url'); ?>
<p>
    <?php echo $this->row->copyright_statement ?>
    <a href="<?php echo $this->row->link; ?>">
        <?php echo $this->row->linked_text; ?> </a>
    <?php echo ' ' . $this->row->remaining_text; ?>
</p>
