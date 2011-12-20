<?php
/**
 * @package     Molajo
 * @subpackage  Template
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<div class="container">
    <doc:include type="modules" name="header" wrap="header" />
    <doc:include type="message" />
    <section>
        <doc:include type="modules" name="menu" wrap="none" />
        <doc:include type="component" />
    </section>
    <doc:include type="modules" name="footer" wrap="footer" />
</div>