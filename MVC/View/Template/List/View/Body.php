<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT, see License folder
 */
defined('NIAMBIE') or die;
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
