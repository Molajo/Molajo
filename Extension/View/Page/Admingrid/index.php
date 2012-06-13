<?php
use Molajo\Service\Services;

/**
 * @package     Molajito
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<include:head/>
<include:template name=Adminheader wrap=None/>
<include:message/>
<include:template name=Adminnavigationbar wrap=None Value=GridBatch/>
<include:template name=Adminsubmenu wrap=None value=AdminSubmenu/><br />
<include:template name=Admintoolbar wrap=None value=Admintoolbar/><br />
<include:template name=Admingridfilters wrap=None value=GridFilters/><br />
<include:template name=Adminsidemenu wrap=Nav value=GridBatch/>
<include:request/>
<include:template name=Admingridpagination wrap=None value=GridPagination/>
<include:template name=Admingridbatch wrap=None value=GridBatch/>
<include:template name=Adminfooter wrap=Footer wrap_class=row,footer/>
<include:defer/>
