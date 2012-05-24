<?php
/**
 * @package     Molajo
 * @subpackage  Theme
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 *
 * <include:module name=Mainmenu wrap=Nav />
 * <include:module name=Sidebar template=Sidebar wrap=div/>
 */
defined('MOLAJO') or die; ?>
<include:head/>
<include:module name=Adminheader/>
<include:module name=Systemmessages/>
<include:request/>
<include:module name=Adminfooter/>
<include:defer/>
