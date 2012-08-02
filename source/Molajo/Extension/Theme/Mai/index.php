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
<?php // echo Services::Registry()->get('Parameters', 'page_view_path_include') ?>
<?php // echo Services::Registry()->get('Parameters', 'template_view_path_include') ?>

<footer style="margin-top: 200px; z-index: 400; background: #efefef;">
<h1>Adminnavigationbar</h1>
<?php //var_dump(Services::Registry()->get('Triggerdata','Adminnavigationbar'))?>

<h1>Adminsectionmenu</h1>
<?php //var_dump(Services::Registry()->get('Triggerdata','Adminsectionmenu'))?>

<h1>Adminstatusmenu</h1>
<?php //var_dump(Services::Registry()->get('Triggerdata','Adminstatusmenu'))?>

<h1>Adminbreadcrumbs</h1>
<?php var_dump(Services::Registry()->get('Triggerdata','Adminbreadcrumbs'));?>

<h1>Parameters</h1>
<?php //var_dump(Services::Registry()->get('Parameters', '*')) ?>
</footer>

</div>
<include:defer/>