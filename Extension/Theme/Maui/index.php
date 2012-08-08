<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 * <include:profiler/>
 */
defined('MOLAJO') or die; ?>
<include:head/>
<section class="body-wrapper">
	<include:template name=Adminheader wrap=header role=banner/>
	<include:message/>
	<?php if (file_exists(Services::Registry()->get('Parameters', 'page_view_path_include'))) {
		include Services::Registry()->get('Parameters', 'page_view_path_include');
	} ?>
	<include:wrap wrap_class=push-body-wrappper/>
</section>
<include:template name=Adminfooter wrap=footer role=footer/>
<include:defer/>
<include:template name=modal/>
