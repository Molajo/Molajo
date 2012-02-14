<?php
/**
 * @package     Molajo
 * @subpackage  Views
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
{{#items}}
<header>
	<h2>{{hello}}</h2>
</header>
<include:module name=pullquote template=pullquote wrap=aside />
{{content_text}}
<footer>
{{start_publishing_datetime}}
</footer>
{{/items}}
