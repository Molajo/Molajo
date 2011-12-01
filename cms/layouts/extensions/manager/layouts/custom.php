<?php
/**
 * @package     Molajo
 * @subpackage  List Manager
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<div class="container">
    <div class="titlerow">
        <div class="title">
            <doc:include type="modules" name="title" wrap="none" id="thisid" class="thisclass"/>
        </div>
        <div class="toolbar">
            <doc:include type="modules" name="toolbar" wrap="none"/>
        </div>
    </div>
    <doc:include type="modules" name="submenu" wrap="nav"/>
    <section>
        <doc:include type="message"/>
        <doc:include type="modules" name="filters" wrap="none"/>
        <doc:include type="modules" name="grid" wrap="div"/>
        <doc:include type="modules" name="gridbatch" wrap="div"/>
    </section>
</div>