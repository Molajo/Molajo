<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
if (isset($this->row->title)) {
	$title = $this->row->title;
} else {
	$title = $this->row->full_name;
}
?>
<li>
<?php if (isset($this->row->catalog_id_url)) { ?>
	<a href="<?php echo $this->row->catalog_id_url; ?>">
<?php } ?>
	<?php echo $title ?>
<?php if (isset($this->row->catalog_id_url)) { ?>
	</a>
<?php } ?>
</li>
