<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$action = Services::Registry()->get('Plugindata', 'full_page_url'); ?>
<h4><?echo Services::Language()->translate('Content Types Assignment'); ?></h4>
<form action="<?php echo $action; ?>" method="get" name="Admingridbatchcontenttypes" id="Admingridbatchcontenttypes">
	<p><?php echo Services::Language()->translate('Add (or remove) selected content from specified content type.'); ?></p>
	<div class="row">
		<div class="nine columns">
			<include:template name=Formselectlist model=Plugindata value=<?php echo 'listbatch_contenttypes*'; ?>/>
		</div>
		<div class="three columns">
			<input type="submit" class="submit button small" name="submit" id="batch-contenttype-create" value="Add">
			<input type="submit" class="submit button small" name="submit" id="batch-contenttype-delete" value="Remove">
		</div>
	</div>
</form>
