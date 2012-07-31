<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$pageURL = Services::Registry()->get('Triggerdata', 'full_page_url'); ?>

<h4><?echo Services::Language()->translate('Columns'); ?></h4>
<form action="<?php echo $pageURL; ?>" method="get" name="Admingridviewcolumns" id="Admingridviewcolumns">
	<div class="row">
		<div class="eight columns">
			<h5>Add a Column to the View Grid</h5>
			<p><?php echo Services::Language()->translate('Select the Column desired and press Add to display it on the Grid for this View.'); ?></p>
			<select name="content_types" class="inputbox">
				<option value="">No selection</option>
				<option value="1">Column 1</option>
				<option value="2">Column 2</option>
				<option value="3">Column 3</option>
				<option value="4">Column 4</option>
				<option value="5">Column 5</option>
				<option value="6">Column 6</option>
			</select>
		</div>
		<div class="four columns">
			<input type="submit" class="submit button small" name="submit" id="batch-collection-create" value="Add">
		</div>
	</div>
	<div class="row">
		<div class="eight columns">
			<h5>Remove a Column from View Column</h5>
			<p><?php echo Services::Language()->translate('To remove a Column from displaying in the Column for this View, select it and press Remove.'); ?></p>
			<select multiple show=5 name="content_types" class="inputbox">
				<option selected value="1">Column 10</option>
				<option selected value="2">Column 11</option>
				<option selected value="3">Column 12</option>
			</select>
		</div>
		<div class="four columns">
			<input type="submit" class="submit button small" name="submit" id="batch-collection-remove" value="Remove">
		</div>
	</div>
</form>
