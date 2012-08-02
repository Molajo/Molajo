<?php
use Molajo\Service\Services;
/**
 * @package     Molajo
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
?>

<form>

    <dl class="contained tabs">
        <dd class="active"><a href="<?php echo Services::Registry()->get('Triggerdata', 'full_page_url'); ?>#edit">Edit</a></dd>
        <dd><a href="<?php echo Services::Registry()->get('Triggerdata', 'full_page_url'); ?>#options">Options</a></dd>
        <dd><a href="<?php echo Services::Registry()->get('Triggerdata', 'full_page_url'); ?>#fields">Fields</a></dd>
    </dl>
    <ul class="contained tabs-content">
        <li class="active" id="editTab">
            <a href="<?php echo $this->query_results[0]->catalog_id_url ?>" class="button">Preview</a>
            <include:template name=Editbuttons/>
            <include:template name=Edititem/>
        </li>
        <li id="optionsTab">
            <a href="<?php echo $this->query_results[0]->catalog_id_url ?>" class="button">Preview</a>
            <include:template name=Editoptions/>
        </li>
        <li id="fieldsTab">
            <a href="<?php echo $this->query_results[0]->catalog_id_url ?>" class="button">Preview</a>
            <include:template name=Editfields/>
        </li>
    </ul>

</form>
