<?php
/**
 * @package     Molajo
 * @subpackage  Launchpad
 * @copyright   Copyright (C) 2011 Amy Stephen, Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<nav id="launchpad">
    <ul class="menu_main">
        <li><a href="#" class="icon_configure"></a></li>
        <li><a href="#" class="icon_access"></a></li>
        <li><a href="#" class="icon_create"></a></li>
        <li><a href="#" class="icon_build"></a></li>
        <li><a href="#" class="icon_search"></a></li>
    </ul>
    <!-- TODO: Get rid of the jUI icons, possibly add east facing arrowhead on hover/active -->
    <div id="accordion" class="menu_sub">
        <div>
            <h2><a href="#">Articles</a></h2>
            <ul>
                <li><a href="#">Add</a></li>
                <li><a href="#">Edit</a></li>
                <li><a href="#">Whatever</a></li>
            </ul>
        </div>
        <div>
            <h2><a href="#">Media</a></h2>
            <ul>
                <li><a href="#">Add</a></li>
                <li><a href="#">Edit</a></li>
                <li><a href="#">Etcetera</a></li>
            </ul>
        </div>
        <div>
            <h2><a href="#">Other Com</a></h2>
            <ul>
                <li><a href="#">Add</a></li>
                <li><a href="#">Edit</a></li>
                <li><a href="#">Like you know better</a></li>
            </ul>
        </div>
    </div>
</nav>