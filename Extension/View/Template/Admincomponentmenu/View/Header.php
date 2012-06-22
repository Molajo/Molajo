<?php
/**
 * @package     Molajo
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;

if (count(Services::Registry()->get('Trigger', 'AdminBreadcrumbs')) > 0) {
	$class = '';
	foreach (Services::Registry()->get('Trigger', 'AdminBreadcrumbs') as $crumb) {
		if ($crumb->component == 1) {
			$componentTitle = $crumb->title;
			$component = $crumb->url;
			if ($component == Services::Registry()->get('Trigger', 'PageURL')) {
				$class = ' class="active" ';
			}
			break;
		}
	}
}
defined('MOLAJO') or die; ?>

<dl class="sub-nav">
	<dt><?php echo Services::Language()->translate('OPTIONS'); ?></dt>
	<dd<?php echo $class; ?>><a href="<?php echo $component; ?>"><?php echo $componentTitle; ?></a></dd>
