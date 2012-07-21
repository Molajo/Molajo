<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 * <include:profiler/>
 */
defined('MOLAJO') or die; ?>
<include:head/>
<include:message/>
<div class="row">
	<div class="twelve columns">
		<include:template name=Adminheader/>
	</div>
</div>
<div class="row main stickywrapper">
	<div class="twelve columns">
		<?php if (file_exists(Services::Registry()->get('Parameters', 'page_view_path_include'))) {
			include Services::Registry()->get('Parameters', 'page_view_path_include');
		} ?>
	</div>
</div>
<div class="row push">
	<div class="twelve columns">
	</div>
</div>
<div class="row footer">
	<div class="twelve columns">
		<include:template name=Adminfooter wrap=none/>
	</div>
</div>
<include:defer/>
