<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;
defined('MOLAJO') or die;

$action = Services::Registry()->get('Trigger', 'PageURL');
?>
<form action="<?php echo $action; ?>" method="post" name="Admingridfilters">
	<section class="row">
		<section class="three columns">
			<dl class="nice vertical tabs">
				<dd><a href="<?php echo $pageUrl; ?>#batchStatus" class="active">Status</a></dd>
				<dd><a href="<?php echo $pageUrl; ?>#batchCategories">Categories</a></dd>
				<dd><a href="<?php echo $pageUrl; ?>#batchTags">Tags</a></dd>
				<dd><a href="<?php echo $pageUrl; ?>#batchPermissions">Permissions</a></dd>
			</dl>
		</section>
		<section class="nine columns">
			<ul class="nice tabs-content vertical">
				<li class="active" id="batchStatusTab">
					<div class="panel">
						<h4>Status</h4>

						<p>Change the items selected, above to the specified status.</p>
					</div>
				</li>
				<li id="batchCategoriesTab">
					<div class="panel">
						<h4>Categories</h4>

						<p>Seriously, just look at this sweet panel.</p>
					</div>
				</li>
				<li id="batchTagsTab">
					<div class="panel">
						<h4>Tags</h4>

						<p>Seriously, just look at this sweet panel.</p>
					</div>
				</li>
				<li id="batchPermissionsTab">
					<div class="panel">
						<h4>P</h4>
					</div>
				</li>
			</ul>
		</section>
	</section>
</form>
