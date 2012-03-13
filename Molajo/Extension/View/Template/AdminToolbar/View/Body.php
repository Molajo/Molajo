<?php
/**
 * @package	 	Molajo
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license	 	GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$class = 'button ' . strtolower($this->row->name);
$class = ' class="' . $class . '"';
?>
<li role="button"><a href="<?php echo $this->row->link; ?>"<?php echo $class; ?>><span><?php echo $this->row->name; ?></span></a></li>
