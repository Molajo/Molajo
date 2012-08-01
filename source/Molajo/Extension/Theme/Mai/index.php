<?php
use Molajo\Service\Services;
use Molajo\Extension\Theme\Molajito\Helper;
/**
 * @package    Molajo
 * @copyright  2012 Babs Gösgens. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 * <include:profiler/>
 */
defined('MOLAJO') or die;

$_baseUri = Services::Registry()->get('Triggerdata', 'full_page_url');
?>
<include:head/>
<!-- this id needs to move to the body maybe -->
<div id="focus">
<include:template name=Adminheader/>

<?php if (file_exists(Services::Registry()->get('Parameters', 'page_view_path_include'))) {
	include Services::Registry()->get('Parameters', 'page_view_path_include');
} ?>
</div>
<footer>
</footer>
<include:defer/>

<!-- 			<include:template name=Adminnavigationbar/>
 -->