<?php
/**
 * @package     Molajo
 * @subpackage  Template
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 *
<include:module name=mainmenu wrap=nav />
<include:head/>
<include:tag name=sidebar view=sidebar wrap=div/>
<include:defer/>
 *
 *
 *
 */
defined('MOLAJO') or die;
?>
<include:message view=messages wrap=div />
<include:module name=page-header view=page-header wrap=section />
<include:request wrap=section />
<include:module name=page-footer view=page-footer wrap=div />
