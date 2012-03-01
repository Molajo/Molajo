<?php
/**
 * @package     Bootstrap
 * @subpackage  Page
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<include:head/>
<include:module name=page-header template=page-header wrap=header wrap_class=header />
<include:message/>
<include:request/>
<include:tag name=sidebar template=sidebar wrap=aside wrap_class=leftsidebar />
<include:module name=page-footer template=page-footer wrap=footer wrap_class="footer" />
<include:defer/>
