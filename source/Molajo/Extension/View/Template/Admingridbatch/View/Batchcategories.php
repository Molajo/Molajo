<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$action = Services::Registry()->get('Triggerdata', 'full_page_url'); ?>
<h4><?echo Services::Language()->translate('Categorize'); ?></h4>
<form action="<?php echo $action; ?>" method="post" name="Admingridbatchcategories" id="Admingridbatchcategories">
    <p><?php echo Services::Language()->translate('Add or remove selected content from specified category(ies).'); ?></p>
    <div class="row">
        <div class="five columns">
            <include:template name=Formselectlist model=Triggerdata value=<?php echo 'listbatch_categories*'; ?>/>
        </div>
        <div class="three columns">
            <input type="submit" class="submit button small" name="submit" id="batch-category-create" value="Add">
            <input type="submit" class="submit button small" name="submit" id="batch-category-delete" value="Remove">
        </div>
        <div class="four columns">&nbsp;</div>
    </div>
</form>
