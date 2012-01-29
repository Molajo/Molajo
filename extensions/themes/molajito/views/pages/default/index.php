<?php
/**
 * @package     Molajo
 * @subpackage  Theme
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 *
<include:module name=mainmenu wrap=nav />
 */
defined('MOLAJO') or die;
?>
<include:head/>
<include:message />
<include:module name=page-header view=page-header wrap=header />
<include:tag name=sidebar view=sidebar wrap=div/>
<include:request />
<include:module name=page-footer view=page-footer wrap=footer />
<include:defer />
