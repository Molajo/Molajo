<?php
/**
 * @package     Molajo
 * @subpackage  Layouts
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 *
 * top.php runs one time, before the rows are processed
 * put html in here that you want to display BEFORE the row results
 *
 */
defined('MOLAJO') or die; ?>
<ul class="<?php echo $this->params->get('page_class_suffix', ''); ?>">
