<?php
use Molajo\Service\Services;
/**
 * 	<include:template name=Admintoolbar/>
 *
 * @package     Molajito
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<include:head/>
<include:template name="Adminheader" wrap="header" wrap_class="row"/>
<include:message wrap="div" wrap_class="row"/>
<section class="row">
	<nav class="three columns">
		<include:template name=Adminnavigationbar/>
		<include:template name=Adminsectionmenu/>
	</nav>
	<section class="nine columns">
		<include:template name=Admincomponentmenu/>
			<include:template name=Admingridfilters/>
			<include:request/>
	</section>
</section>
<footer class="row">
	<div class="three columns"></div>
	<div class="nine columns"><include:template name=Adminfooter wrap=none/></div>
</footer>
<include:defer/>
