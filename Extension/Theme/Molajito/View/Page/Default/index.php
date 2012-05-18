<?php
/**
 * @package   Molajo
 * @subpackage  Theme
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 *
 */
defined('MOLAJO') or die; ?>
<include:head/>
<include:module name=PageHeader template=PageHeader wrap=Head/>
<include:message/>
<include:module name=Mainmenu wrap=Nav/>
<include:tag name=Sidebar template=Sidebar wrap=Div/>
<include:request/>
<include:module name=PageFooter template=PageFooter wrap=Footer/>
<include:defer/>
