<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 * todo: think about error/offline templates get rid of page
 */
defined('MOLAJO') or die; ?>
<include:head/>
<include:message/>
<div class="row">
	<div class="twelve columns">
		<include:template name=Adminheader/>
	</div>
</div>
<div class="stickywrapper">
	<div class="row">
		<div class="twelve columns">

<?php if (file_exists(Services::Registry()->get('Parameters', 'page_view_path_include'))) {
	include Services::Registry()->get('Parameters', 'page_view_path_include');
} ?>
		</div>
	</div>
	<div class="row">
		<div class="twelve columns">
			<div class="push"></div>
		</div>
	</div>
</div>
<div class="row footer">
	<div class="twelve columns">
		<include:template name=Adminfooter wrap=none/>
	</div>
</div>
<include:profiler/>
<include:defer/>
