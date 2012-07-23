<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
$action = Services::Registry()->get('Triggerdata', 'full_page_url'); ?>
<div id="m-view" style="display: none;">
	<h3><?php echo Services::Language()->translate('View Options'); ?></h3>
	<div class="row">
		<div class="eight columns">
			<dl class="tabs contained">
				<dd><a href="<?php echo $action; ?>#viewContentypes" class="active"><?echo Services::Language()->translate('Content Types'); ?></a></dd>
				<dd><a href="<?php echo $action; ?>#viewFilters"><?echo Services::Language()->translate('Filters'); ?></a></dd>
				<dd><a href="<?php echo $action; ?>#viewColumns"><?echo Services::Language()->translate('Columns'); ?></a></dd>
				<dd><a href="<?php echo $action; ?>#viewStatus"><?echo Services::Language()->translate('Status'); ?></a></dd>
				<dd><a href="<?php echo $action; ?>#viewBatch"><?echo Services::Language()->translate('Batch'); ?></a></dd>
			</dl>
			<ul class="tabs-content contained">
				<li class="active" id="viewContentypesTab">
					<?php include __DIR__ . '/' . 'Viewcontenttypes.php'; ?>
				</li>
				<li id="viewFiltersTab">
					<?php include __DIR__ . '/' . 'Viewfilters.php'; ?>
				</li>
				<li id="viewColumnsTab">
					<?php include __DIR__ . '/' . 'Viewcolumns.php'; ?>
				</li>
				<li id="viewstatusTab">
					<?php include __DIR__ . '/' . 'Viewstatus.php'; ?>
				</li>
				<li id="viewPermissionsTab">
					<?php include __DIR__ . '/' . 'Viewbatch.php'; ?>
				</li>
			</ul>
		</div>
		<div class="four columns">&nbsp;</div>
	</div>
</div>
<div id="b-view"></div>
