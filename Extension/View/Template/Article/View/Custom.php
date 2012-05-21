<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 *
 * <include:module name=pullquote template=pullquote wrap=aside/>
 */
defined('MOLAJO') or die; ?>
<article>
	<include:wrap name=Header wrap_class=blue {{{<h2>The Title Goes Here</h2>}}} />

	<include:wrap name=Aside {{{This is aside text lorem ipsum dolor sit amet consectetur adipisicing}}} />

	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis <a href="#">nostrud exercitation</a> ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod <a href="#">tempor incididunt</a> ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit.</p>

	<include:wrap name=Footer {{{<h2>Posted August 22, 2011</h2>}}} />
</article>
