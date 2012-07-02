<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see license folder
 */
$class = '';
$component = '';
$componentTitle = '';
if (count(Services::Registry()->get('Triggerdata', 'AdminBreadcrumbs')) > 0) {
	foreach (Services::Registry()->get('Triggerdata', 'AdminBreadcrumbs') as $crumb) {
		if ($crumb->component == 1) {
			$componentTitle = $crumb->title;
			$component = $crumb->url;
			if ($component == Services::Registry()->get('Triggerdata', 'PageURL')) {
				$class = ' class="active" ';
			}
			break;
		}
	}
}
defined('MOLAJO') or die; ?>
<dl class="sub-nav">
	<dt><?php echo Services::Language()->translate('STATUS'); ?></dt>
	<dd<?php echo $class; ?>><a href="<?php echo $component; ?>"><?php echo $componentTitle; ?></a></dd>
