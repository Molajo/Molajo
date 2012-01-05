<?php
/**
 * @package     Molajo
 * @subpackage  Template
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<div class="header">
    <include:module title=header view=header wrap=header />
    <include:modules position=menu wrap=nav />
    <include:message />
</div>
<include:component class=section wrap=div />
<include:modules position=footer class=footer wrap=footer />