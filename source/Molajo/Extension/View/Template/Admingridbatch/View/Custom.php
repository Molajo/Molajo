<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
$action = Services::Registry()->get('Triggerdata', 'full_page_url'); ?>
<div id="m-batch" style="display: none;">
	<h3><?php echo Services::Language()->translate('Batch Changes'); ?></h3>
	<div class="row">
		<div class="nine columns">
			<dl class="tabs contained">
				<dd><a href="<?php echo $action; ?>#batchContenttypes"><?echo Services::Language()->translate('Content Types'); ?></a></dd>
				<dd><a href="<?php echo $action; ?>#batchStatus" class="active"><?echo Services::Language()->translate('Status'); ?></a></dd>
				<dd><a href="<?php echo $action; ?>#batchCategories"><?echo Services::Language()->translate('Categories'); ?></a></dd>
				<dd><a href="<?php echo $action; ?>#batchTags"><?echo Services::Language()->translate('Tags'); ?></a></dd>
				<dd><a href="<?php echo $action; ?>#batchCollections"><?echo Services::Language()->translate('Collections'); ?></a></dd>
				<dd><a href="<?php echo $action; ?>#batchPermissions"><?echo Services::Language()->translate('Permissions'); ?></a></dd>
			</dl>
			<ul class="tabs-content contained">
				<li id="batchContenttypeTab">
					<?php include __DIR__ . '/' . 'Batchcontenttypes.php'; ?>
				</li>
				<li class="active" id="batchStatusTab">
					<?php include __DIR__ . '/' . 'Batchstatus.php'; ?>
				</li>
				<li id="batchCategoriesTab">
					<?php include __DIR__ . '/' . 'Batchcategories.php'; ?>
				</li>
				<li id="batchTagsTab">
					<?php include __DIR__ . '/' . 'Batchtags.php'; ?>
				</li>
				<li id="batchCollectionsTab">
					<?php include __DIR__ . '/' . 'Batchcollections.php'; ?>
				</li>
				<li id="batchPermissionsTab">
					<?php include __DIR__ . '/' . 'Batchpermissions.php'; ?>
				</li>
			</ul>
		</div>
		<div class="three columns">&nbsp;</div>
	</div>
</div>
<div id="b-batch"></div>

