<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
$action = Services::Registry()->get('Triggerdata', 'full_page_url'); ?>
<h4>Status</h4>
<form action="<?php echo $action; ?>" method="post" name="Admingridbatchstatus" id="Admingridbatchstatus">
	<p><?php echo Services::Language()->translate('Change the status of selected items to the specified value.'); ?></p>
	<div class="row">
		<div class="three columns">
			<include:template name=Formselectlist model=Triggerdata value=<?php echo 'listbatch_status*'; ?>/>
		</div>
		<div class="two columns">
			<input type="submit" class="submit button small" name="submit" id="action" value="Apply">
		</div>
		<div class="seven columns">&nbsp;</div>
	</div>
</form>
