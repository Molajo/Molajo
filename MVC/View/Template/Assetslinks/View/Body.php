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
	<link href="<?php echo $this->row->url; ?>" rel="<?php echo $this->row->relation; ?>"<?php echo $this->row->attributes; ?><?php echo $end; ?>
