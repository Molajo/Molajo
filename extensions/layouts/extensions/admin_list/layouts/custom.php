<?php
/**
 * @package     Molajo
 * @subpackage  List Manager
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<div class="container">
    <div class="titlerow">
        <div class="title">
            <jdoc:include type="modules" name="title" wrap="none" />
        </div>
        <div class="toolbar">
            <jdoc:include type="modules" name="toolbar" wrap="none" />
        </div>
    </div>
    <jdoc:include type="modules" name="submenu" wrap="nav" />
    <section>
        <jdoc:include type="message" />
        <jdoc:include type="modules" name="filters" wrap="none" />
        <jdoc:include type="modules" name="grid" wrap="div" />
        <jdoc:include type="modules" name="gridbatch" wrap="div" />
    </section>
</div>