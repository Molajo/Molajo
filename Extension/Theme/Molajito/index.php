<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 * <include:profiler/>
 */
defined('MOLAJO') or die; ?>
<include:head/>
<section class="row wrapper">
    <div class="twelve columns">
        <header class="row header">
            <div class="twelve columns">
                <include:template name=Adminheader/>
            </div>
        </header>
        <section class="row main">
            <div class="twelve columns">
                <include:message/>
                <?php if (file_exists(Services::Registry()->get('Parameters', 'page_view_path_include'))) {
                include Services::Registry()->get('Parameters', 'page_view_path_include');
            } ?>
            </div>
        </section>
        <div class="row">
            <div class="push twelve columns"></div>
        </div>
    </div>
</section>
<footer class="row footer">
    <div class="twelve columns">
        <include:template name=Adminfooter wrap=none/>
    </div>
</footer>
<include:template name=modal/>
    <include:defer/>
