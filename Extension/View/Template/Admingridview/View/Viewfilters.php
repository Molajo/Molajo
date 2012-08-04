<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$pageURL = Services::Registry()->get('Plugindata', 'full_page_url'); ?>
<h4><?echo Services::Language()->translate('Filters'); ?></h4>
<form action="<?php echo $pageURL; ?>" method="get" name="Admingridviewcollections" id="Admingridviewcollections">
    <div class="row">
        <div class="eight columns">
            <h5>Add a Filter for View Display</h5>
            <p><?php echo Services::Language()->translate('Select the Filter desired and press Add to use it on this View.'); ?></p>
            <select name="content_types" class="inputbox">
                <option value="">No selection</option>
                <option value="1">Filter 1</option>
                <option value="2">Filter 2</option>
                <option value="3">Filter 3</option>
                <option value="4">Filter 4</option>
                <option value="5">Filter 5</option>
                <option value="6">Filter 6</option>
            </select>
        </div>
        <div class="four columns">
            <input type="submit" class="submit button small" name="submit" id="batch-collection-create" value="Add">
        </div>
    <div class="row">
        <div class="eight columns">
            <h5>Remove a Filter from View Display</h5>
            <p><?php echo Services::Language()->translate('To remove the Filter from display, select it and press Remove.'); ?></p>
            <select multiple show=5 name="content_types" class="inputbox">
                <option selected value="1">Filter 10</option>
                <option selected value="2">Filter 11</option>
                <option selected value="3">Filter 12</option>
            </select>
        </div>
        <div class="four columns">
            <input type="submit" class="submit button small" name="submit" id="batch-collection-remove" value="Remove">
        </div>
    </div>
</form>
