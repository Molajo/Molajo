<?php
/**
 * @package     Molajo
 * @subpackage  Layout
 * @copyright   Copyright (C) 2011 Amy Stephen, Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<?php if ($this->row->grouped === true) { ?>
<datalist
	<?php if ($this->row->id == "") { } else { echo ' id="'.htmlspecialchars($this->row->id, ENT_COMPAT, 'UTF-8').'"'; } ?>
<?php }