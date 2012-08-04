<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
$pageURL = Services::Registry()->get('Plugindata', 'full_page_url'); ?>
<div id="m-view" style="display: none;">
    <h3><?php echo Services::Language()->translate('View Options'); ?></h3>
    <div class="row">
        <div class="eight columns">

            <dl class="tabs">
                <dd class="active"><a href="#simple1">Simple Tab 1</a></dd>
                <dd><a href="#simple2">Simple Tab 2</a></dd>
                <dd><a href="#simple3">Simple Tab 3</a></dd>
            </dl>

            <ul class="tabs-content">
                <li class="active" id="simple1Tab">This is simple tab 1's content. Pretty neat, huh?</li>
                <li id="simple2Tab">This is simple tab 2's content. Now you see it!</li>
                <li id="simple3Tab">This is simple tab 3's content. It's, you know...okay.</li>
            </ul>

            <dl class="tabs">
                <dd class="active"><a href="<?php echo $pageURL; ?>#viewfilters"><?echo Services::Language()->translate('Filters'); ?></a></dd>
                <dd><a href="<?php echo $pageURL; ?>#viewcolumns"><?echo Services::Language()->translate('Columns'); ?></a></dd>
                <dd><a href="<?php echo $pageURL; ?>#viewstatus"><?echo Services::Language()->translate('Status'); ?></a></dd>
                <dd><a href="<?php echo $pageURL; ?>#viewbatch"><?echo Services::Language()->translate('Batch'); ?></a></dd>
            </dl>
            <ul class="tabs-content">
                <li class="active" id="viewfiltersTab">
                    <?php include __DIR__ . '/' . 'Viewfilters.php'; ?>
                </li>
                <li id="viewcolumnsTab">
                    <?php include __DIR__ . '/' . 'Viewcolumns.php'; ?>
                </li>
                <li id="viewstatusTab">
                    <?php include __DIR__ . '/' . 'Viewstatus.php'; ?>
                </li>
                <li id="viewbatchTab">
                    <?php include __DIR__ . '/' . 'Viewbatch.php'; ?>
                </li>
            </ul>
        </div>
        <div class="four columns">&nbsp;</div>
    </div>
</div>
<div id="b-view"></div>
