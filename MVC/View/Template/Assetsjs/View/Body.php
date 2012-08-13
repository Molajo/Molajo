<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */

defined('MOLAJO') or die;
$html5 = $this->row->html5;
$end = $this->row->end;
?>
<script src="<?php echo $this->row->url; ?>"<?php if ((int)$html5 == 0): ?>
		type="<?php echo $this->row->mimetype; ?>"<?php endif; ?><?php if ((int)$this->row->defer == 1): ?>
		defer="defer"<?php endif; ?><?php if ((int)$this->row->async == 1): ?>
		async="async"<?php endif; ?>></script><?php echo chr(10);
