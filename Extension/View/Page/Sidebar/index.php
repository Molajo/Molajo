<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see license folder
 */
defined('MOLAJO') or die; ?>
<include:head/>
<include:module name=PageHeader template=PageHeader wrap=Head wrap_class=header/>
<include:message/>
<include:request/>
<include:tag name=sidebar template=sidebar wrap=aside wrap_class=leftsidebar/>
<include:module name=PageFooter template=PageFooter wrap=Footer wrap_class="footer"/>
<include:defer/>
