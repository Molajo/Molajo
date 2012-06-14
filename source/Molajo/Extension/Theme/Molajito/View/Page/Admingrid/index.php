<?php
use Molajo\Service\Services;
/**
 * @package     Molajito
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
$row = 'http://localhost/molajo/source/admin/index.php?id=190'; ?>
<include:head/>
<include:template name="Adminheader" wrap="header" wrap_class="row"/>
<include:message wrap="div" wrap_class="row"/>
<section class="row">
	<nav class="four columns">
		<include:template name="Adminnavigationbar"/>
		<include:template name="Search"/>
	</nav>
	<div class="eight columns">
		<include:template name="Adminsidemenu"/>
		<include:template name="Admintoolbar" value="Admintoolbar"/><br />
		<include:template name="Admingridfilters" value="GridFilters"/><br />
		<include:request/>
	</div>
</section>
<footer class="row">
	<div class="four columns"></div>
	<include:template name="Adminfooter" wrap="div" wrap_class="eight,columns"/>
</footer>
<include:defer/>


