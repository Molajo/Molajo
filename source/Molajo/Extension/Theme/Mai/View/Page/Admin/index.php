<?php
/**
 * @package     Molajo
 * @copyright   2012 Babs Gösgens. All rights reserved.
 * @license     GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>

	<nav role="navigation">
		<include:template name=Adminnavigationbar/>
	</nav>

	<section role="main" id="focus">
		<include:message/>

		<a href="<?php echo $_baseUri ?>#focus" id="expander"><span>Expand working area</span></a>
		<include:request/>
	</section>

