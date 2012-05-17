<?php
/**
 * @package     Molajo
 * @subpackage  Theme
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 * <include:module name=Mainmenu wrap=Nav />
 * <include:module name=Sidebar template=Sidebar wrap=div/>
 */
defined('MOLAJO') or die; ?>
<include:module name=Pageheader template=Pageheader wrap=Head/>
<include:message/>

<include:head/>

<include:request/>
<include:module name=Pagefooter template=Pagefooter wrap=Footer/>
<include:defer/>
