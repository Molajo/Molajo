<?php
use Molajo\Service\Services;
/**
 * @package     Molajo
 * @copyright   2012 Babs Gösgens. All rights reserved.
 * @license     GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<div class="row">
	<div class="two columns">
		<include:template name=Adminnavigationbar/>
		<include:template name=Adminsectionmenu/>
	</div>
	<div class="ten columns">
		<include:request/>
	</div>
</div>

			<include:template name=Adminsectionmenu/>
			<include:template name=Adminresourcemenu/>
