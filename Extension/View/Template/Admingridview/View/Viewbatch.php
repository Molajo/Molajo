<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
$pageURL = Services::Registry()->get('Triggerdata', 'full_page_url'); ?>
<h4><?echo Services::Language()->translate('Tag'); ?></h4>
<form action="<?php echo $pageURL; ?>" method="post" name="Admingridviewtags" id="Admingridviewtags">
	<p><?php echo Services::Language()
		->translate('Add or remove selected content from specified tag(s). New tags entered as comma delimited text will be created.'); ?>
	</p>
	<div class="row">
		<div class="nine columns">
			<input type="text" name="tag" id="tag"><br />
			<include:template name=Formselectlist model=Triggerdata value=<?php echo 'listbatch_tags*'; ?>/>
		</div>
		<div class="three columns">
			<input type="submit" class="submit button small" name="submit" id="batch-tag-create" value="Add">
			<input type="submit" class="submit button small" name="submit" id="batch-tag-delete" value="Remove">
		</div>
	</div>
</form>
