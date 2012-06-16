<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;

$urlx = Services::Registry()->get('Trigger', 'PageURL');
$connector = Services::Registry()->get('Trigger', 'PageURLConnector');

defined('MOLAJO') or die; ?>
<ul class="nav-section">
<?php
/** Content */
if (Services::Registry()->get('Configuration', 'url_sef', 1) == 1) {
	$url = Services::Url()->getApplicationURL('admin/content');
}  else {
	$url = Services::Url()->getApplicationURL(Services::Url()->getCatalogID('admin/content'));
}?>
	<li><a href="<?php echo $url; ?>"><span class="glyph general">[</span></a></li>
<?php
/** Content */
if (Services::Registry()->get('Configuration', 'url_sef', 1) == 1) {
	$url = Services::Url()->getApplicationURL('admin/access');
}  else {
	$url = Services::Url()->getApplicationURL(Services::Url()->getCatalogID('admin/access'));
} ?>
	<li><a href="<?php echo $url; ?>"><span class="glyph general">o</span></a></li>
<?php
/** Content */
if (Services::Registry()->get('Configuration', 'url_sef', 1) == 1) {
	$url = Services::Url()->getApplicationURL('admin/build');
}  else {
	$url = Services::Url()->getApplicationURL(Services::Url()->getCatalogID('admin/build'));
} ?>
	<li><a href="<?php echo $url; ?>"><span class="glyph general">k</span></a></li>
<?php
/** Content */
if (Services::Registry()->get('Configuration', 'url_sef', 1) == 1) {
	$url = Services::Url()->getApplicationURL('admin/settings');
}  else {
	$url = Services::Url()->getApplicationURL(Services::Url()->getCatalogID('admin/settings'));
} ?>
	<li><a href="<?php echo $url; ?>"><span class="glyph general">a</span></a></li>
<?php
/** Content */
if (Services::Registry()->get('Configuration', 'url_sef', 1) == 1) {
	$url = Services::Url()->getApplicationURL('admin/install');
}  else {
	$url = Services::Url()->getApplicationURL(Services::Url()->getCatalogID('admin/install'));
} ?>
	<li class="last-in-row"><a href="<?php echo $url; ?>"><span class="glyph general">l</span></a></li>
</ul>

