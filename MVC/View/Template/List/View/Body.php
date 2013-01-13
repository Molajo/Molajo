<?php
/**
 * List Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

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
