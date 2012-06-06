<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
$html5 = $this->row->html5;
$end = $this->row->end;
?>
	<link rel="stylesheet" href="<?php echo $this->row->url; ?>"<?php if ((int) $this->row->html5 == 0): ?> type="<?php echo $this->row->mimetype; ?>"<?php endif; ?><?php if ($this->row->media != null): ?> type="<?php echo $this->row->media; ?>"<?php endif; ?><?php if (trim($this->row->attributes) != ''): ?><?php echo $this->row->attributes; ?><?php endif; ?><?php echo $end; ?>
