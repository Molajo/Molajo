<?php
/**
 * @package   Molajo
 * @subpackage  Views
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<p><a href="<?php echo $this->row->link; ?>"><?php echo $this->row->linked_text; ?>
	v. <?php echo $this->row->version; ?></a><?php echo $this->row->remaining_text; ?></p>
