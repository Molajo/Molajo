<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Babs Gösgens. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 * <include:profiler/>
 */
defined('MOLAJO') or die; ?>
<include:head/>

    <div class="row" id="focus">
    <?php if (file_exists(Services::Registry()->get('Parameters', 'page_view_path_include'))) {
        include Services::Registry()->get('Parameters', 'page_view_path_include');
    } ?>
    </div>

    <footer>
        <include:template name=Adminfooter wrap=none/>
    </footer>
<include:template name=modal/>
<include:defer/>
<include:template name=Scripts/>
<?php //include('_scripts.php')
