<?php
use Molajo\Service\Services;
/**
 * @package     Molajito
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<include:head/>
<include:template name="Adminheader" wrap="header" wrap_class="row"/>
<include:message wrap="div" wrap_class="row"/>
<section class="row">
	<nav class="four columns">
		<include:template name=Adminnavigationbar/>
		<include:template name=Adminsidemenu/>
		<include:template name=Adminsubmenu/>
		<include:template name=Adminsectionmenu/>
	</nav>
	<section class="eight columns">
		<include:template name=Admintoolbar/>
		<include:template name=Admingridfilters/>
		<include:template name=Search/>
		<include:request/>
		<include:template name=Admingridbatch/>
	</section>
</section>
<footer class="row">
	<div class="four columns"></div>
	<include:template name=Adminfooter wrap=div wrap_class=eight,columns/>
</footer>
<include:defer/>
