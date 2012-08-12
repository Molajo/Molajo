<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
$pageURL = Services::Registry()->get('Plugindata', 'full_page_url'); ?>
<div class="row">
    <div class="twelve columns">
        <div id="m-batch" style="display: none;">
            <h3><?php echo Services::Language()->translate('Batch Updates'); ?></h3>
            <div class="row">
                <div class="nine columns">
                    <dl class="tabs">
                        <dd class="active"><a href="<?php echo $pageURL; ?>#batchstatus"><?echo Services::Language()->translate('Status'); ?></a></dd>
                        <dd><a href="<?php echo $pageURL; ?>#batchcategories"><?echo Services::Language()->translate('Categories'); ?></a></dd>
                        <dd><a href="<?php echo $pageURL; ?>#batchtags"><?echo Services::Language()->translate('Tags'); ?></a></dd>
                        <dd><a href="<?php echo $pageURL; ?>#batchcollections"><?echo Services::Language()->translate('Collections'); ?></a></dd>
                        <dd><a href="<?php echo $pageURL; ?>#batchpermissions"><?echo Services::Language()->translate('Permissions'); ?></a></dd>
                    </dl>
                    <ul class="tabs-content">
                        <li class="active" id="batchstatusTab">
                            <?php include __DIR__ . '/' . 'Batchstatus.php'; ?>
                        </li>
                        <li id="batchcategoriesTab">
                            <?php include __DIR__ . '/' . 'Batchcategories.php'; ?>
                        </li>
                        <li id="batchtagsTab">
                            <?php include __DIR__ . '/' . 'Batchtags.php'; ?>
                        </li>
                        <li id="batchcollectionsTab">
                            <?php include __DIR__ . '/' . 'Batchcollections.php'; ?>
                        </li>
                        <li id="batchpermissionsTab">
                            <?php include __DIR__ . '/' . 'Batchpermissions.php'; ?>
                        </li>
                    </ul>
                </div>
                <div class="three columns">&nbsp;</div>
            </div>
        </div>
        <div id="b-batch"></div>
    </div>
</div>
