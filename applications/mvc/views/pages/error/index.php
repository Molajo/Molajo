<?php
/**
 * @package     Molajo
 * @subpackage  Page
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 *
 * <include:module name=logon view=logon wrap=div/>
 */
defined('MOLAJO') or die; ?>
<include:head/>
<include:module name=header view=header wrap=header/>
<include:message view=error />
<include:module position=footer view=footer wrap=footer/>
<include:defer/>
