<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$action = Services::Registry()->get('Triggerdata', 'full_page_url');
?>
<h4>Permissions</h4>
<form action="<?php echo $action; ?>" method="post" name="Admingridbatchpermissions" id="Admingridbatchpermissions">
	<p><?php echo Services::Language()->translate('Permit or restrict specified permission for selected content from this group(s).'); ?></p>
	<div class="row">
		<div class="twelve columns">
			<ul class="permissions">
				<li>
					<label for="permission-create">
						<input name="radio1" type="radio" value="2" id="permission-create">
						<span class="custom radio"><?echo Services::Language()->translate('Create'); ?></span>
					</label>
				</li>
				<li>
					<label for="permission-read">
						<input name="radio1" type="radio" value="3" checked id="permission-read">
						<span class="custom radio"><?echo Services::Language()->translate('Read'); ?></span>
					</label>
				</li>
				<li>
					<label for="permission-update">
						<input name="radio1" type="radio" value="4" id="permission-update">
						<span class="custom radio"><?echo Services::Language()->translate('Update'); ?></span>
					</label>
				</li>
				<li>
					<label for="permission-publish">
						<input name="radio1" type="radio" value="5" id="permission-publish">
						<span class="custom radio"><?echo Services::Language()->translate('Update'); ?></span>
					</label>
				</li>
				<li>
					<label for="permission-delete">
						<input name="radio1" type="radio" value="6" id="permission-delete">
						<span class="custom radio"><?echo Services::Language()->translate('Delete'); ?></span>
					</label>
				</li>
				<li>
					<label for="permission-all">
						<input name="radio1" type="radio" value="7" id="permission-all">
						<span class="custom radio"><?echo Services::Language()->translate('Administer'); ?></span>
					</label>
				</li>
			</ul>
		</div>
	</div>
	<div class="row">
		<div class="nine columns">
			<include:template name=Formselectlist model=Triggerdata value=<?php echo 'listbatch_groups*'; ?>/>
		</div>
		<div class="three columns">
			<input type="submit" class="submit button small" name="submit" id="batch-permission-create" value="Add">
			<input type="submit" class="submit button small" name="submit" id="batch-permission-delete" value="Remove">
		</div>
	</div>
</form>
