<?php
/**
 * @package     Molajo
 * @subpackage  Template
 * @copyright   Copyright (C) 2011 Cristina Solana, Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<div class="container">
    <div class="titlerow">
        <div class="title">
            <doc:include type="modules" name="title" wrap="none"/>
        </div>
        <div class="toolbar">
            <doc:include type="modules" name="toolbar" wrap="none"/>
        </div>
    </div>
    <doc:include type="modules" name="submenu" wrap="nav"/>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Content</a></li>
            <li><a href="#tabs-2">Access</a></li>
            <li><a href="#tabs-3">Properties</a></li>
            <li><a href="#tabs-4">Custom Fields</a></li>
            <li><a href="#tabs-4">Metadata</a></li>
        </ul>
        <doc:include type="modules" name="edit-form" wrap="div" id="tabs-1"/>
        <doc:include type="modules" name="acl-widget" wrap="div" id="tabs-2"/>
        <doc:include type="modules" name="edit-properties" wrap="div" id="tabs-3"/>
        <doc:include type="modules" name="edit-custom-fields" wrap="div" id="tabs-4"/>
        <doc:include type="modules" name="edit-meta" wrap="div" id="tabs-4"/>
    </div>
</div>