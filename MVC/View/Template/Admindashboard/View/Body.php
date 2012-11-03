<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<include:template name=formbegin form_name=dashboard/>
<include:ui name=navigationtab tab_array=<?php echo $this->row->tab_array; ?>/>
<include:template name=formend/>

<section class="dashboard">
    <div class="sortable">
        <br /><br /><br /><br />
        <h1>This will be the very last thing. It will pull from the section content.</h1>
        <?php //echo Services::Registry()->get('Plugindata', 'PortletOptions'); ?>
    </div>
</section>
