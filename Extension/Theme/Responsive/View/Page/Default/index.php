<?php
/**
 * @package     Molajo
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<include:head/>
<include:module name=PageHeader template=Header wrap=Header wrap_class="span16"/>
<include:message/>
<body class="cf">
<div id="container" class="cf">
<include:request template="section" wrap="article" wrap_class="span11"/>
<include:module name="ArticleList" template="aside" wrap="aside" wrap_class="span5 col"/>
</div> <!--! end of #container -->
<include:module name=PageFooter template=PageFooter wrap=Footer/>
<include:defer/>
