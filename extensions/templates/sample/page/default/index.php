<?php
/**
 * @package     Molajo
 * @subpackage  Template
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<div class="container">
    <jdoc:include type="modules" name="header" wrap="header" />
    <jdoc:include type="message" />
    <section>
        <jdoc:include type="modules" name="menu" wrap="none" />
        <jdoc:include type="component" />
    </section>
    <jdoc:include type="modules" name="footer" wrap="footer" />
</div>