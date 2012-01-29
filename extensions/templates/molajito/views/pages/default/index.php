<?php
/**
 * @package     Molajo
 * @subpackage  Template
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 *
<include:module name=mainmenu wrap=nav />
<include:tag name=sidebar view=sidebar wrap=div/>
<include:component name=sidebar view=sidebar wrap=div/>
 *
 *
 *
 */
defined('MOLAJO') or die;
?>
<include:head/>
<include:message />
<include:module name=page-header view=page-header wrap=header />
<include:request />
<include:module name=page-footer view=page-footer wrap=footer />
<include:defer />
