<?php
/**
 * @package     Molajo
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;
$bread_crumbs = Services::Registry()->get('Trigger', 'AdminBreadcrumbs');
if (count($bread_crumbs) > 0) {
	foreach ($bread_crumbs as $crumb) {
		if ($crumb->component == 1) {
			$component = $crumb->url;
			break;
		}
	}
}
defined('MOLAJO') or die; ?>
<dl class="sub-nav">
	<dt><?php echo Services::Language()->translate('OPTIONS'); ?></dt>
	<dd class="active"><a href="<?php echo $component; ?>"><?php echo Services::Language()->translate('ALL'); ?></a></dd>
