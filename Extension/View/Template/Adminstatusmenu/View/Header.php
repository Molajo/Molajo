<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$class = '';
$class = ' class="active" ';
$status = '';
$statusTitle = '';
if (count(Services::Registry()->get('Triggerdata', 'Adminbreadcrumbs')) > 0) {
    foreach (Services::Registry()->get('Triggerdata', 'Adminbreadcrumbs') as $crumb) {
        if ($crumb->lvl == 3) {
            $statusTitle = $crumb->title;
            $status = $crumb->url;
            break;
        }
    }
}
?>
<dl class="sub-nav">
    <dt><?php echo Services::Language()->translate('Status'); ?></dt>
    <dd<?php echo $class; ?>><a href="<?php echo $status; ?>"><?php echo $statusTitle; ?></a></dd>
