<?php
/**
 *
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$array = $this->row->button_group_array;

foreach ($array as $set) {
    if ($set == 'divider') { ?>
        <li class="divider"></li>
    <?php } else { ?>
        <li><a href="#"><?php echo $set; ?></a></li>
    <?php }
}
