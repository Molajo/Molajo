<?php
/**
 * @package     Molajo
 * @subpackage  Layouts
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 *
 * body.php runs one time for each row processed, after header.php has processed and before footer.php
 * put html in here that you want to display as the row information
 *
 */
defined('MOLAJO') or die;

echo $this->row->text;

