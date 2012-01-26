<?php
/**
 * @package     Molajo
 * @subpackage  Template
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 *
<include:message view=messages wrap=div />
<include:modules position=menu wrap=nav />
<include:head/>
<include:defer/>
 *
 *
 */
defined('MOLAJO') or die;
?>
<include:module position=header view=page-header wrap=div/>
<include:request/>
<include:module position=footer view=page-footer wrap=div/>
