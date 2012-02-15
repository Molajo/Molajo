<?php
/**
 * @package     Molajo
 * @subpackage  Views
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<include:module name=admin-toolbar template=admin-toolbar wrap=header />
<include:module name=admin-submenu template=admin-submenu wrap=menu />
<include:module name=grid-filters template=grid-filters wrap=menu />
<include:module name=grid-table template=grid-table wrap=section />
<include:module name=grid-pagination template=grid-pagination wrap=menu />
<include:module name=grid-batch template=grid-batch wrap=menu />
