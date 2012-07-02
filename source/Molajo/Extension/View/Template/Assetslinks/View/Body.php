<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see license folder
 */
defined('MOLAJO') or die;
$html5 = $this->row->html5;
$end = $this->row->end;
?>
	<link href="<?php echo $this->row->url; ?>" rel="<?php echo $this->row->relation; ?>"<?php echo $this->row->attributes; ?><?php echo $end; ?>
