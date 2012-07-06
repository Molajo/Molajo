<?php
use Molajo\Service\Services;
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
<include:request/>
<include:tag name=sidebar template=sidebar wrap=aside wrap_class=leftsidebar/>
<div class="row">
	<div class="twelve columns">
		<include:template name=Adminfooter/>
	</div>
</div>
<include:defer/>
