<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<div class="row">
	<div class="twelve columns">
		<h4>
			<?php echo $this->row->title; ?>
		</h4>
	</div>
</div>
<div class="row">
	<div class="nine columns">
		<h3>
			<?php echo $this->row->visitor_name; ?>
		</h3>
	</div>
	<div class="three columns">
		<h3>
			<?php echo $this->row->start_publishing_datetime_ccyymmdd; ?>
		</h3>
	</div>
</div>
<div class="row">
	<div class="twelve columns">
		<h3>
			<?php echo $this->row->content_text; ?>
		</h3>
	</div>
</div>
