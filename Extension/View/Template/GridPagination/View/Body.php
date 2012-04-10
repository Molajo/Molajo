<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$class = strtolower($this->row->pagination_class);
$class = ' class="' . $class . '"';
?>
<li <?php echo $class . $role; ?>><a
    href="<?php echo $this->row->link; ?>"<?php echo $class; ?>><span><?php echo $this->row->name; ?></span></a></li>
