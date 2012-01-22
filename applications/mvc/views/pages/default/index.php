<?php
/**
 * @package     Molajo
 * @subpackage  Page
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<include:head/>
<include:module name=header view=header wrap=header/>
<include:message/>
<include:module name=menu wrap=nav/>
<include:request/>
<include:module position=footer view=footer wrap=footer/>
<include:defer/>