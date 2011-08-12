<?php
/**
 * @package     Molajo
 * @subpackage  Layouts
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 * 
 * header.php runs one time for each row processed
 * put html in here that you want to display as a heading to the row information
 * 
 */
defined('MOLAJO') or die; ?>
<h3>
<a href="<?php echo $this->row->url; ?>"><?php echo $this->row->title; ?></a>
</h3>