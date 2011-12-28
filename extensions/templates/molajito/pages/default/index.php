<?php
/**
 * @package     Molajo
 * @subpackage  Template
 * @copyright   Copyright (C) 2012 Cristina Solana, Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<div class="container">
    <include:position name="header" wrap="header"/>
    <include:message/>
    <include:position name="menu" wrap="nav" id="launchpad"/>
    <section class="dash">
        <include:component/>
        <include:view/>
    </section>
    <include:position name="footer" wrap="footer"/>
</div>