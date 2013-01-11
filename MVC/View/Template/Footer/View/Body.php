<?php
/**
 * Footer Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('NIAMBIE') or die;

$homeURL = Services::Registry()->get(PAGE_LITERAL, 'home_url'); ?>
<p>
    <?php echo $this->row->copyright_statement ?>
    <a href="<?php echo $this->row->link; ?>">
        <?php echo $this->row->linked_text; ?> </a>
    <?php echo ' ' . $this->row->remaining_text; ?>
</p>
