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

	<section role="main">
		<include:message/>

		<a href="<?php echo $_baseUri ?>#focus" id="expander"><span>Expand working area</span></a>
		<include:request/>
	</section>


<!-- <div class="row">
	<div class="two columns">
		<include:template name=Adminnavigationbar/>
		<include:template name=Adminsectionmenu/>
	</div>
	<div class="ten columns">
		<include:request/>
	</div>
</div> -->

