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
    <doc:include type="modules" name="header" wrap="header" />
    <doc:include type="message" />
    <doc:include type="modules" name="menu" wrap="nav" id="launchpad" />
    <section class="dash">
        <div id="">
            <doc:include type="component" />
        </div>
    </section>
    <doc:include type="modules" name="footer" wrap="footer" />
</div>