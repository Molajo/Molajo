<?php
use Molajo\Service\Services;
/**
* @package     Molajito
* @copyright   2012 Amy Stephen. All rights reserved.
* @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
*/
defined('MOLAJO') or die; ?>
<include:head/>
<include:template name=Adminheader wrap=Header wrap_class=banner-wrap/>
<include:message/>
<include:template name=Admintoolbar wrap=Section value=AdminToolbar/>
<include:template name=Adminsubmenu wrap=Section value=AdminSubmenu/>
<include:template name=Admingridfilters wrap=Section value=GridFilters/>
<include:request/>
<include:template name=Admingridpagination wrap=Nav wrap_class=page value=GridPagination/>
<include:template name=Admingridbatch wrap=Section value=GridBatch/>
<include:template name=Adminfooter wrap=Footer wrap_class=row,footer/>
<include:defer/>
