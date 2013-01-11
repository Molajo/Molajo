<?php
/**
 * Admindashboard Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('NIAMBIE') or die;
?>
<include:template name=formbegin form_name=dashboard/>
    <include:ui name=navigationtab page_array=<?php echo $this->row->page_array; ?>/>
    <include:template name=formend/>

        <section class="dashboard">
            <div class="sortable">
                <br/><br/><br/><br/>

                <h1>This will be the very last thing. It will pull from the section content.</h1>
                <?php //echo Services::Registry()->get('Plugindata', 'PortletOptions'); ?>
            </div>
        </section>
