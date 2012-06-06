<?php
/**
 * @package     Molajo
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<include:head/>
<include:module name=Adminheader/>
<include:module name=Systemmessages/>
<include:template name=Admintoolbar wrap=Section value=AdminToolbar/>
<include:template name=Adminsubmenu wrap=Section value=AdminSubmenu/>
<include:template name=Admingridfilters wrap=Section value=GridFilters/>
<include:request/>
<include:template name=Admingridpagination wrap=Section value=GridPagination/>
<include:template name=Admingridbatch wrap=Section value=GridBatch/>
<include:module name=Adminfooter/>
<include:defer/>
