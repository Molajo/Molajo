<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$pageURL = Services::Registry()->get('Triggerdata', 'full_page_url'); ?>
<div class="row">
	<div class="twelve columns">
	<h4><?echo Services::Language()->translate('Content Types'); ?></h4>
	<form action="<?php echo $pageURL; ?>" method="post" name="Admingridviewcontenttypes" id="Admingridviewcontenttypes">
		<div class="row">
			<div class="eight columns">
			<p>
				<?php echo Services::Language()->translate('Create a Content Type to be used to store different data for items associated so associated.'); ?></p>
			</p>
				<input type="text" name="content_type" id="content_type">
			</div>
			<div class="four columns">
				<input type="submit" class="submit button small" name="submit" id="view-contenttypes-create" value="Add">
			</div>
		</div>
		<div class="row">
			<div class="eight columns">
			<p><?php echo Services::Language()->translate('Remove an existing Content Type. Note: items must first be deleted or moved to a different Content Type using the Batch Utility.'); ?></p>
				<select name="content_types" class="inputbox">
					<option value="">No selection</option>
					<option value="20">Content Type 1</option>
					<option value="21">Content Type 2</option>
				</select>
			</div>
			<div class="four columns">
				<input type="submit" class="submit button small" name="submit" id="view-contenttypes-delete" value="Remove">
			</div>
		</div>
	</form>
	</div>
</div>
