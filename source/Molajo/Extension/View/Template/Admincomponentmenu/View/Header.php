<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
$class = '';
$resource = '';
$resourceTitle = '';
if (count(Services::Registry()->get('Triggerdata', 'AdminBreadcrumbs')) > 0) {
	foreach (Services::Registry()->get('Triggerdata', 'AdminBreadcrumbs') as $crumb) {
		if ($crumb->resource == 1) {
			$resourceTitle = $crumb->title;
			$resource = $crumb->url;
			if ($resource == Services::Registry()->get('Parameters', 'full_page_url')) {
				$class = ' class="active" ';
			}
			break;
		}
	}
}
defined('MOLAJO') or die; ?>
<dl class="sub-nav">
	<dt><?php echo Services::Language()->translate('STATUS'); ?></dt>
	<dd<?php echo $class; ?>><a href="<?php echo $resource; ?>"><?php echo $resourceTitle; ?></a></dd>
