<?php
/**
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined('MOLAJO') or die;
$array = $this->row->button_group_array;
for ($i = 0; $i < count($array); $i++) {
    ?>
<li>
    <include:ui name=button <?php echo $array[$i]; ?>/>
</li>
<?php }
