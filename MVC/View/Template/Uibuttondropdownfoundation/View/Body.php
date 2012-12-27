<?php
/**
 *
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined('NIAMBIE') or die;

$array = $this->row->button_group_array;

foreach ($array as $set) {
    if ($set == 'divider') {
        ?>
    <li class="divider"></li>
    <?php } else { ?>
    <li><a href="#"><?php echo $set; ?></a></li>
    <?php
    }
}
