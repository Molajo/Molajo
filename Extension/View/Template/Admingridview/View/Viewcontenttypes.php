<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$action = Services::Registry()->get('Triggerdata', 'full_page_url'); ?>
<h4><?echo Services::Language()->translate('Resource Content Types'); ?></h4>
<form action="<?php echo $action; ?>" method="post" name="Admingridviewcontenttypes" id="Admingridviewcontenttypes">
	<p><?php echo Services::Language()->translate('Create a new or remove an existing Content Type. Before removing a Content Type. all items in the Content Type must first be deleted or moved to a different Content Type using the Batch Utility.'); ?></p>
	<div class="row">
		<div class="five columns">
			<include:template name=Formselectlist model=Triggerdata value=<?php echo 'listview_contenttypes*'; ?>/>
		</div>
		<div class="three columns">
			<input type="submit" class="submit button small" name="submit" id="view-contenttypes-create" value="Add">
			<input type="submit" class="submit button small" name="submit" id="view-contenttypes-delete" value="Remove">
		</div>
		<div class="four columns">&nbsp;</div>
	</div>
</form>
