<?php
use Molajo\Service\Services;

/**
 * @package     Molajo
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<include:head/>
<include:template name="Adminheader" wrap="div" wrap_class="row"/>
<include:message wrap="div" wrap_class="row"/>
<div class="row">
	<nav class="one columns">
		<include:template name=Adminnavigationbar/>
	</nav>
	<section class="eleven columns">
		<include:request/>
	</section>
</div>
<div class="row">
	<div class="twelve columns">
		<include:template name=Adminfooter wrap=none/>
	</div>
</div>
<include:defer/>
