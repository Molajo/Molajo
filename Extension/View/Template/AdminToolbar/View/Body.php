<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
$class = ' toolbar ' . strtolower($this->row->name);
$class = ' class="' . $class . '"';
?>
<li class="toolbar"><a
    href="<?php echo $this->row->link; ?>"<?php echo $class; ?>><span><?php echo $this->row->name; ?></span></a></li>

