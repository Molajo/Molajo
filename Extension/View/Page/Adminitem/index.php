<?php
/**
 * @package     Molajo
 * @copyright   2012 Individual Molajo Contributors. All rights reserved.
 * @license     GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<div class="row">
    <div class="two columns hide-for-small">
        <include:template name=Adminsectionmenu/>
    </div>
    <div class="ten columns">
        <include:request/>
    </div>
</div>
<div class="row show-for-small">
    <div class="twelve columns">
        <include:template name=Adminsectionmenu/>
    </div>
</div>
