<?php
/**
 * @package     Molajo
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<div class="row">
    <div class="two columns">
        <include:template name=Adminsectionmenu/>
    </div>
    <div class="ten columns">
		<include:template name=Preferences/>
        <include:request/>
    </div>
</div>
