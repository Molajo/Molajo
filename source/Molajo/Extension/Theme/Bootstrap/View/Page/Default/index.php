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
<include:module name=Adminheader display_view_on_no_results=1/>
<include:message/>
<include:request template_class=red non_standard_attributes=ignored/>
<include:module name=Adminfooter/>
<include:defer/>
