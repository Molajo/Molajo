<?php
/**
 * @package     Molajo
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<include:head/>
<div class="row">
    <div class="twelve columns">
        <include:template name=Adminheader/>
    </div>
</div>
<include:message wrap="div" wrap_class="row"/>
<div class="row">
    <div class="three columns">
        <include:template name=Adminnavigationbar/>
        <include:template name=Adminsectionmenu/>
    </div>
    <div class="nine columns">
        <include:template name=Adminresourcemenu/>
        <include:template name=Admingridfilters/>
        <include:request/>
        <include:template name=Admingridbatch/>
    </div>
</div>
<div class="row">
    <div class="twelve columns">
        <include:template name=Adminfooter/>
    </div>
</div>
<include:defer/>
