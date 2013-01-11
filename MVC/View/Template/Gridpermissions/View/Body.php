<?php
/**
 * Gridpermissions Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('NIAMBIE') or die;
$action = Services::Registry()->get(PAGE_LITERAL, 'page_url');
if ($this->row->enable == 1) {
?>
<div class="grid-permissions grid-batch">
    <ol class="grid-batch">
        <li><input type="submit" class="submit button small radius" name="submit" id="AssignPermissions" value="Assign">
        </li>
        <li><strong><?php echo Services::Language()->translate('or'); ?></strong></li>
        <li><input type="submit" class="submit button small radius" name="submit" id="RemovePermissions" value="Remove">
        </li>
        <li><input id="create" type="radio" value="create" name="permission"><label
                for="create"><?php echo Services::Language()->translate('Create'); ?></label></li>
        <li><input id="read" type="radio" value="read" name="permission"><label
                for="read"><?php echo Services::Language()->translate('Read'); ?></label></li>
        <li><input id="update" type="radio" value="update" name="permission"><label
                for="update"><?php echo Services::Language()->translate('Update'); ?></label></li>
        <li><input id="publish" type="radio" value="publish" name="permission"><label
                for="publish"><?php echo Services::Language()->translate('Publish'); ?></label></li>
        <li><input id="delete" type="radio" value="delete" name="permission"><label
                for="delete"><?php echo Services::Language()->translate('Delete'); ?></label></li>
        <li>
            <include:template name=formselectlist datalist=groups/>
        </li>
        <li><strong><?php echo Services::Language()->translate('Permissions'); ?></strong></li>
    </ol>
</div>
<?php }
