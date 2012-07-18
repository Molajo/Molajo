<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
use Molajo\Service\Services;

defined('MOLAJO') or die;

$action = Services::Registry()->get('Triggerdata', 'full_page_url');

?>

<dl class="tabs contained">
    <dd><a href="<?php echo $action; ?>#batchStatus" class="active">Status</a></dd>
    <dd><a href="<?php echo $action; ?>#batchCategories">Categories</a></dd>
    <dd><a href="<?php echo $action; ?>#batchTags">Tags</a></dd>
    <dd><a href="<?php echo $action; ?>#batchPermissions">Permissions</a></dd>
</dl>

<ul class="tabs-content contained">

    <li class="active" id="batchStatusTab">
        <form action="<?php echo $action; ?>" method="post" name="Admingridfilters">
            <p>Set the status of items selected above with the status specified below.
                <include:template name=Formselectlist model=Triggerdata value=<?php echo 'listbatch_status*'; ?>/>
                <input type="submit" class="submit button small" name="submit" id="action"
                       value="Apply">
            </p>
        </form>
    </li>

    <li id="batchCategoriesTab">
        <form action="<?php echo $action; ?>" method="post" name="Admingridfilters">
            <p>Associate (or disassociate) the items selected with the specified category(ies).
                <include:template name=Formselectlist model=Triggerdata value=<?php echo 'listbatch_categories*'; ?>/>
                <input type="submit" class="submit button small" name="submit" id="batch-category-create"
                       value="Add">
                <input type="submit" class="submit button small" name="submit" id="batch-category-delete"
                       value="Remove">
            </p>
        </form>
    </li>

    <li id="batchTagsTab">
        <form action="<?php echo $action; ?>" method="post" name="Admingridfilters">
            <p>Associate (or disassociate) the items selected with the specified tags(s).
                <include:template name=Formselectlist model=Triggerdata wrap=div wrap-class=filter
                                  value=<?php echo 'listbatch_tags*'; ?>/>
                <input type="text" name="tag" id="tag">
                <input type="submit" class="submit button small" name="submit" id="batch-tag-create"
                       value="Add">
                <input type="submit" class="submit button small" name="submit" id="batch-tag-delete"
                       value="Remove">
            </p>
        </form>
    </li>

    <li id="batchPermissionsTab">
        <form action="<?php echo $action; ?>" method="post" name="Admingridfilters">
            <p>Associate (or disassociate) the items selected with the specified group(s) and permission(s).</p>
            <include:template name=Formselectlist model=Triggerdata value=<?php echo 'listbatch_groups*'; ?>/>
            <ul class="permissions">
                <li>
                    <label for="permission-create">
                        <input name="radio1" type="radio" id="permission-create">
                        <span class="custom radio"></span> Create
                    </label>
                </li>
                <li>
                    <label for="permission-read">
                        <input name="radio1" type="radio" selected id="permission-read">
                        <span class="custom radio"></span> Read
                    </label>
                </li>
                <li>
                    <label for="permission-update">
                        <input name="radio1" type="radio" id="permission-update">
                        <span class="custom radio"></span> Update
                    </label>
                </li>
                <li>
                    <label for="permission-delete">
                        <input name="radio1" type="radio" id="permission-delete">
                        <span class="custom radio"></span> Delete
                    </label>
                </li>
                <li>
                    <label for="permission-all">
                        <input name="radio1" type="radio" id="permission-all">
                        <span class="custom radio"></span> All
                    </label>
                </li>
            </ul>

            <input type="submit" class="submit button small" name="submit" id="batch-permission-create"
                   value="Add">
            <input type="submit" class="submit button small" name="submit" id="batch-permission-delete"
                   value="Remove">

        </form>
    </li>
</ul>
