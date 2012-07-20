<?php
/**
 * @package     Molajo
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<div class="row">
    <div class="three columns">
        <include:template name=Adminnavigationbar/>
            <include:template name=Adminsectionmenu/>
    </div>
    <div class="nine columns">
        <include:template name=Adminresourcemenu/>
            <include:request/>
    </div>
</div>
