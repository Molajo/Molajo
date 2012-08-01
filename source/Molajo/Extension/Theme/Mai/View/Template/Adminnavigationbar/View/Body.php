<?php
/**
 * @package    Molajo
 * @copyright  2012 Babs Gösgens. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
?>
<dt>
	<a href="<?php echo $this->row->link; ?>#<?php echo $this->row->catalog_sef_request; ?>">
		<?php echo $this->row->class; ?>
		<i data-icon="k"></i><span><?php echo $this->row->link_text; ?></span>
	</a>
	<span><span></span></span>
</dt>
<dd>
	<?php if($this->row->link_text == 'Resources'): ?>
			<include:template name=Adminsectionmenu/>
	<?php endif ?>
</dd>
