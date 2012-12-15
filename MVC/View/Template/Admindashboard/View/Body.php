<?php
use Molajo\Service\Services;

/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('NIAMBIE') or die; ?>
<include:template name=formbegin form_name=dashboard/>
<include:ui name=navigationtab page_array=<?php echo $this->row->page_array; ?>/>
<include:template name=formend/>

<section class="dashboard">
    <div class="sortable">
        <br /><br /><br /><br />
        <h1>This will be the very last thing. It will pull from the section content.</h1>
        <?php //echo Services::Registry()->get('Plugindata', 'PortletOptions'); ?>
    </div>
</section>
